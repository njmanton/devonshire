<?php

class Week extends AppModel {
	public $name = 'Week';
	public $hasMany = [
		'Match', 
		'Standing', 
		'Place',
		'Killer' => [
			'classname' => 'Killer',
			'foreignKey' => 'start_week'
		]
	];
	public $actsAs = ['Containable'];

	// TODO function to handle editing weeks, taking into account 
	// different games

	public function updateLeague($week) {
		// called on updating results
		// get all scores for players
		// write results to Standings table
		
		$sql = 'SELECT 
						U.id,
						U.username,
						P.pred,
						P.joker,
						M.id,
						M.score,
						M.gotw
						FROM 
						users U
						JOIN predictions P ON U.id = P.user_id
						JOIN matches M ON P.match_id = M.id
						WHERE (U.games & 1) != 0
						AND (M.game & 1) != 0 
						AND M.week_id = ?';

		$db = $this->getDataSource();
		$data = $db->fetchAll($sql, [$week]);

		$table = [];
		$goals = [];
		foreach ($data as $w) {
			$uid = $w['U']['id'];
			$mid = $w['M']['id'];
			@$table[$uid]['goals'] += array_sum(explode('-', $w['P']['pred']));
			if (!is_null($w['M']['score'])) {
				$score = calc($w['P']['pred'], $w['M']['score'], $w['P']['joker'], $w['M']['gotw']);
				@$table[$uid]['score'] += $score;
				if (empty($goals[$mid])) {
					$goals[$mid] = array_sum(explode('-', $w['M']['score']));
				}
			} else {
				@$table[$uid]['score'] += 0;
			}
		}

		$goalsum = array_sum($goals);

		if (!empty($table)) {
			foreach ($table as $k=>$n) {
				$goal_diff[$k] = abs($goalsum - $n['goals']);
			}
		}

		$closest = getmin($goal_diff);
		foreach ($table as $k=>&$n) {
			if (in_array($k, $closest)) {
				$n['score'] += 3;
			}
		}

		ksort($table);
		uasort($table, function($x, $y) {
			return ($x['score'] > $y['score']) ? -1 : 1;
		});

		$this->Standing->deleteAll(['week_id' => $week]);

		$row 			= 0;	// which row are we on
		$rank     = 0;	// what is the rank of the player
		$absrank  = 0; 	// what is abs score
		$prevrank = 0;  // rank of row n-1

		foreach($table as $k=>&$n) {
			$absrank = $n['score'];
			if ($absrank == $prevrank) {
				$row++;
			} else {
				$rank = ++$row;
			}
			$prevrank = $absrank;

			$tosave = ['Standing' => [
				'week_id' => $week,
				'user_id' => $k,
				'points' => $n['score'],
				'position' => $rank
			]];
			$this->Standing->create();
			if ($this->Standing->save($tosave)) {
				// TODO something
			} else {
				$this->log('Couldn\'t update standings', 'admin');
			}

		}
		
	} // end updateLeague

	public function checkComplete($week) {

		// checks to see whether all the results for a given week have been entered
		$arr = [
			'fields' => ['id','score'],
			'recursive' => 0,
			'conditions' => ['week_id' => $week, 'game & 1 != 0']
		];

		$results = $this->Match->find('all', $arr);

		// get status of this week
		$status = $this->field('status', ['id' => $week]);

		// loop through the matches, if any result is still null then week can't be complete, so exit
		$comp = 1;
		foreach ($results as $m) {
			if (empty($m['Match']['score'])) {
				$comp = 0;
				break;
			}
		}

		// return true if all matches are complete, _and_ match not already finalised
		return $comp && !$status;
	} // end checkComplete

	public function processComplete($week, $userid) {
	// run when week is complete, finalising all workflows
	// such as updating the ledger and setting week status

		// update status in week model
		$this->id = $week;
		$this->saveField('status', 1);

		// update ledger with -1 for all players who took part
		$players = $this->Match->Prediction->getPlayers($week, $userid);

		// count is total players that took part that week
		$count = count($players);
		$win_amt = ($count * WINNING_PCT); // first place 75% takings
		
		// take a pound from each player
		foreach($players as $k=>$p) {
			$tosave = ['Ledger' => [
				'user_id' => $k,
				'amount' => -1,
				'date' => date('Y-m-d'),
				'description' => __('Entry for week %s', $week)
			]];
			$this->Match->Prediction->User->Ledger->create();
			if ($this->Match->Prediction->User->Ledger->save($tosave)) {
				$this->log(json_encode($tosave), 'admin');
			}
		}

		// find the winner(s)
		$arr = [
			'conditions' => ['week_id' => $week, 'position' => 1],
			'recursive' => 0,
			'fields' => ['user_id']
		];

		$winners = $this->Standing->find('list', $arr);
		$win_amt /= count($winners);

		// add the winnings to the ledger
		foreach ($winners as $w) {
			$tosave = ['Ledger' => [
				'user_id' => $w,
				'date' => date('Y-m-d h:i'),
				'amount' => $win_amt,
				'description' => __('Winnings for week %s', $week)
			]];

			$this->Match->Prediction->User->Ledger->create();
			if ($this->Match->Prediction->User->Ledger->save($tosave)) {
				$this->log(json_encode($tosave), 'admin');
			}
		}

		// add the pot amount to the ledger (using user 0) for the pot
		$tosave = ['Ledger' => [
			'user_id' => 0,
			'date' => date('Y-m-d h:i'),
			'amount' => ($count * (1 - WINNING_PCT)),
			'description' => __('Pot for week %s', $week)
		]];
		$this->Match->Prediction->User->Ledger->create();
		$this->Match->Prediction->User->Ledger->save($tosave);
		$this->log(__('Week: %s set as finalised', $week), $admin);

	} // end processComplete

	public function calcYtd() {
	// function to calculate the overall standings for tipping
		$maxweek = $this->Place->find('first', [
			'order' => 'week_id DESC',
			'conditions' => 'week_id >= ' . START_WEEK
		]);
		if ($maxweek) {
			$max = $maxweek['Place']['week_id'];
		} else {
			$max = 0;
		}
		
		// 2013-08-30 fixed ambiguity in query around balance field
		$arr = [
			'fields' => ['user_id', 'SUM(Place.balance) AS totalbalance'],
			'contain' => 'User.username',
			'group' => 'user_id',
			'order' => 'totalbalance DESC',
			'conditions' => [
				'week_id >= ' . START_WEEK,
				'User.games & 2 != 0'
			]
		];

		$cur = $this->Place->find('all', $arr);

		$arr['conditions'] = [
			__('week_id BETWEEN %s AND %s', START_WEEK, $max - 1),
			'User.games & 2 != 0'
		];

		$prev = $this->Place->find('all', $arr);

		$ytd = [];

		foreach ($cur as $a) {
			$ytd[$a['Place']['user_id']]['balance'] = $a[0]['totalbalance'];
			$ytd[$a['Place']['user_id']]['name'] = $a['User']['username'];
		}
		$x = 0;
		foreach ($prev as $a) {
			$ytd[$a['Place']['user_id']]['prevbalance'] = $a[0]['totalbalance'];
			$ytd[$a['Place']['user_id']]['prevrank'] = ++$x;
		}

		return $ytd;
	} // end ytd

} // end class