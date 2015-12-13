<?php

class WeeksController extends AppController {
	public $name = 'Weeks';
	public $helpers = ['Html', 'Form'];

	public function index() {
		// controller to display list of weeks, with available actions

		$sql = 'SELECT 
						W.id,
						W.start,
						W.status,
						COUNT(M.id) AS ms
						FROM weeks W
						LEFT JOIN matches M ON M.week_id = W.id
						WHERE W.id >= ?
						GROUP BY W.id
						HAVING COUNT(M.id) > 0
						UNION 
						(SELECT 
						W.id, 
						W.start, 
						W.status,
						COUNT(M.id) AS ms
						FROM weeks W 
						LEFT JOIN matches M ON M.week_id = W.id
						WHERE W.id >= ? 
						GROUP BY W.id
						HAVING COUNT(M.id) = 0
						ORDER BY id 
						LIMIT 5)
						ORDER BY 1';
		
		$db = $this->Week->getDataSource();
		$weeks = $db->fetchAll($sql, [START_WEEK, START_WEEK]);

		$this->set('weeks', $weeks);


	} // end index

	public function view($id = null) {
		// function to view a particular week
		// TODO ensure that the correct fields are pulled back reflecting
		// the appropriate games

		if ($this->request->params['id']) {
			$wid = $this->request->params['id'];
		} elseif ($id) {
			$wid = $id;
		}

		$arr = [
			'fields' => ['id', 'start'],
			'conditions' => ['id' => $wid],
			'contain' => [
				'Match' => [
					'fields' => ['id', 'date', 'score', 'game'],
					'Competition' => ['fields' => 'name'],
					'TeamA' => ['fields' => 'name'],
					'TeamB' => ['fields' => 'name']
				]
			]
		];

		$week = $this->Week->find('first', $arr);

		if (empty($week) || is_null($wid)) {
			$this->flash(__('This is not a valid week'), '/weeks/', 2);
		} else {
			$this->set('week', $week);
		}

	} // end view

	public function current() {
		// work out the current week
		// current week is the first week with a start date less than current date
		// or, the following week if the week has been completed
		// usually requested from banner.ctp for each page

		// 2013-08-30 added recursive option to array to limit redundant queries
		$arr = [
			'conditions' => [
				'start <= ' => date('Y-m-d'),
				__('id >= %s', 199)  
			],
			'order' => 'start DESC',
			'fields' => ['id', 'status'],
			'recursive' => 0
		];

		$current = $this->Week->find('first', $arr);

		// if the current week is completed, advance it by 1
		return $current['Week']['id'] + $current['Week']['status'];

	} // end current

	public function ytd() {

		$this->set('ytd', $this->Week->calcYtd());

	} // end ytd

	public function unplayedWeeks() {

		// only process this action is requested (ie there is no view)
		if ($this->request->is('requested')) {

			if ($this->Auth->user()) {
				$id = $this->Auth->user('id'); 
			
				// which games does the user play?
				$games = $this->Auth->user('games'); 

				// checks for weeks that haven't been played to feed into
				// menu notifications in banner.ctp
				
					// get the current date
					$now = new DateTime();

					// get the parameters
					$arr = [
						'fields' => ['id', 'start'],
						'contain' => [
							'Match' => [
								'fields' => ['id', 'game'],
								'Prediction' => [
									'fields' => 'id',
									'conditions' => ['user_id' => $id]
								],
								'Bet' => [
									'fields' => 'id',
									'conditions' => ['user_id' => $id]
								]
							]
						],
						'order' => ['start'],
						'conditions' => ['status' => 0]
					];

					@$data = $this->Week->find('all', $arr);
					// define a new array, and loop through results populating array
					$newarr = [];
					foreach ($data as $d) {
						if (!empty($d['Match'])) {
							$wid = $d['Week']['id'];
							// default values for preds and bets
							$newarr[$wid]['goalmine'] = ['matches' => 0, 'preds' => 0];
							$newarr[$wid]['tipping'] = ['matches' => 0, 'preds' => 0];
							$newarr[$wid]['killer'] = ['matches' => 0, 'preds' => 0];
							foreach($d['Match'] as $m) {
								// only count if the match is a goalmine match, AND the player is a goalmine player
								if (($m['game'] & 1) != 0) {
									$newarr[$wid]['goalmine']['matches']++;
									if (($games & 1) != 0) {
										$newarr[$wid]['goalmine']['preds'] += count($m['Prediction']);
									}
								} 
								if (($m['game'] & 2) != 0) {
									$newarr[$wid]['tipping']['matches']++;
									if (($games & 2) != 0) {
										$newarr[$wid]['tipping']['preds'] += count($m['Bet']);
									}
								}
							}
						}
					}
					return $newarr;

			} else {
				// no user logged in, so return the active weeks
				$sql = 'SELECT
								W.id,
								SUM(M.game & 1 != 0) AS preds,
								SUM(M.game & 2 != 0) AS bets
								FROM matches M
								INNER JOIN weeks W ON W.id = M.week_id
								WHERE W.status = 0
								GROUP BY W.id';
				$db = $this->Week->getDataSource();
				$weeks = $db->fetchAll($sql, []);

				$newarr = [];
				foreach ($weeks as $w) {
					if ($w[0]['preds'] + $w[0]['bets'] > 0) {
						$wid = $w['W']['id'];
						$newarr[$wid]['goalmine']['matches'] = $w[0]['preds'];
						$newarr[$wid]['tipping']['matches'] = $w[0]['bets'];
					}
				}

				return $newarr;

			}

		} else {
			// throw exception if not a requested action
			throw new MethodNotAllowedException();
		}
		

	} // end unplayedWeeks

	public function beforeFilter() {

		parent::beforeFilter();
		$this->Auth->allow('unplayedWeeks');

	} // end beforeFilter

} // end class