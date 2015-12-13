<?php

class Standing extends AppModel {
	public $name = 'Standing';
	public $belongsTo = ['Week', 'User'];
	public $actsAs = ['Containable'];
	
	public function view($week = null) {
	// function to return the standings for a given week

		// prepare the query
		$arr = [
			'contain' => [
				'User' => [
					'fields' => ['id', 'username']
				],
			],
			'conditions' => ['week_id' => $week]
		];

		$data = $this->find('all', $arr);
		// loop through results, and rearrange into new array
		$table = [];
		foreach ($data as $d) {
			@$table[$d['User']['id']]['name'] = $d['User']['username'];
			@$table[$d['User']['id']]['points'] += $d['Standing']['points'];
			@$table[$d['User']['id']]['gp']++;
		}

		// sort the array on key values, then custom sort on points
		ksort($table);
		uasort($table, function($x,$y) {
			return ($x['points']>$y['points']) ? -1 : 1;
		});

		return $table;
	} // end view

	public function index() {
		//league calculation

		// and the current week
		$currentweek = $this->requestAction('weeks/current');
		// the returned array
		$ytd = [];

		$arr = [
			'fields' => ['user_id', 'week_id', 'points'],
			'contain' => 'User.username',
			'order' => ['user_id', 'points DESC'],
			'conditions' => ['week_id >=' => LEAGUE_START_WEEK]
		];

		$leaguetodate = $this->find('all', $arr);

		$arr['conditions'] = ['week_id >= ' => LEAGUE_START_WEEK, 'week_id <=' => $currentweek - 1];
		$prevleague = $this->find('all', $arr);

		$prevuser = 0;
		foreach ($leaguetodate as $k=>$l) {
			$uid = $l['Standing']['user_id'];
			$rank = ($uid != $prevuser) ? 1 : ++$rank;
			$ytd[$uid]['user'] = $l['User']['username'];
			@$ytd[$uid]['weeks']++;
			if ($rank <= LEAGUE_WEEKS) {
				@$ytd[$uid]['points'] += $l['Standing']['points'];
				@$ytd[$uid]['lowest']  = $l['Standing']['points'];
			}
			$prevuser = $l['Standing']['user_id'];
		}

		foreach ($prevleague as $l) {
			$rank = ($l['Standing']['user_id'] != $prevuser) ? 1 : ++$rank;
			if ($rank <= LEAGUE_WEEKS) {
				@$ytd[$l['Standing']['user_id']]['prevpoints'] += $l['Standing']['points'];
			}
		}

		// sort by prevpoints to get previous week's rank

		uasort($ytd, function($x, $y) {
			if (isset($x['prevpoints']) && isset($y['prevpoints'])) {
				return ($x['prevpoints'] > $y['prevpoints']) ? -1 : 1 ;
			} else {
				return ($x['points'] > $y['points']) ? -1 : 1;
			}
			
		});

		$row = 0;
		$rank = 0;
		$prevpoints = 0;

		foreach($ytd as &$y) {
			if (isset($y['prevpoints']) && ($y['prevpoints'] == $prevpoints)) {
					$row++;
				} else {
					$rank = ++$row;
				}
				$prevpoints = $y['points'];
				@$y['oldrank'] = $rank;	
		}

		// sort by current rankings
		uasort($ytd, function($x,$y) {
			return ($x['points']>$y['points']) ? -1 : 1 ;
		});

		$row = 0;
		$rank = 0;
		$prevpoints = 0;

		foreach($ytd as &$y) {
			if ($y['points'] == $prevpoints) {
					$row++;
				} else {
					$rank = ++$row;
				}
				$prevpoints = $y['points'];
				@$y['rank'] = $rank;	
		}

		return $ytd;

	} // end index

} // end class