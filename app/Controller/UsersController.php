<?php

class UsersController extends AppController {
	public $name = 'Users';
	public $helpers = ['Html', 'Form'];

	public function view($id = null) {
		// view function. Displays a particular user
		// args: $id - user id

		if (isset($this->request->params['id'])) {
			$uid = $this->request->params['id'];
		} elseif ($id) {
			$uid = $id;
		}
		// TODO - check what fields are included to account for all games played
		$arr = [
			'conditions' => ['id' => $uid],
			'fields' => ['username', 'games'],
			'contain' => [
				'Standing' => [
					'order' => 'week_id ASC',
					'Week' => ['fields' => 'status'],
					'conditions' => __('week_id >= %s', START_WEEK)
				]
			]
		];

		$s = $this->User->find('first', $arr);
		$b = $this->User->Bet->userBets($uid);
		
		ksort($b);

		if (is_null($uid) || empty($s)) {
			$this->flash(__('Invalid user, or no user selected'), $this->referer, 2);
		} else {
			$this->set('selecteduser', $s);
			$this->set('bets', $b);
		}
	} // end view

	public function add() {
		// create a new user (admin-only function)

		if ($this->request->is('post')) {
			debug($this->request->data);
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'), 'custom-flash', ['myclass' => 'alert-box success']);
				//$this->redirect(['action' => 'index']);
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please try again.'), 'custom-flash', ['myclass' => 'alert-box warning']);
			}
		}
	} // end add

	public function update() {
		/*********************************************************
		name:					update
		description:	displays users' details, allowing them to
									make changes
		arguments:		none
		*********************************************************/
		
		// TODO check that relevant info for each game is pulled into view
		$user = $this->User->find('first', ['conditions' => ['id' => $this->Auth->user('id')], 'recursive' => 0]);

		if (empty($user)) {
			$this->redirect(['controller' => 'users', 'action' => 'index']);
		}

		if ($this->request->is('post')) {
			$data = $this->request->data;
			$response = $this->User->processUpdate($data['UserDetails'], $user['User']['password']);
			$this->Session->setFlash($response[1], 'custom-flash', ['myclass' => $response[0]]);
		}

		$arr = [
			'conditions' => ['id' => $user['User']['id']],
			'recursive' => 0,
			'fields' => ['id', 'username', 'email', 'password', 'preferences']
		];

		$this->set('data', $this->User->find('first', $arr));

	} // end update

	public function send() {

		// kick out non-admins
		if (!$this->Auth->user('admin')) {
			$this->flash(__('You must be an admin to send emails to users'), $this->referer, 2);
		}

		if ($this->request->is('post')) {
			// form has been submitted
			$response = $this->User->processSend($this->request->data);
			if (is_null($response)) {
				$this->Session->setFlash(__('There may have been a problem in sending that email'), 'custom-flash', ['myclass' => 'alert-box warning']);
			} else {
				$this->Session->setFlash(__('email sent to %s user(s)', $response), 'custom-flash', ['myclass' => 'alert-box success']);
			}
		}

	} // end send

	public function edit($id = null) {
		// new controller action, so admins can change registered games


	} // end edit

	// authentication related functions and members below

	public function beforeFilter() {
		// callback to allow certain views without logging in

		parent::beforeFilter();
		$this->Auth->allow('view', 'index', 'login', 'forgot');

	} // end beforeFilter
	
	public function login() {
		// function to handle login requests
		
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$now = date('Y-m-d H:i:s');
				$user = $this->Auth->user();
				$this->User->id = $user['id'];
				$this->User->saveField('lastlogin', $now);
				$this->User->saveField('useragent', env('HTTP_USER_AGENT'));
				$this->Session->write('current_week', $this->requestAction('/weeks/current'));
				$this->Session->write('unplayed_weeks', $this->requestAction('/weeks/unplayedWeeks'));
				$this->log('Player ' . $user['username'] . ' logged in', 'user');
				return $this->redirect($this->Auth->redirectUrl());
			} else {
				$this->Session->setFlash('Invalid username or password', 'custom-flash', ['myclass' => 'alert-box warning']);
			}
		}

	} // end login
	
	public function forgot() {
		// function to handle forgotten password requests
		// passes submitted data to processForgot method in model

		$user = $this->Auth->user();
		$response = '';
		// if logged in, redirect away from the forgot password page
		if ($user) $this->redirect(['controller'=>'users', 'action'=>'view', $user['id']]);
		
		if ($this->request->is('post')) {
		// form has been submitted
			$response = $this->User->processForgot($this->request->data);
			$this->Session->setFlash($response, 'custom-flash', ['myclass'=>'alert-box info']);
		}
		
	} // end forgot
	
	public function logout() {
		// handles logout request and redirects user

		$this->log('User ' . $this->Auth->user('username') . ' logged out', 'user');
		$this->Session->setFlash(__('You\'re now logged out'), 'custom-flash', ['myclass'=>'alert-box info']);
		$this->redirect($this->Auth->logout());

	}	// end logout
		
} // end class