<?php

// bring in authentication and email handling
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeEmail', 'Network/Email');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
	// basic behaviours
	public $name = 'User';
	public $hasMany = [
		'Prediction', 
		'Standing', 
		'League' => [
			'className' => 'League',
			'foreignKey' => 'admin'
		], 
		'LeagueUser', 
		'Bet', 
		'Place', 
		'Ledger', 
		'Khistory',
		'Kentry',
		'Killer',
		'Post'];

	public $actsAs = ['Containable'];

	// authentication related functions and members below here

	public $validate = [
		'username' => [
			'rule-1' => [
				'rule' => 'notEmpty',
				'message' => 'A username is required'
			],
			'rule-2' => [
				'rule' => 'isUnique',
				'message' => 'That username is already taken'
			]
		],
		'password' => [
			'required' => [
				'rule' => 'notEmpty',
				'message' => 'A password is required'
			]
		]
	];

	public function processUpdate($data,$password) {
		
		// prepare flash response
		$response = '';
		$class = 'error';
		$prefs = 0;

		/*foreach ($data['prefs'] as $k=>$v) {
			if ($v == 'on') {
				$prefs += $k;
			}
		}*/

		// if user has tried to change password
		if (AuthComponent::password($data['current']) != $password && $data['current'] != '') {
			// passwords don't match, and new password completed
			$response = 'The password supplied does not match the password for this user';
		} elseif (strlen($data['new']) < 5 && $data['new'] != '') {
			// new password completed, but too short
			$response = 'The new password must be at least five characters';
		} elseif ($data['new'] != $data['repeat']) {
			// repeated password doesn't match
			$response = 'The new passwords do not match';
		} else {
			// validation ok, so try and update record
			$savedata = [
				'id' => $data['id'],
				'email' => $data['email'],
				'preferences' => $prefs
			];
			// include password if tried to change
			if ($data['current']) $savedata['password'] = $data['new'];
			// save the changes
			if ($this->save(['User'=>$savedata])) {
				$response = 'Details updated';
				$class = 'alert-box success';
				$this->log($data['email'] . ' updated their details ', 'user');
			} else {
				$response = 'Could not update details';
			}
		}
		return [$class, $response];
	
	} // end ProcessUpdate

	public function processSend($data) {

		// set up email parameters
		$subject = isset($data['subject']) ? $data['subject'] : 'Goalmine update';

		// set up parameters

		// add the checkbox values together to get all games
		$game = array_sum($data['game']);

		// do bitwise comparison of game field to select correct users
		$arr = [
			'fields' => 'email',
			'recursive' => 0,
			'conditions' => __('games & %s != 0', $game)
		];

		$users = $this->find('list', $arr);
		foreach ($users as $k=>&$u) {
			if (filter_var($u, FILTER_VALIDATE_EMAIL)) {
				$filtered_users[] = $u;
			}
		}
		// the response. null if the email isnt sent
		$res = null;

		// set up the email object and send email
		// recipients are single list in BCC field so only one email need be sent
		try {
			$email = new CakeEmail('default');
			$email->from(MAIL_SENDER)
						->subject($subject)
						->to(MAIL_SENDER)
						->bcc($filtered_users);
			$res = $email->send($data['body']);
		} catch (SocketException $e) {
			$this->log('Error in sending email');
		}
		$email->reset();

		return (is_null($res)) ? null : count($users) ;

	} // end processSend

	public function processForgot($data) {
		// get form parameters
		$name = $data['Forgot']['name'];
		$to = $data['Forgot']['email'];
		
		// can we find a user with those fields?
		$forgetful_user = $this->find('first', [
			'conditions' => ['username' => $name, 'email' => $to]
		]);
		if (empty($forgetful_user)) {
			// no user
			$response = 'Sorry, no user matches those details';
		} else {
			// generate a new password for the use
			$temp = generate_random_password(8);
			if ($this->save(['User' => ['id' => $forgetful_user['User']['id'], 'password' => $temp]])) {
				// compose an email to the user telling them the new password
				$from = [MAIL_SENDER => 'Goalmine Admin'];
				$subject = 'Forgotten Password';
				$message = 'You have requested a password reset for your Goalmine account. Your new, temporary password is given below.
				
				' . $temp . '
				
				Please log in using this password, and change it to something more memorable';
				$email = new CakeEmail('default');
				$email->from($from)
							->subject($subject)
							->to($to);
				$email->send($message);
				debug($message);
				$response = 'Your password has been reset and emailed to you';
				$this->log(__('Reset password for user: %s, to %s', $name, $temp), 'user');
			} else {
				// couldn't save the new record for some reason
				$response = 'Sorry, your details couldn\'t be updated at this time';
			}
		}
		return $response;
	
	} // end processForgot

	public function beforeSave($options = []) {
	// callback function to hash password

		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}


}