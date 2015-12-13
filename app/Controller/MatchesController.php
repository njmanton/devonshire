<?php

class MatchesController extends AppController {
	public $name = 'Matches';
	public $helpers = ['Html', 'Form'];

	public function index() {
		// test ajax function

		$qry = 'SELECT W.id, COUNT(M.id) AS c FROM weeks W INNER JOIN matches M ON W.id = M.week_id GROUP BY W.id ORDER BY W.id DESC';

		$data = $this->Match->query($qry);

		foreach($data as $m) {
			$matches[$m['W']['id']]['count'] = $m[0]['c'];
		}

		$this->set('matches', $matches);

	} // end index

	public function add() {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;

			$success = false;
			$data = $this->request->data;
			$game = $data['game'];
			if (isset($data['gm-toggle']) && $data['gm-toggle'] != '') {
				$game = ($game | 1);
			}
			if (isset($data['odd-toggle']) && $data['odd-toggle'] != '')  {
				$game = ($game | 2);
			}
			$gotw = (isset($data['gotw']) && $data['gotw'] == 'on') ? 1 : 0;


			$tosave = ['Match' => [
				'id' => $data['matchid'], //need to provide edits too
				'date' => date('Y-m-d', strtotime($data['date'])),
				'week_id' => $data['week'],
				'teama_id' => $data['teama_id'],
				'teamb_id' => $data['teamb_id'],
				'competition_id' => $data['comp_id'],
				'game' => $game,
				'gotw' => $gotw,
				'odds1' => $data['odds1'],
				'oddsX' => $data['oddsX'],
				'odds2' => $data['odds2'],
			]];
			if ($this->Match->save($tosave)) {
				$success = $this->Match->id;
				// when adding a match, ensure the status for that week is correctly set
				$this->Match->Week->id = $data['week'];
				$this->Match->Week->saveField('status', 0);
			} 
			echo json_encode([$success]);
			exit;
		}

	} // end add

	public function delete($id = null) {

		// deletes a match from the database. Can only be called through an ajax request
		if ($this->request->is('ajax')) {
			if (!is_null($id) && is_numeric($id)) {
				//$m = $this->Match->read(null, $id);
				if ($this->Match->delete($id)) {
					$success = true;
					$this->log(__('Deleted match id: %s', $id), 'admin');
				} else {
					$success = false;
				}
			}
			echo json_encode(['success' => true]);
			exit;
		} else {
			throw new MethodNotAllowedException();
		}

	} // end delete

	public function edit($week = null) {

		// set info about the week
		$arr = [
			'conditions' => ['id' => $week],
			'recursive' => 0
		];
		$thisweek = $this->Match->Week->find('first', $arr);

		$sd = new DateTime($thisweek['Week']['start']);
		$now = new DateTime();

		if ($thisweek['Week']['status'] == 1 || ($now > $sd))  {
			//$this->flash(__('This week cannot now be edited'), $this->referer, 2);
		}

		// check week hasn't started yet

		$this->set('week', $thisweek);

		$arr = [
			'conditions' => ['week_id'=>$week],
			'contain' => [
				'TeamA.name',
				'TeamB.name',
				'Competition.name',
				'Prediction.id',
				'Bet.id'
			],
			'order' => ['date ASC']
		];

		$this->set('matches', $this->Match->find('all', $arr));

	} // end edit
	
	public function week($week = null) {
		// get matches for particular week

		if ($this->request->is('ajax')) {
		
			$this->autoRender = false;
			$arr = [
				'fields' => ['id', 'date', 'week_id', 'gotw', 'score', 'game'],
				'conditions' => ['week_id' => $week],
				'contain' => [
					'TeamA' => ['fields' => 'name'],
					'TeamB' => ['fields' => 'name'],
					'Competition'=> ['fields' => ['name', 'country']]
				]
			];

			$m = $this->Match->find('all', $arr);

			echo json_encode($m);
			exit;
		}

	} // end week

	public function view($id = null) {
		// function to display a particular match

		if (!empty($this->request->params['id'])) {
			$mid = $this->request->params['id'];
		} elseif ($id) {
			$mid = $id;
		}

		if ($this->request->is('ajax')) {
		// if this is an ajax call to get match details for /matches/edit
			$this->autoRender = false;
			$arr = [
				'conditions' => ['Match.id' => $mid],
				'contain' => [
					'TeamA' => ['fields' => 'name'],
					'TeamB' => ['fields' => 'name'],
					'Competition'=> ['fields' => ['name', 'country']]
				]
			];

			$m = $this->Match->find('first', $arr);

			echo json_encode($m);
			exit;
		}

		// define the query parameters to get information on a given match
		$arr = [
			'conditions' => ['Match.id' => $mid],
			'contain' => [
				'TeamA' => ['fields' => 'name'],
				'TeamB' => ['fields' => 'name'],
				'Competition'=> ['fields' => 'name'],
				'Prediction' => [
					'fields' => ['id', 'joker', 'pred', 'points'],
					'User' => ['fields' => 'username']
				]
			]
		];

		// get the data on the match and predictions
		$m = $this->Match->find('first', $arr);

		// get the data on the bets for the match
		$bets = $this->Match->Bet->calculateBets('match', $mid);

		if (is_null($mid) || empty($m)) {
			$this->flash('Invalid or no match Selected','/matches/', 2);
		} else {

			if (!is_null($m['Match']['score'])) {
				list($a, $b) = explode('-', $m['Match']['score']);
				$m['TeamA']['goals'] = $a;
				$m['TeamB']['goals'] = $b;
			} else {
				$m['TeamA']['goals'] = '';
				$m['TeamB']['goals'] = '';
			}

			$this->set('m', $m);
			$this->set('bets', $bets);
			
		}
	
	} // end view

	public function results($week = null) {
		// function to enter and update the match results for a given week
		
		// ensure user is an admin
		if ($this->Auth->user('admin')) {
			// if the data has been posted from the form
			if ($this->request->is('post')) {
				// loop through each match result
				foreach($this->request->data as $k=>$d) {
					// if it's been changed
					if (isset($d['dirty']) && $d['dirty'] == 1) {
						$tosave = ['Match' => [
							'id' => $k,
							'score' => $d['score']
						]];
						// try to save it
						if ($this->Match->save($tosave)) {
							$this->log(json_encode($tosave), 'admin');

							$game = $this->Match->field('game', ['id' => $k]);
							
							// if killer game update killer model
							if ($game & 4) {
								$ret = $this->Match->Kentry->updateKiller($k);
							} 
							// if tipping _or_ goalmine, update respective models
							if ($game & 3) {
								$ret = $this->Match->updateModels($k);
								$ret = $this->Match->Week->updateLeague($week);
								$ret = $this->Match->Week->Place->updatePlaces($week);
							}
						} else {
							// didn't save update
						}
					}
				}
				
			}

			$arr = [
				'fields' => ['id', 'date', 'score', 'game', 'gotw'],
				'contain' => [
					'TeamA.name',
					'TeamB.name',
					'Competition.name'
				],
				'conditions' => ['week_id' => $week],
				'order' => 'date ASC'
			];

			$r = $this->Match->find('all', $arr);

			if (!empty($r)) {
				$this->set('results', $r);
				$this->set('status', $this->Match->Week->field('status', ['id' => $week]));
			} else {
				$this->flash(__('Can\'t return data for this week'), '/', 2);
			}

		} else {
			$this->flash(__('Only admins can edit results of matches'), '/', 2);
		}

	} // end results

	public function scoreFreq() {

		$sql = 'SELECT score, COUNT(score) AS freq 
						FROM matches 
						WHERE score IS NOT NULL AND score NOT IN ("P-P", "A-A")
						GROUP BY score 
						ORDER BY 2 DESC';
		$data_score = $this->Match->query($sql);

		$sql = 'SELECT pred, COUNT(pred) AS freq 
						FROM predictions 
						WHERE pred IS NOT NULL
						GROUP BY pred 
						ORDER BY 2 DESC';
		$data_pred = $this->Match->Prediction->query($sql);

		$ajax = [];
		$totals = ['score' => 0, 'pred' => 0];

		foreach ($data_score as $d) {
			$ajax[$d['matches']['score']]['score'] = $d[0]['freq'];
			$totals['score'] += $d[0]['freq'];
			$ajax[$d['matches']['score']]['pred'] = 0;
		}

		foreach ($data_pred as $d) {
			$ajax[$d['predictions']['pred']]['pred'] = $d[0]['freq'];
			$totals['pred'] += $d[0]['freq'];
		}

		foreach ($ajax as &$a) {
			if (isset($a['pred'])) {
				//$a['pred'] /= $totals['pred'];
				$a['pred'] = number_format($a['pred'] / $totals['pred'] * 100, 4);
			}
			if (isset($a['score'])) {
				//$a['score'] /= $totals['score'];
				$a['score'] = number_format($a['score'] / $totals['score'] * 100, 4);
			}
		}

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			return (json_encode($ajax));

		} else {

		}

	} // end scoreFreq

	public function byScore($score = null) {

		if (is_null($score)) {
			$this->flash(__('No score selected'), $this->referer, 2);
		}

		$arr = [
			'fields' => ['id', 'date', 'score', 'game'],
			'order' => 'date DESC',
			'conditions' => ['score' => $score],
			'contain' => ['TeamA.name',	'TeamB.name', 'Competition.name']
		];

		$b = $this->Match->find('all', $arr);
		$this->set('matches', $b);

	} // end byScore

	public function beforeFilter() {

		parent::beforeFilter();
		$this->Auth->allow('byScore', 'scoreFreq');
		
	} // end beforeFilter
	
} // end class