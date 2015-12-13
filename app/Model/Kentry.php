<?php

App::uses('CakeEmail', 'Network/Email');

class Kentry extends AppModel {
	public $name = 'Kentry';
	public $belongsTo = ['User','Match', 'Week', 'Killer'];
	public $actsAs = ['Containable'];

	public function updateKiller($matchid = null) {
		// this function is called whenever a match score is updated for a killer game
		// it checks the prediction for that killer round, and if the player is still alive
		// adds a new record for the following week for that player

		// get the match(es)
		$arr = [
			'contain' => ['Match.score', 'User.username', 'User.email'],
			'conditions' => ['match_id' => $matchid]
		];

		$kentries = $this->find('all', $arr);

		if (empty($kentries)) {
			// couldn't find match, so not a killer game
			return false;
		}

		foreach ($kentries as $k=>$r) {

			list($h, $a) = explode('-', $r['Match']['score']);

			if ($h > $a) {
				$res = '1';
			} elseif ($h < $a) {
				$res = '2';
			} elseif ($h == $a) {
				$res = 'X';
			}

			$lives = $r['Kentry']['lives'];
			if (isset($r['Kentry']['pred']) && ($res == $r['Kentry']['pred'])) {
				// prediction is correct
				// maybe do something here to give bonus lives...? eg
				// $lives = max(3,++$lives);
			} else {
				$lives--;
				// send email to player
				$email = new CakeEmail('default');
				$from = ['noreply@goalmine.eu' => 'Killer Goalmine'];
				$email->from($from)
							->to($r['User']['email'])
							->subject('Killer GoalMine update')
							->template('killer_result_wrong')
							->viewVars([
								'username' => $r['User']['username'],
								'game' => $r['Kentry']['killer_id'],
								'round' => $r['Kentry']['round_id'],
								'lives' => $lives
								])
							->send();
				}

			if ($lives > 0) {
				// need to check if a record exists already
				$arr = [
					'killer_id' => $r['Kentry']['killer_id'],
					'round_id' => $r['Kentry']['round_id'] + 1,
					'Kentry.user_id' => $r['Kentry']['user_id']
				];
				$this->deleteAll($arr);

				// create a new record for next week in the Kentry table
				$this->create();
				$tosave = ['Kentry' => [
					'killer_id' => $r['Kentry']['killer_id'],
					'round_id' => $r['Kentry']['round_id'] + 1,
					'week_id' => $r['Kentry']['week_id'] + 1,
					'user_id' => $r['Kentry']['user_id'],
					'lives' => $lives
				]];
				if ($this->save($tosave)) {
					$this->log(json_encode($tosave), 'admin');

					// and also add those teams to the killer history for that person
					$m = $this->Match->findById($matchid, ['recursive' => 0]);

					$todelete = [
						'killer_id' => $r['Kentry']['killer_id'],
						'week_id' => $r['Kentry']['week_id'],
						'Khistory.user_id' => $r['Kentry']['user_id']
					];
					$this->User->Khistory->deleteAll($todelete);

					// create an entry for the home team
					$this->User->Khistory->create();
					$tosave = ['Khistory' => [
						'killer_id' => $r['Kentry']['killer_id'],
						'week_id' => $r['Kentry']['week_id'],
						'user_id' => $r['Kentry']['user_id'],
						'team_id' => $m['Match']['teama_id']
					]];
					$this->User->Khistory->save($tosave);

					// and one for the away team
					$tosave['Khistory']['team_id'] = $m['Match']['teamb_id'];
					$this->User->Khistory->create();
					$this->User->Khistory->save($tosave);

					$email = new CakeEmail('default');
					$from = ['noreply@goalmine.eu' => 'Killer Goalmine'];
					$email->from($from)
							->to($r['User']['email'])
							->subject('Killer GoalMine update')
							->template('killer_result_alive')
							->viewVars([
								'username' => $r['User']['username'],
								'game' => $r['Kentry']['killer_id'],
								'round' => $r['Kentry']['round_id']+1,
								'lives' => $lives
								])
							->send();

				} else {
					// couldn't save the data
					$this->log('problem saving killer data', 'admin');
				}
				
			} else {
				// no lives left - dead!
				// send an email to the player
				$email->from($from)
							->to($r['User']['email'])
							->subject('Killer GoalMine update')
							->template('killer_result_dead')
							->viewVars([
								'username' => $r['User']['username'],
								'game' => $r['Kentry']['killer_id'],
								])
							->send();
			}

			$this->checkNoMatchEntered($r['Kentry']['killer_id'], $r['Kentry']['week_id']);
		}

	} // end updateKiller

	public function checkNoMatchEntered($kid = null, $wid = null) {
		// searches for kentries for given week with no prediction
		// loop through kentries, if one _doesn't_ have an entry in the subsequent week, then add it
		$arr = [
			'contain' => ['Match.score', 'User.username', 'User.email'],
			'conditions' => ['Kentry.week_id' => $wid, $kid => null, 'pred' => null]
		];
		$nullKentries = $this->find('all', $arr);
debug ($nullKentries);
		foreach ($nullKentries as $k=>$v) {
			$uid = $v['Kentry']['user_id'];
			$rid = $v['Kentry']['round_id'];
			if ($v['Kentry']['lives'] < 2) {
				// dead!
				$email = new CakeEmail('default');
				$email->from($from)
							->to($v['User']['email'])
							->subject('Killer GoalMine update')
							->template('killer_result_dead')
							->viewVars([
								'username' => $v['User']['username'],
								'game' => $v['Kentry']['killer_id'],
								])
							->send();
			}	elseif (!$this->findByUserIdAndWeekId($uid, ($wid + 1))) {
				$tosave = ['Kentry' => [
					'killer_id' => $kid,
					'round_id' => $rid + 1,
					'week_id' => $wid + 1,
					'user_id' => $uid,
					'lives' => $v['Kentry']['lives'] - 1
				]];
				$this->create();
				$this->save($tosave);
			}
		}

	} // end checkNoMatchEntered

} // end class