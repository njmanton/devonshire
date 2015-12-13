<?php

class LedgersController extends AppController {
	public $name = 'Ledgers';
	public $helpers = ['Html', 'Form'];

	public function index() {
		// function to get all transactions
		// admin only?

		if ($this->Auth->user('admin')) {
			$arr = ['contain' => 'User.username'];
			$this->set('lines', $this->Ledger->find('all', $arr));
		} else {
			$this->flash(__('You must be an admin to view this page'), '/', 2);
		}


	} // end index

	public function edit() {
	// handles admin function of adding a new ledger line

		if ($this->request->is('Post')) {
			if ($this->Ledger->save($this->request->data)) {
				$this->Session->setFlash(__('Money updated'), 'custom-flash', ['myclass' => 'alert-box info']);
				$this->log(__('Ledger updated for userid: %s', $this->request->data['Ledger']['user_id']), 'admin');
			}
		}

		// gets list of users to populate select box
		$this->set('users', $this->Ledger->User->find('list', ['fields' => 'username']));

	} // end edit

	public function view($player = null) {
	// displays a table of ledger entries for the given user

		if (is_null($player)) {
		// if player id not passed, redirect
			$this->flash(__('Can\'t find player'), '/users', 2);
		}

		$user = $this->Auth->user();
		// added further clause to allow people to see the pot (player 0)
		if (($user['id'] == $player) || $user['admin'] || $player == 0) {
		// if the right player is logged in or it's an admin user, show the data
			
			if ($this->request->is('requested')) {
			// if this method is requested (ie not from the 'view' view) return the balance
				$arrtot = [
					'conditions' => ['Ledger.user_id' => $player, 'date >= ' => '2014-08-01'],
					'fields' => 'amount',
					'recursive' => 0
				];
				$balance = array_sum($this->Ledger->find('list', $arrtot));
				return $balance;
			}

			$arr = [
				'conditions' => ['Ledger.user_id' => $player, 'date >=' => '2014-08-01'],
				'order' => ['date ASC', 'description ASC'],
				'fields' => ['id', 'amount', 'date', 'description'],
				'contain' => 'User.username'
			];

			$l = $this->Ledger->find('all', $arr);
			
			if (!empty($l)) {
			// if there's data, send it to the view, otherwise send a flash message
				$this->set('lines', $l);
			} else {
				$this->flash(__('No transactions yet. Once you have played one week, or paid money in then the details will appear here'), '/users/', 2);
			}

		} else {
			$this->flash(__('Sorry, you can only view your own balances'), '/users/', 2);
		}
		
	} // end view

	public function pot() {

		if ($this->request->is('requested')) {
			$arrtot = [
				'conditions' => ['Ledger.user_id' => 0, 'date >= ' => '2014-08-01'],
				'fields' => 'amount',
				'recursive' => 0
			];
			$balance = array_sum($this->Ledger->find('list', $arrtot));
			return $balance;
		}

	} // end pot

	public function beforeFilter() {
		// callback to allow certain views without logging in

		parent::beforeFilter();
		$this->Auth->allow('pot');

	} // end beforeFilter
	
} // end class