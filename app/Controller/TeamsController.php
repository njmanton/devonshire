<?php

class TeamsController extends AppController {
	public $name = 'Teams';
	public $helpers = ['Html', 'Form'];
	
	// function to show list of all teams
	public function index() {
	
		$arr = [
			'fields' => ['id', 'name', 'country'],
			'contain' => [
				'homeTeam' => [
					'fields' => ['gotw', 'score'],
				],
				'awayTeam' => [
					'fields' => ['gotw', 'score'],
				]
			]
		];

		$data = $this->Team->find('all', $arr);
		$teams = [];

		foreach ($data as $d) {
			$team = [
				'name' => '',
				'gotw' => 0,
				'w' => 0,
				'd' => 0,
				'l' => 0
			];

			$team['name'] = $d['Team']['name'];
			foreach ($d['homeTeam'] as $ht) {
				if ($ht['gotw']) {
					$team['gotw']++;
				}
				if (isset($ht['score'])) {
					list($h,$a) = explode('-', $ht['score']);
					if ($h > $a) {
						$team['w']++;
					} elseif ($h < $a) {
						$team['l']++;
					} else {
						$team['d']++;
					}
				}
			}
			foreach ($d['awayTeam'] as $ht) {
				if ($ht['gotw']) {
					$team['gotw']++;
				}
				if (isset($ht['score'])) {
					list($h,$a) = explode('-', $ht['score']);
					if ($h < $a) {
						$team['w']++;
					} elseif ($h > $a) {
						$team['l']++;
					} else {
						$team['d']++;
					}
				}	
			}
			$teams[$d['Team']['id']] = $team;
		}
		
		$this->set('teams', $teams);

	} // end index

	public function show() {
		// this action handles ajax requests for team names

		if ($this->request->is('ajax')) {
			$uid = $this->Auth->user('id');
			$this->disableCache();
			$this->autoRender = false;
			$arr = [
				'fields' => ['id', 'name'],
				'conditions' => ['name LIKE ' => '%'.$_GET['term'].'%']
			];
			if (isset($_GET['killer'])) {
				// this is a killer lookup, so only include english teams and build a list of
				// used teams by user and killer id
				$arr['conditions'][] = 'englishleague = 1';
				$used_teams = $this->Team->Khistory->findAllByKillerIdAndUserId($_GET['killer'], $uid);
			}
			$teams = $this->Team->find('list', $arr);

			if (!empty($used_teams)) {
				foreach($used_teams as $u) {
					$used[$u['Team']['id']] = $u['Team']['name'];
				}
				$teams = array_diff($teams, $used);
			}

			foreach ($teams as $k=>$t) {
				$new[] = ['id' => $k, 'value' => $t];
			}
			echo json_encode($new);
		} else {
			throw new ForbiddenException();
		}

	} // end show
	

	// function to show a particular team
	// finds all matches involving the team	
	public function view($id = null) {

		if ($this->request->params['id']) {
			$tid = $this->request->params['id'];
		} elseif ($id) {
			$tid = $id;
		}

		if (is_null($tid)) {
			$this->flash(__('No team selected'), $this->referer(), 2);
		}

		$arr = [
			'conditions' => ['Team.id' => $tid],
			'contain' => [
				'homeTeam' => [
					'fields' => ['date', 'id', 'week_id', 'gotw', 'score', 'game'],
					'TeamB' => ['fields' => 'name'],
					'Competition' => ['fields' => ['name', 'country']]
				],
				'awayTeam' => [
					'fields' => ['date', 'id', 'week_id', 'gotw', 'score', 'game'],
					'TeamA' => ['fields' => 'name'],
					'Competition' => ['fields' => ['name', 'country']]
				]
			]
		];
		
		// create a single list of matches by merging home and away arrays
		$data = $this->Team->find('first', $arr);
		$team = $data['Team']['name'];
		$list = array_merge($data['homeTeam'], $data['awayTeam']);

		$summary = [
			'P' => 0,
			'W' => 0,
			'D' => 0,
			'L' => 0,
			'GF' => 0,
			'GA' => 0,
			'GD' => 0,
			'PTS' => 0,
		];
		foreach ($list as $l) {
			$summary['P']++;
			if (!is_null($l['score']) && $l['score'] != 'P-P' && $l['score'] != 'A-A') {
				list($a, $b) = explode('-', $l['score']);
				if ($a == $b) { // a draw
					$summary['D']++;
					$summary['GF'] += $a;
					$summary['GA'] += $a;
				} else {
					if (array_key_exists('TeamB', $l)) { // team in question is at home
						$summary['GF'] += $a;
						$summary['GA'] += $b;
						if ($a > $b) {
							$summary['W']++;
						} else {
							$summary['L']++;
						}
					} else {
						$summary['GF'] += $b;
						$summary['GA'] += $a;
						if ($a > $b) {
							$summary['L']++;
						} else {
							$summary['W']++;
						}
					}
				}
			}
		}
		$summary['GD'] = ($summary['GF'] - $summary['GA']);
		$summary['PTS'] = ($summary['W'] * 3 + $summary['D']);

		// custom sort the array by date
		uasort($list, function($x,$y) {
			return ($x['date'] > $y['date']) ? -1 : 1 ;
		});
		$this->set('team', $team);
		$this->set('list', $list);
		$this->set('summary', $summary);

	} // end view

	public function add() {
	// shows a form to add a team to the database
	// if team already exists, notify the user

		$teams_list = $this->Team->find('list', ['fields' => 'name']);

		if ($this->request->is('post')) {
			// check to see if that team exists already (assuming javascript didn't work)
			if (in_array($this->request->data['Team']['name'], $teams_list)) {
					$this->Session->setFlash(__('There is already a team with that name'), 'custom-flash', ['myclass' => 'alert-box warning']);
			} else {
				if ($this->Team->save($this->request->data)) {
					$this->Session->setFlash(__('Team \'%s\' Saved', $this->request->data['Team']['name']), 'custom-flash', ['myclass' => 'alert-box success']);
				} else {
					$this->Session->setFlash(__('There was a problem saving that team'), 'custom-flash', ['myclass' => 'alert-box warning']);
				}
			}
		}

	} // end add

} // end class