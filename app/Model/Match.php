<?php

class Match extends AppModel {
	public $name = 'Match';
	public $hasMany = ['Prediction','Bet', 'Kentry'];
	public $belongsTo = [
		'TeamA' => [
			'className' => 'Team',
			'foreignKey' => 'teama_id'
		],
		'TeamB' => [
			'className' => 'Team',
			'foreignKey' => 'teamb_id'
		],
		'Week' => [
			'className' => 'Week',
			'foreignKey' => 'week_id'
		],
		'Competition'
	];
	public $actsAs = ['Containable'];
	
	// define a validation rule that scores must be 2 digits, hyphen, 2 digits
	// or A-A / P-P for abandoned or postponed games
	public $validate = [
		'score' => [
			'rule' => '/^[AP0-9]{1,2}-[AP0-9]{1,2}$/',
			'message' => 'each score must be between 0 and 99',
			'allowEmpty' => true
		]
	];
	
	public function getBets($week, $id) {

		$arr = [
			'fields' => ['id', 'date', 'score', 'odds1', 'odds2', 'oddsX'],
			'conditions' => ['week_id' => $week, 'game & 2 != 0'],
			'contain' => [
				'TeamA' => ['fields' => 'name'],
				'TeamB' => ['fields' => 'name'],
				'Bet' => [
					'fields' => ['id', 'amount', 'prediction'],
					'conditions' => ['user_id' => $id]
				]
			]
		];

		$b = $this->find('all', $arr);
		return $b;

	} // end getBets

	public function updateModels($k) {

		// $k is match id
		// get all predictions with that match id
		// calculate points and update each row
		$arr = [
			'fields' => ['id', 'score', 'gotw'],
			'conditions' => ['id' => $k],
			'contain' => [
				'Prediction' => [
					'fields' => ['id', 'pred', 'joker']
				],
				'Bet' => ['fields' => ['id', 'prediction']]
			]
		];

		$preds = $this->find('all', $arr);
		$preds = $preds[0]; // simplify the array
		$score = $preds['Match']['score'];
		$gotw = $preds['Match']['gotw'];
		// get the result to compare to each bet made
		@list($sa, $sb) = explode('-', $score);
		if ($sa > $sb) {
			$result = '1';
		} elseif ($sa < $sb) {
			$result = '2';
		} elseif ($sa == $sb) {
			$result = 'X';
		}

		// loop through each prediction
		foreach ($preds['Prediction'] as $p) {
			$pts = calc($p['pred'], $score, $p['joker'], $gotw);
			$tosave = ['Prediction' => [
				'id' => $p['id'],
				'points' => $pts
			]];
			if ($this->Prediction->save($tosave)) {
				//$this->log(__('updated score to %s for prediction %s', $pts, $p['id']), 'admin');
			} else {
				$this->log(__('Couldn\'t update score for prediction %s ', $p['id']), 'admin');
			}
		}
		
		// get all bets with that match id
		// calculate right/wrong and update each row
		foreach ($preds['Bet'] as $b) {
			$tosave = ['Bet' => [
				'id' => $b['id'],
				'outcome' => (int)($result == $b['prediction'])
			]];
			if ($this->Bet->save($tosave)) {
				// do something on save
			} else {
				$this->log(__('Couldn\'t update outcome for bet %s ', $p['id']), 'admin');	
			}

		}

	} // end updateModels
	
} // end Class