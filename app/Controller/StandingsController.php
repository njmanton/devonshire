<?php

class StandingsController extends AppController {
	public $name = 'Standings';
	public $helpers = ['Html', 'Form'];

	public function index() {
	// the index action shows the *overall* league table
			
		$data = $this->Standing->index();
		$this->set('league', $data);
		
	} // end index
	
	public function view($week = null) {
	// the view action shows *individual* standings for a given week
	
		// get data from model
		$league = $this->Standing->view($week);

		// handles request from predictions controller
		if ($this->request->is('requested')) {
			return $league;
		}

		if (empty($league)) {
		// no results yet been entered
			$this->flash(__('There are no results for this week yet'), $this->referer, 2);
		} elseif (is_null($week)) {
		// no parameter passed in url
			$this->flash(__('No week selected'), '/weeks/', 2);
		} else {
		// pass the data to the view
			$this->set('league', $league);
			$this->set('week', $week);
		}

	}  // end view

	public function thirtyclub() {

		if ($this->request->is('requested')) {

			$arr = [
				'fields' => ['week_id', 'points'],
				'contain' => 'User.username',
				'conditions' => 'points >= 30',
				'order' => 'points DESC'
			];

			return $this->League->find('all', $arr);
		}

	} // end thirtyclub
		
}