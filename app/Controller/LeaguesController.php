<?php

class LeaguesController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Session'];
	
	public function index() {
		// get all leagues, number of members
		
		// if post data then administrator has processed a new league app
		if ($this->request->is('post')) {
			$update_cnt = 0;
			$from = [MAIL_SENDER => APP_NAME];
			foreach ($this->request->data['Manage'] as $k=>$v) {
				$l = $this->League->find('first', [
					'fields' => ['ID', 'name'],
					'conditions' => ['League.id' => $k],
					'contain' => [
						'User' => [
							'fields' => ['id','username','email']
						]
					]
				]);
				if ($v == 'a') {
					if ($this->League->save(['League' => ['id' => $k, 'confirmed' => 1]])) {
						$this->log('League ' . $k . ' approved', 'league');
						// need to email the organiser, and add him/her to the league
						$update_cnt++;
						// save the league admin as a league user
						$this->League->LeagueUser->save([
							'LeagueUser' => [
								'league_id' => $k,
								'user_id' => $l['User']['id'],
								'confirmed' => 1
							]
						]);
						$email = new CakeEmail('default');
						$email->from($from)
									->to($l['User']['email'])
									->subject('GoalMine league request')
									->template('league_create_accept')
									->viewVars(['user' => $l['User']['username'],'league' => $l['League']['name'],'id' => $l['League']['ID']])
									->send();
					}
				} elseif ($v == 'r') {
					if ($this->League->delete($k, false)) {
						$update_cnt++;
						$this->log('League ' . $k . ' rejected', 'league');
						$email = new CakeEmail('default');
						$email->from($from)
									->to($l['User']['email'])
									->subject('GoalMine league request')
									->template('league_create_reject')
									->viewVars(['user'=>$l['User']['username'],'league'=>$l['League']['name']])
									->send();
					}
				}
			}
			if ($update_cnt>0) $response = $update_cnt . ' league(s) processed';
			$this->Session->setFlash($response, 'custom-flash', ['class'=>'alert-box success']);
		}
		
		$this->set('leagues', $this->League->LeagueUser->find('all', [
			'fields' => ['League.name', 'League.id', 'League.type', 'count(LeagueUser.user_id) as cnt'],
			'conditions' => ['LeagueUser.confirmed' => 1, '1=1 GROUP BY League.name'],
		]));
		
		// if admin, get all pending leagues
		$this->set('pending', $this->League->find('all', [
			'fields' => ['name', 'id', 'admin', 'description'],
			'conditions' => ['confirmed' => 0],
			'contain' => 'User.username'
		]));
	}
	
	public function view($id = null) {
		$this->League->id = $id;
		if ($this->request->is('get')) {
			$this->set('league', $this->League->read());
			$db = $this->League->User->Standing->getDataSource();
			$sql = 'SELECT 
						U.id,
						username,
						SUM(points) AS pts
						FROM standings S INNER JOIN users U ON S.user_id = U.id
						INNER JOIN league_users LU ON LU.user_id = U.id
						INNER JOIN leagues L ON LU.league_id = L.id
						WHERE week_id > 100 AND L.id = ? AND LU.confirmed = 1 GROUP BY U.id ORDER BY pts DESC';
			$this->set('standings', $db->fetchAll($sql, [$id]));
			$this->set('pending',$db->fetchAll('SELECT COUNT(U.id) AS cnt FROM users U INNER JOIN league_users L ON U.ID=L.user_id WHERE league_id=? AND confirmed=0', [$id]));
		}
	}
	
	public function manage($id = null) {
		$response = '';
		$update_cnt = 0;
		$this->League->id = $id;
		if ($this->request->is('post')) {
			$response = $this->League->processInvites($this->request->data);
			$this->Session->setFlash($response, 'default', ['class' => 'alert alert-box success']);
		}
		$league = $this->League->find('first', [
				'conditions' => ['id' =>$id],
				'contain' => [
					'LeagueUser' => [
						'User' => [
							'fields' => ['id', 'username']
						]
					]
				]
			]
		);

		if ($league) {
			$this->set('league', $league);
		} else {
			$this->flash(__('That league does not exist'), '/leagues/', 2);
		}
		
	} // end manage
	
	public function apply($league = null) {
		// process an application to join a league
		// params - id of league

		$user = $this->Auth->user();

		// pass to processApply method of model
		if ($user && $league) {
			$response = $this->League->processApply($user, $league);	
		} else {
			throw new MethodNotAllowedException();
		}

		// set message to response from model method
		$this->Session->setFlash($response['msg'], 'custom-flash', ['class' => 'alert-box ' . $response['css']]);
		// no view needed for this so redirect to leagues index page
		$this->redirect(['action' => 'index']);

	} // end change

	public function add() {
		// get name of new league
		
		// on submit, pass new league details back to model
		if ($this->request->is('post')) {
			if ($this->League->save($this->request->data)) {
				$response = 'Your request for a new league has been sent';
				$class = 'alert alert-box success';
				$from = [MAIL_SENDER => APP_NAME];
				$email = new CakeEmail('default');
				$email->from($from)
							->to('nick@mantonbradbury.com')
							->subject('GoalMine pending action')
							->template('league_create')
							->viewVars(['user_id' => $this->request->data['League']['organiser_name'],'league' => $this->request->data['League']['name'],'desc' => $this->request->data['League']['description']])
							->send();
			} else {
				$response = 'Unable to add your league at this time';
				$class = 'alert alert-box warning';
			}
		$this->Session->setFlash($response, 'custom-flash', ['myclass' => $class]);
		$this->redirect(['action'=>'index']);
		}
	}
	
	public function leave($id = null) {
		$user = $this->Auth->user();
		$luid = $this->League->LeagueUser->find('first', [
			'fields' => 'id',
			'conditions' => ['league_id' => $id, 'user_id' => $user['id']]
		]);
		if ($this->League->LeagueUser->delete($luid['LeagueUser']['id'])) {
			$this->log(__('%s has left league %s', $user['username'], $id));
			$this->Session->setFlash('You have now left this league', 'custom-flash', ['myclass'=>'alert alert-notice']);
			$this->redirect(['controller' => 'users', 'action' => 'view', $user['id']]);
		}
	}

} // end LeagueController