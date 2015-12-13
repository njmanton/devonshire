<?php

class Place extends AppModel {
	public $name = 'Place';
	public $belongsTo = ['User', 'Week'];
	public $actsAs = ['Containable'];

	public function updatePlaces($week = null) {
		// this function is called whenever a result is updated
		// get the bets for the current week and work out the totals

		// get the match data for the current week
		$arr = [
			'fields' => ['id', 'odds1', 'odds2', 'oddsX', 'score'],
			'contain' => [
				'Bet.user_id',
				'Bet.amount',
				'Bet.prediction'],
			'conditions' => ['week_id' => $week, 'game & 2 != 0']
		];

		$matchdata = $this->Week->Match->find('all', $arr);

		if (empty($matchdata)) {
			// if there no matches this week (e.g. international week) don't need to update
			return false;
		}

		// get all tipping players
		$players = $this->User->find('list', ['conditions' => 'games & 2 != 0']);

		// create a new array of players and balances
		$newarr = [];

		// initialise the array
		foreach ($players as $p) {
			$newarr[$p]['balance'] = null;
		}

		// loop through the matches and bets
		foreach ($matchdata as $m) {
			$res = '';
			if (!is_null($m['Match']['score'])) {
				// get the match res
				list($h,$a) = explode('-', $m['Match']['score']);
				if ($h>$a) {
					$res = '1';
				} elseif ($h<$a) {
					$res = '2';
				} else {
					$res = 'X';
				}
			}
			// loop through bets calculating the return
			foreach ($m['Bet'] as $b) {
				if ($b['prediction'] == $res) {
					$newarr[$b['user_id']]['balance'] += (float)($m['Match']['odds' . $res] - 1) * $b['amount'];
				} else {
						$newarr[$b['user_id']]['balance'] -= ($b['amount']);
				}		
			}
		}

		// custom sort on the array by balance
		uasort($newarr, function($x, $y) {
			return ($x['balance']>$y['balance'])?-1:1;
		});

		$x=0;
		$this->deleteAll(['week_id' => $week]);
		foreach ($newarr as $k=>&$n) {
			$n['user_id'] = $k;
			if (is_null($n['balance'])) {
				$n['balance'] = -100;
			}
			$n['week_id'] = $week;
			$n['rank'] = ++$x;
			$place = ['Place' => $n];
			$this->create();
			if ($this->save($place)) {
				//TODO logging
			} else {
				//TODO logging
			}
		}

	} // end updatePlaces

} // end Class