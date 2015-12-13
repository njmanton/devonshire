<?php

class PlacesController extends AppController {
	public $name = 'Places';
	public $helpers = ['Html', 'Form'];

	public function twohundred() {

		$arr = [
			'conditions' => ['Place.balance >= ' => 200],
			'contain' => 'User.username',
			'fields' => ['week_id', 'Place.balance'],
			'order' => 'balance DESC'
		];

		$data = $this->Place->find('all', $arr);

		if ($this->request->is('requested')) {
			return $data;
		}

	} // end twohundred

} // end class