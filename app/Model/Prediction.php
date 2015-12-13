<?php

App::uses('AuthComponent', 'Controller/Component');

class Prediction extends AppModel {
	public $name = 'Prediction';
	public $belongsTo = ['Match', 'User'];
	public $actsAs = ['Containable'];
	
	public $validate = [
		'pred' => [
			'rule' => '/^[0-9]{1,2}-[0-9]{1,2}$/',
			'message' => 'each score must be between 0 and 99'
		]
	];

	public function getPlayers($week = null, $userid = null) {
		// get all the players who have made a prediction during the game week, plus
		// the logged in user if not already listed
		$players = [];

		$qry_user = __('SELECT username, U.id FROM users U WHERE id = %s UNION ', $userid);
		$qry = 'SELECT DISTINCT username, U.id FROM predictions P INNER JOIN users U ON U.id=P.user_id
						INNER JOIN matches M ON M.id=P.match_id WHERE M.week_id = ' . $week;

		$qry = (is_null($userid)) ? $qry : ($qry_user . $qry);

		$data = $this->query($qry);

		// loop through query results to create a simpler array
		foreach ($data as $d) {
			$players[$d[0]['id']]['username'] = $d[0]['username'];
		}

		return $players;

	} // end getPlayers

	public function getTable($week = null) {
		// this function creates a new 2D array of predictions by players for the week

		$newarr = [];
		
		// the second condition ensures that only goalmine games are included
		$arr = [
			'conditions' => ['week_id' => $week, 'game & 1 != 0'],
			'fields' => ['id', 'date', 'gotw', 'week_id', 'score'],
			'contain' => [
				'TeamA' => ['fields' => 'name'],
				'TeamB' => ['fields' => 'name'],
				'Competition' => ['fields' => ['id', 'name', 'country']],
				'Prediction' => [
					'fields' => ['pred', 'id', 'joker', 'points'],
					'User' => ['fields' => ['id', 'username']]
				]
			]
		];

		$table = $this->Match->find('all', $arr);

		// loop through the predictions, populating a new array 
		// 2013-09-02 added an element for competition id, for sorting purposes
		foreach ($table as $row) {
			// _match dimension holds info about match data, rather than prediction
			$mid = $row['Match']['id'];
			$newarr[$mid]['_match']['id'] = $mid;
			$newarr[$mid]['_match']['date'] = $row['Match']['date'];
			$newarr[$mid]['_match']['comp'] = $row['Competition']['name'];
			$newarr[$mid]['_match']['country'] = $row['Competition']['country'];
			$newarr[$mid]['_match']['compid'] = $row['Competition']['id'];
			$newarr[$mid]['_match']['fixture'] = $row['TeamA']['name'] . ' v ' . $row['TeamB']['name'];
			$newarr[$mid]['_match']['score'] = $row['Match']['score'];
			$newarr[$mid]['_match']['gotw'] = $row['Match']['gotw'];

			foreach ($row['Prediction'] as $p) {
				// new dimension for each player
				$pid = $p['User']['id'];
				$newarr[$mid][$pid]['name'] = $p['User']['username'];
				$newarr[$mid][$pid]['pred'] = $p['pred'];
				$newarr[$mid][$pid]['predid'] = $p['id'];
				$newarr[$mid][$pid]['joker'] = $p['joker'];
				$newarr[$mid][$pid]['pts'] = $p['points'];
			}	
		}

		// sort the new arr using a custom sort function: gotw to the top, then by date
		// 2013-09-02 changed sort function to also sort by comp id
		uasort($newarr, function($x,$y) {
			if ($x['_match']['gotw'] == $y['_match']['gotw']) {
				if ($x['_match']['date'] == $y['_match']['date']) {
					return ($x['_match']['compid'] < $y['_match']['compid']) ? -1 : 1 ;
				} else {
					return ($x['_match']['date'] < $y['_match']['date']) ? -1 : 1 ;
				}
			} else {
				return ($x['_match']['gotw'] > $y['_match']['gotw']) ? -1 : 1 ;
			}
		});

		return $newarr;

	} // end getTable

	public function process($data, $userid) {
		// function to process user data from the predictions view
		// this could be new or updated predictions, or results from admin users
		$update = [];

		$joker = (isset($data['joker'])) ? $data['joker'] : false;
		foreach($data as $k=>$m) {
		
			// loop through and remove the jokers for this week, so only one match can have a joker
			if (isset($m['predid']) && $m['predid'] != '' && ($k != 'joker')) {
				/*$this->id = $m['predid'];
				$this->saveField('joker', (int)($joker==$k));*/
				$this->create();
				$tosave = ['Prediction' => [
					'id' => $m['predid'],
					'joker' => (int)($joker==$k),
					'user_id' => $userid,
					'match_id' => $k
				]];
				$this->log(__('data %s: %s', $k, json_encode($tosave)), 'debug');
				$this->save($tosave);
			}

			if (isset($m['pred']) && $m['dirty'])  {
				// if a prediction has been made, and the record is dirty (changed)
				$tosave = ['Prediction' => [
					'user_id' => $userid,
					'match_id' => $k,
					'pred' => $m['pred']
				]];
				// if the prediction id is passed back then add it to the saved record
				// to indicate an update, rather than insert
				if (isset($m['predid'])) {
					$tosave['Prediction']['id'] = $m['predid'];
				}

				if (isset($joker) && $joker == $k) {
					$tosave['Prediction']['joker'] = 1;
				}

				// save the record to the Prediction model
				$this->create();
				if ($this->save($tosave)) {

					// add the match id to the update array to pass back to the view
					$update[] = $k;
					$this->log(json_encode($tosave), 'pred');
				}
			}
			
			// if the score is passed back, create a skeleton record to update match record
			if (isset($m['score']) && $m['score'] != '' && $m['scoredirty']) {

				$gotw = (isset($m['gotw']) && $m['gotw'] == 1) ? 1 : 0 ;
				$results = ['Match' => [
					'id' => $k,
					'score' => $m['score'],
					'gotw' => $gotw
				]];
				$this->Match->create();
				// save the record to the Match model
				if ($this->Match->save($results)) {
					$update[] = $k;
					$this->log(json_encode($results), 'admin');
					// update killer model with result TODO only call if killer game
					$ret = $this->Match->Kentry->updateKiller($k);
					// update the prediction and bet models with points
					$ret = $this->Match->updateModels($k);
				}
			}

		}
		//$this->Session->write('unplayed_weeks', $this->requestAction('/weeks/unplayedWeeks'));
		//CakeSession::write('unplayed_weeks', $this->requestAction('/weeks/unplayedWeeks'));
		return $update;

	} // end process

} // end class