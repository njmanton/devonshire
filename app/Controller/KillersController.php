<?php

class KillersController extends AppController {
	public $name = 'Killers';
	public $helpers = ['Html', 'Form'];

	public function index() {
		// show a list of Killer games, with descriptions

		$arr = [
			'fields' => ['id', 'description', 'user_id', 'complete'],
			'recursive' => 0
		];
		$this->set('games', $this->Killer->find('all', $arr));

	} // end index

	public function view($id = null) {
		// shows a particular Killer game

		if (isset($this->request->params['id'])) {
			$gid = $this->request->params['id'];
		} elseif ($id) {
			$gid = $id;
		}

		$userid = $this->Auth->user('id');

		// if no game id passed, flash and redirect
		if (is_null($gid)) {
			$this->flash(__('No game selected'), '/', 1);
		}

		// get the data for this game of killer
		$killers = $this->Killer->getkiller($gid, $this->Auth->user('id'));

		if (is_null($killers)) {
			$this->flash(__('Invalid Game'), '/killers/', 1);
		}

		// get a list of used teams for the combination of user and game
		$used = null;
		foreach ($this->Killer->Khistory->findAllByKillerIdAndUserId($gid, $userid) as $u) {
			$used[$u['Team']['id']] = $u['Team']['name'];
		}

		$latest = array_values($killers)[0];
		$week = $latest['start'];

		$this->Killer->id = $gid;
		$this->set('game', $this->Killer->read(['recursive' => 0]));
		$this->set('used', $used);
		$this->set('killers', $killers);

		$this->set('week', $week);

	} // end view

	public function add() {
		// create a new game of killer

		if (!$this->Auth->user('admin')) {
			$this->flash(__('Only admin users can set up a new killer game'), $this->referer, 2);
		}

		// get the list of players and push to the view
		$arr = [
			'fields' => ['id', 'username'],
			'recursive' => 0,
			'conditions' => ['(games & 4) != 0']
		];
		$this->set('players', $this->Killer->User->find('list', $arr));

		// and the list of available weeks for the start week and push to the view
		$arr = [
			'fields' => ['id', 'start'],
			'conditions' => ['OR' => ['status is null', 'status != 1']],
			'recursive' => 0,
			'order' => 'id ASC',
			'limit' => 5
		];
		$this->set('weeks', $this->Killer->Kentry->Week->find('list', $arr));

		// get the next game number from Killers table
		$arr = [
			'fields' => 'id',
			'order' => 'id DESC'
		];
		$d = $this->Killer->find('first', $arr);
		$new_game = $d['Killer']['id'] + 1;
		$this->set('new_game_number', $new_game);

		if ($this->request->is('post')) {
			// data submitted
			$data = $this->request->data;

			// try and save the new Killer game
			$tosave = ['Killer' => [
				'id' => $new_game,
				'description' => $data['Description'],
				'start_week' => $data['Week'],
				'user_id' => $this->Auth->user('id')
			]];
			$this->Killer->create();
			if ($this->Killer->save($tosave)) {
				$this->log(json_encode($tosave), 'admin');
			} else {
				$this->log(__('Error saving new Killer game'), 'admin');
			}

			// now save initial entries for each player into the Kentry table
			foreach ($data['Players'] as $p) {
				$tosave = ['Kentry' => [
					'killer_id' => $new_game,
					'user_id' => $p,
					'round_id' => 1,
					'week_id' => $data['Week'],
					'lives' => 3
				]];
				$this->Killer->Kentry->create();
				if ($this->Killer->Kentry->save($tosave)) {
					$this->log(json_encode($tosave), 'admin');
					$this->Session->setFlash(__('New Killer game created starting week %s. Access by <a href="/killer/%s">/killer/%s</a>', $data['Week'], $new_game, $new_game), 'custom-flash', ['myclass' => 'alert-box success']);
					
				} else {
					$this->log(__('Error saving new killer record'), 'admin');
					$this->Session->setFlash(__('Problem creating new game for player %s', $p), 'custom-flash', ['myclass' => 'alert-box warning']);
				}
			}
		}
	} // end add

	public function edit($game = null) {

		if (!$this->Auth->user('admin')) {
			$this->flash(__('Only admin users can edit games'), $this->referer, 2);
		}

		// if no parameter passed, get the latest game
		if (is_null($game)) {
			$sql = 'SELECT MAX(id) as max FROM killers';
			$rs = $this->Killer->query($sql);
			$game = $rs[0][0]['max'];
		}

		// get the details of the killer game
		$arr = [
			'conditions' => [
				'Killer.id' => $game
			],
			'contain' => 'Week.start'
		];
		$killer = $this->Killer->find('first', $arr);

		// no data? 
		if (empty($killer)) {
			$this->flash(__('No Killer game with that ID found'), $this->referer, 2);
		}

		$now = new DateTime();
		$start = (empty($killer)) ? null : new DateTime($killer['Week']['start']);

		if ($start && $now >= $start) {
			$this->flash(__('Game %s has already started and cannot now be edited', $game), $this->referer, 2);
		}

		if ($this->request->is('post')) {
			// submit button hit
			$data = $this->request->data;
			foreach ($data['Players'] as $p) {
				$tosave = ['Kentry' => [
					'killer_id' => $game,
					'user_id' => $p,
					'round_id' => 1,
					'week_id' => $killer['Killer']['start_week'],
					'lives' => 3
				]];

				$this->Killer->Kentry->create();
				if ($this->Killer->Kentry->save($tosave)) {
					$this->log(json_encode($tosave), 'admin');
					$this->Session->setFlash(__('Player id %s added to Killer game %s', $p, $game), 'custom-flash', ['myclass' => 'alert-box success']);
				} else {
					$this->log(__('Error saving new killer record'), 'admin');
					$this->Session->setFlash(__('Problem adding player %s to Killer game %s', $p, $game), 'custom-flash', ['myclass' => 'alert-box warning']);
				}
			}
		}

		// get all possible players
		$arr = [
			'fields' => ['id', 'username'],
			'recursive' => 0,
			'conditions' => ['(games & 4) != 0']
		];
		$players = $this->Killer->User->find('list', $arr);

		// and get the players already playing
		$arr = [
			'fields' => 'user_id',
			'conditions' => ['killer_id' => $game]
		];
		$existing_players = $this->Killer->Kentry->find('list', $arr);

		// and then remove the existing players
		foreach ($players as $k=>$p) {
			if (in_array($k, $existing_players)) {
				unset ($players[$k]);
			}
		}

		$this->set('game', $game);
		$this->set('players', $players);

	} // end edit

	public function currentKillers($uid = null) {
			// returns a list of current killer games in which player is involved
		
		if ($this->request->is('requested')) {
			$sql = 'SELECT 
							K.id,
							E.lives,
							E.week_id
							FROM killers K INNER JOIN kentries E ON K.id = E.killer_id
							WHERE K.complete = 0 AND E.user_id = ? AND E.lives > 0';

			$db = $this->Killer->getDataSource();
			$data = $db->fetchAll($sql, [$uid]);

			return $data;
		}

	} // end currentKillers

	public function beforeFilter() {
		// callback to allow certain views without logging in

		parent::beforeFilter();
		$this->Auth->allow('currentKillers');

	} // end beforeFilter

} // end class