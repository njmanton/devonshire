<?php

class PredictionsController extends AppController {
	public $name = 'Predictions';
	public $helpers = ['Html', 'Form', 'Score', 'Cache'];

	public function view($id = null) {
		// this provides and handles the data for predictions for a given week
		if (isset($this->request->params['id'])) {
			$wid = $this->request->params['id'];
		} elseif ($id) {
			$wid = $id;
		}

		$userid = $this->Auth->user('id');
		if (is_null($wid)) {
		// if no week parameter passed, redirect back to weeks list
			$this->flash(__('No week specified'), '/weeks/', 2);
		}

		if ($this->request->is('post')) {
		// handle data posted back from user

			$updates = $this->Prediction->process($this->request->data, $userid);
			$this->set('updates', $updates);
			$this->Session->setFlash(__('%s match(es) updated', count($updates)), 'custom-flash', ['myclass' => 'alert-box success']);
			$ret = $this->Prediction->Match->Week->updateLeague($wid);
			$this->Prediction->Match->Week->Place->updatePlaces($wid);
		}

		// retrieve data for $week and pass onto view
		$arr = [
			'conditions' => ['id' => $wid],
			'fields' => ['id', 'start', 'status'],
			'recursive' => 0
		];
		$this->set('week', $this->Prediction->Match->Week->find('first', $arr));

		// get the array of predictions x player
		$table = $this->Prediction->getTable($wid);
		if (empty($table)) {
			$this->flash(__('No matches entered for this week yet'), 'weeks', 2);
		} else {
			$this->set('table', $table);
		}

		// get the array of players
		$this->set('players', $this->Prediction->getPlayers($wid, $userid));

		if ($this->Prediction->Match->Week->checkComplete($wid)) {
			$this->set('complete', 1);
		}

	} // end view

	public function complete($week) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		$userid = $this->Auth->user('id');
		$ret = $this->Prediction->Match->Week->processComplete($week, $userid);

		$this->Session->setFlash(__('Week set as finalised'), 'custom-flash', ['myclass' => 'alert-box success']);
		$this->redirect(['action' => 'view', $week]);
	}

} // end class