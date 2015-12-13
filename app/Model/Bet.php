<?php

class Bet extends AppModel {
	public $name = 'Bet';
	public $belongsTo = ['User', 'Match'];
	public $actsAs = ['Containable'];

	public $validate = [
		'amt' => [
			'rule' => ['range', -1, 61]
		]
	];

	public function calcStats() {

		$arr = [
			'fields' => 'amount',
			'contain' => [
				'Match' => [
					'fields' => ['id', 'result', 'odds1', 'odds2', 'oddsX', 'week_id'],
					'TeamA' => ['fields' => 'name'],
					'TeamB' => ['fields' => 'name']
				],
				'User' => ['fields' => 'username']
			]
		];

		$stats = $this->Find('all', $arr);

	} // end calcStats

	public function getTable($week) {
		
		// set up query parameters
		$arr = [
			'fields' => ['id', 'date', 'week_id', 'odds1', 'odds2', 'oddsX', 'score'],
			'conditions' => ['week_id' => $week, '(game & 2) != 0'],
			'contain' => [
				'TeamA' => ['fields' => 'sname'],
				'TeamB' => ['fields' => 'sname'],
				'Bet' => [
					'fields' => ['user_id', 'match_id', 'amount', 'prediction', 'outcome'],
					'User' => ['fields' => ['username', 'id']]
				]
			]
		];

		// retrieve the data
		$table = $this->Match->find('all', $arr);
		// set up new table to hold reaults
		$newarr = [];

		foreach($table as $r) {

				// work out the winner from the score
			@list($h,$a) = explode('-', $r['Match']['score']);
			if (!isset($r['Match']['score'])) {
				$result = '';
				$winner = '';
			} elseif ($h > $a) {
				$result = '1';
				$winner = $r['TeamA']['sname'];
			} elseif ($h < $a) {
				$result = '2';
				$winner = $r['TeamB']['sname'];
			} elseif ($h == $a) {
				$result = 'X';
				$winner = 'Draw';
			}

			// populate new array with the match details
			$mid = $r['Match']['id'];
			$newarr[$mid]['_match']['id'] = $mid;
			$newarr[$mid]['_match']['date'] = $r['Match']['date'];
			$newarr[$mid]['_match']['fixture'] = $r['TeamA']['sname'] . ' v ' . $r['TeamB']['sname'];
			$newarr[$mid]['_match']['result'] = $winner;
			if ($r['Match']['score']) {
				$newarr[$mid]['_match']['return'] = $r['Match']['odds' . $result];
			}

			foreach ($r['Bet'] as $b) {
				// now loop through each bet, adding that to new array

				// define some simpler variable names
				$amt = $b['amount'];
				$uid = $b['User']['id'];
				$outcome = ($b['outcome'] && ($result)) ? ($r['Match']['odds' . $result] * $amt) - $amt : ($amt *- 1);

				if ($b['prediction'] == 1) {
					$pred = $r['TeamA']['sname'];
				}	elseif ($b['prediction'] == 2) {
					$pred = $r['TeamB']['sname'];
				}	else {
					$pred = 'Draw';
				}

				$newarr[$mid][$uid]['name'] = $b['User']['username'];
				$newarr[$mid][$uid]['bet'] = $pred;
				$newarr[$mid][$uid]['amt'] = $amt;
				$newarr[$mid][$uid]['res'] = $outcome;

				// show the bets be hidden?
				if (HIDE_BETS === 1) {
					$wk = $this->Match->Week->findById($week);
					$start = new DateTime($wk['Week']['start']);
					$start->add(new DateInterval(DEADLINE_OFFSET));
					$now = new DateTime();
					if ($start > $now) {
						$newarr[$mid][$b['User']['id']]['bet'] = '???';
						$newarr[$mid][$b['User']['id']]['res'] = 0;
					}
				}
				
			}

		}

		// custom sort on array by match date
		uasort($newarr, function($x, $y) {
			return ($x['_match']['date'] < $y['_match']['date']) ? -1 : 1;
		});

		return $newarr;

	} // end getTable

	public function getPlayers($week, $id = null) {

		// set up array 
		$players = [];

		// setup query
		$id = (is_null($id)) ? 0 : $id;

		$sql = 'SELECT
						DISTINCT username, 
						U.id, 
						(U.id = ?) AS self
						FROM bets B 
						INNER JOIN users U ON U.id=B.user_id
						INNER JOIN matches M ON M.id=B.match_id 
						WHERE M.week_id = ?';

		$db = $this->getDataSource();
		$data = $db->fetchAll($sql, [$id, $week]);
		
		// iterate over returned data and populate array
		foreach ($data as $d) {
			$players[$d['U']['id']]['username'] = $d['U']['username'];
			$players[$d['U']['id']]['self'] = $d[0]['self'];
		}

		// custom sort to ensure logged-in player appears first in list
		uasort($players, function($x,$y) {
			return ($x['self']>$y['self']) ? -1 : 1;
		});

		return $players;

	} // end getPlayers

	public function getStandings($week) {
		// get the standings for a particular week

		// setup parameters
		$arr = [
			'conditions' => ['week_id' => $week],
			'contain' => ['User.username', 'User.id'],
			'order' => 'rank'
		];

		$places = $this->User->Place->find('all', $arr);

		return $places;

	} // end getStandings

	public function processBets($data, $id) {
		
		$upd = [];

		// loop through the submitted data
		foreach ($data as $k=>$v) {
			// if the Bet PK exists, delete that row from the database
			if (isset($v['betid']) && $v['betid'] != '') {
				$this->delete($v['betid']);
			}
			// if this row has data, then create the array
			if (array_key_exists('sel', $v)) {
				$bet = ['Bet' => [
					'match_id' => $k,
					'user_id' => $id, 
					'amount' => (int)$v['amt'],
					'prediction' => $v['pred']
					]
				];
				// if existing row then add PK to update row, else create new row
				if ($v['betid'] != '') {
					$bet['Bet']['id'] = $v['betid'];
				}
				// save the data
				$this->create();
				if ($this->save($bet)) {
					if ($v['dirty'] == 1) {
						$upd[] = $k;
						$this->log(json_encode($bet), 'pred');
					}
				} else {
					$this->log(__('Error saving Bet data. user %s', $id), 'pred');
				}
			} 
		}
		return $upd;
	} // end processBets

	public function userBets($id = null) {

		$arr = [
			'fields' => ['id', 'username'],
			'conditions' => ['id' => $id],
			'contain' => [
				'Bet' => [
					'fields' => ['amount', 'prediction'],
					'Match' => [
						'conditions' => __('week_id >= %s', START_WEEK),
						'fields' => ['week_id', 'date', 'score', 'odds1', 'odds2', 'oddsX'],
						'TeamA' => ['fields' => 'name'],
						'TeamB' => ['fields' => 'name']
					]
				]
			]
		];

		$data = $this->User->find('all', $arr);

		$matches = [];

		foreach ($data[0]['Bet'] as $b) {
			if (!empty($b['Match'])) {
				$wid = $b['Match']['week_id'];
				$bid = $b['match_id'];
				$matches[$wid][$bid]['fixture'] = $b['Match']['TeamA']['name'] . ' v ' . $b['Match']['TeamB']['name'];
				$matches[$wid][$bid]['date'] = $b['Match']['date'];
				$matches[$wid][$bid]['bet'] = $b['prediction'];
				$matches[$wid][$bid]['amount'] = $b['amount'];
				
				if (isset($b['Match']['score'])) {
				list($h,$a) = explode('-', $b['Match']['score']);
				if ($h==$a) {
					$matches[$wid][$bid]['result'] = 'X';
					$matches[$wid][$bid]['win_odds'] = $b['Match']['oddsX'];
				} elseif ($h>$a) {
					$matches[$wid][$bid]['result'] = '1';
					$matches[$wid][$bid]['win_odds'] = $b['Match']['odds1'];
				} elseif ($h<$a) {
					$matches[$wid][$bid]['result'] = '2';
					$matches[$wid][$bid]['win_odds'] = $b['Match']['odds2'];
				}

				$matches[$wid][$bid]['outcome'] = 
				  ($b['prediction'] == $matches[$wid][$bid]['result']) 
					? ($matches[$wid][$bid]['win_odds'] - 1) * $b['amount'] 
					: $b['amount'] * -1;
				}	
			}
		}

		return $matches;

	} // end userBets


	public function calculateBets($type, $id = null) {
	// function to retrieve data from bet model and return calculated results
	// arguments - $type filter on 'all', 'user' or 'match'
	// $id - used for 

		// setup valid types of return value
		$types = ['user', 'match', 'week', 'all'];
		if (!in_array($type, $types) || ($id == null && $type != 'all')) {
			return null;
		}

		// define standard query parameters
		$arr = [
			'fields' => ['id', 'amount', 'prediction', 'outcome'],
			'contain' => [
				'Match' => [
					'fields' => ['week_id', 'date', 'odds1', 'odds2', 'oddsX', 'score'],
					'TeamA' => ['fields' => 'name'],
					'TeamB' => ['fields' => 'name']
				],
				'User' => ['fields' => 'username']
			],
			'order' => ['Week_id', 'Match.id'],
		];

		// add the query conditions, based on the type
		if (($type == 'user') && isset($id)) {
			$arr['conditions'] = ['user_id' => $id];
		} elseif (($type == 'match') && isset($id)) {
			$arr['conditions'] = ['match_id' => $id];
		} elseif (($type == 'week') && isset($id)) {
			$arr['conditions'] = ['Match.week_id' => $i];
		}

		// extract the data
		$data = $this->find('all', $arr);

		$newarr = [];

		// transform into new array
		foreach ($data as $b) {
			$bid = $b['Bet']['id'];
			$newarr[$bid]['match_id']	  = $b['Match']['id'];
			$newarr[$bid]['user_id']	  = $b['User']['id'];
			$newarr[$bid]['username']	  = $b['User']['username'];
			$newarr[$bid]['week_id']	  = $b['Match']['week_id'];
			$newarr[$bid]['date']			  = $b['Match']['date'];
			$newarr[$bid]['prediction'] = $b['Bet']['prediction'];
			$newarr[$bid]['amount']		  = $b['Bet']['amount'];

			if (isset($b['Match']['score'])) {
				$odds = $b['Match']['odds' . $b['Bet']['prediction']] - 1;
				$newarr[$bid]['outcome'] = 
				  ($b['Bet']['outcome'] == 1) ? ($odds * $b['Bet']['amount']) : ($b['Bet']['amount'] * -1);
			} else {
				$newarr[$bid]['outcome'] = null;
			}
		}
		return $newarr;

	} // end calculateBets

} // end class
	
	