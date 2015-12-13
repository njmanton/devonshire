<?php

App::uses('CakeEmail', 'Network/Email');

class League extends AppModel {
	public $name = 'League';
	public $hasMany = ['LeagueUser'];
	// the admin field in the leagues table is foreign key to users table
	// and id of the league admin
	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'admin'
		]
	];
	
	public $actsAs = ['Containable'];
		
	public function processApply($user, $league_id) {
		// process an application to join a league
		// params - $user (user), $league (id of league applied for)
		
		$this->id = $league_id;
		$league = $this->read();
		
		if ($league['League']['type'] == 0) {
			// not a public league, so email organiser and set pending
			
			// email to organiser
			$admin = $this->LeagueUser->User->find('first', [
				'recursive' => 0,
				'fields' => ['username', 'email'],
				'conditions' => ['id' => $league['League']['admin']]
			]);
			
			// set up email template to admin
			$email = new CakeEmail('default');
			$from = [MAIL_SENDER => APP_NAME];
			$email->from($from)
						->to($admin['User']['email'])
						->subject('GoalMine pending action')
						->template('league_join')
						->viewVars(['organiser' => $admin['User']['username'],'user_id' => $user['id'], 'league' => $league['League']['name'], 'league_id' => $league['League']['id']])
						->send();
			
			// set pending_league_id for user
			$arr = ['LeagueUser' =>  ['user_id' => $user['id'], 'league_id' => $league_id, 'pending' => 1]];
			if ($this->LeagueUser->save($arr)) {
				$this->log(__('%s has requested to join %s', $user['id'], $league['League']['name']));
				$response['msg'] = __('Thank you, your request to join %s has been processed, and the league organiser informed', $league['League']['name']);
				$response['css'] = 'success';
			} else {
				$this->log(__('request by %s to join %s couldn\'t be processed', $league['League']['name']));
				$response['msg'] = 'Sorry, couldn\'t process your league membership. Please try again later';
				$response['css'] = 'warning';
			}
					
		} else {
		// public league, so just update leagueuser record
			$arr = ['LeagueUser' => ['user_id'=>$user_id, 'league_id'=>$league_id]];
			if ($this->LeagueUser->save($arr)) {
				$response['msg'] = __('You are now a member of %s, you may need to re-login to see the changes', $league['League']['name']);
				$response['css'] = 'success';
				$this->log($user_id . ' has joined public league ' . $league['League']['name'], 'league');
			} else {
				$this->log(__('request by %s to join %s couldn\'t be processed', $league['League']['name']));
				$response['msg'] = 'Sorry, couldn\'t process your league membership. Please try again later';
				$response['css'] = 'warning';
			}
		}
		
		return $response;
		
	} // end procesChange
	
	public function processNewLeague($data) {
	// $data = user, new league name
	
	// email to administrator
	// create league, set to pending, with organiser = user
	
	} // end processnewLeague
	
	public function processInvites($data) {
		// $data = user, new league, decision
		
		$response = '';
		$update_cnt = 0;
		// foreach set, do appropriate change to user record
		// email decision to user
		if ($this->save($data)) {
			$response .= 'description updated ';
		}
		if (!empty($data['Manage'])) {
			foreach ($data['Manage'] as $k=>$v) {
				$from = [MAIL_SENDER => APP_NAME];
				$request = $this->User->find('first', [
					'fields' => ['username','email'],
					'conditions' => ['id' => $data['Request']['id']],
					'recursive' => 0
				]);
				if ($v=='a') {
					// accept
					if ($this->LeagueUser->save(['LeagueUser' => ['id' => $k, 'confirmed' => 1]])) {
						$update_cnt++;
						
						$email = new CakeEmail('default');
						$email->from($from)
									->to($request['User']['email'])
									->subject('GoalMine league request')
									->template('league_join_accept')
									->viewVars(['user' => $request['User']['username'], 'league' => $data['League']['name'], 'id' => $data['League']['id']])
									->send();
					}
				} elseif ($v=='r') {
					// reject
					if ($this->LeagueUser->delete($k, false)) {
						$update_cnt++;
						$email = new CakeEmail('default');
						$email->from($from)
									->to($request['User']['email'])
									->subject('GoalMine league request')
									->template('league_join_reject')
									->viewVars(['user' => $request['User']['username'], 'league' => $data['League']['name']])
									->send();
					}
				}
			}
			if ($update_cnt > 0) {
				$response .= __('%s | user(s) processed', $update_cnt);
			}
		}
	
		return $response;
	
	} // end processInvites

	// auth stuff below
	public $validate = [
		'name' => ['rule' => 'notempty'],
		'public' => ['rule' => 'notempty']
	];
	
}