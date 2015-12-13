<?php

class KentriesController extends AppController {
	public $name = 'Kentries';
	public $helpers = ['Html', 'Form'];

	public function add() {

		// adds a killer match (kentry record). Need to add both the match, and the killer records

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			
			// get the POSTed data
			$data = $this->request->data;
			$postdate = date('Y-m-d', strtotime($data['date']));
			// get the logged in user id
			$uid = $this->Auth->user('id');
			// default response
			$success = false;
			
			// log the attempt
			$this->log(json_encode($data), 'killer');

			// try and find the match
			// 1. is matchid passed back from form (ie an edit) _and_ details are same as saved match?
			// 2. search Match model for combination of teams and week
			// 3. no match, so add new Match row

			$same = 0;
			if ($data['matchid']) {
				$match = $this->Kentry->Match->findById($data['matchid']);
				$mid = $match['Match']['id'];
				$same = (($data['teama_id'] == $match['Match']['teama_id']) && ($data['teamb_id'] == $match['Match']['teamb_id']) && ($data['week'] == $match['Match']['week_id']));
			}

			if (!$data['matchid'] || $same == false) {
				$arr = [
					'conditions' => [
						'teama_id' => $data['teama_id'],
						'teamb_id' => $data['teamb_id'],
						'week_id' => $data['week']
					],
					'recursive' => 0
				];
				$match = $this->Kentry->Match->find('first', $arr);
				if (!empty($match)) {
					$mid = $match['Match']['id'];
				}
			} 

			if (empty($match)) {
				$tosave = ['Match' => [
					'teama_id' => $data['teama_id'],
					'teamb_id' => $data['teamb_id'],
					'game' => 4,
					'date' => $postdate,
					'week_id' => $data['week']
				]];
				$this->Kentry->Match->create();
				$this->Kentry->Match->save($tosave);
				$mid = $this->Kentry->Match->id;
			} else {
				// if we've found a record in the match table, ensure that the Killer bit is set
				$tosave = ['Match' => [
					'id' => $mid,
					'game' => ($match['Match']['game'] | 4)
				]];
				$this->Kentry->Match->save($tosave);
			}

			// now update kentry field
			$tosavek = ['Kentry' => [
				'id' => $data['kid'], // this isn't the kentry uid, it's the killer id!
				'match_id' => $mid,
				'pred' => $data['pred']
			]];
			if ($this->Kentry->save($tosavek)) {
				echo json_encode($mid);
			} else {
				echo json_encode(false);
			}

		}

	} // end add

	public function beforeFilter() {
		// callback to allow certain views without logging in

		parent::beforeFilter();
		$this->Auth->allow('currentKiller');

	} // end beforeFilter

} // end class