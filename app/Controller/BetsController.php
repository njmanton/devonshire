<?php

class BetsController extends AppController {
	public $name = 'Bets';
	public $helpers = ['Html','Form'];

	public function edit($week = null) {
	// function to handle edits to bets

		$id = $this->Auth->user('id');
		
		if ($this->request->is('post')) {
			$updates = $this->Bet->processBets($this->request->data, $id);
			$this->set('updates', $updates);
			// 12/12/2015 - added following two lines to redict user to bets/view rather than bets/edit
			$this->Session->setFlash(__('Bets updated'), 'custom-flash', ['myclass' => 'alert-box success']);
			$this->redirect(['action' => 'view', $week]);
		}

		// work out whether the week in question is over, or not
		$dl = new DateTime($this->Bet->Match->Week->field('start', ['id' => $week]), new DateTimeZone('UTC'));
		$dl->add(new DateInterval(DEADLINE_OFFSET)); // changed to two days - need to revert for next season
		$now = new DateTime();
		// if expired, redirect to the results, otherwise push data to view
		if ($now > $dl) {
			$this->flash('The deadline for this week has expired. Redirecting to results', '/bets/view/' . $week, 2);
		} else {
			$gb = $this->Bet->Match->getBets($week, $id);
			if (empty($gb)) {
				$this->flash('No matches found for this week. Redirecting to home', '/', 2);
			} else {
				foreach($gb as &$i) {
					if (count($i['Bet'])) {
						$i['Bet'] = $i['Bet'][0];
					}
				}
				$this->set('bets', $gb);
				$this->set('deadline', $dl);
			}
		}

	} // end edit

	public function bets_by_match($mid = null) {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$arr = [
				'fields' => ['outcome', 'prediction', 'SUM(amount) AS sum'],
				'conditions' => ['match_id = ' . $mid],
				'group' => ['outcome', 'prediction']
			];
			$bets = $this->Bet->find('all', $arr);
			$json = [];
			foreach ($bets as $b) {
				$json[] = [
					'prediction' => $b['Bet']['prediction'],
					'outcome' => $b['Bet']['outcome'],
					'amount' => $b[0]['sum'] * ($b['Bet']['outcome'] == 1 ? 1 : -1)
				];
			}
			return(json_encode($json));
		} else {
			throw new MethodNotAllowedException();
		}

	} // end bets_by_match

	public function bets_places($wid = null) {

		if ($this->request->is('ajax')) {
			$this->autoRender = false;
			$arr = [
				'fields' => ['rank', 'balance'],
				'contain' => ['User.username'],
				'conditions' => ['week_id = ' . $wid],
				'recursive' => 0,
				'order' => ['rank DESC']
			];
			$bp = $this->Bet->User->Place->find('all', $arr);
			$json = [];
			foreach ($bp as $b) {
				$json['names'][] = __('%s. %s', $b['Place']['rank'], $b['User']['username']);
				$json['values'][] = (float)$b['Place']['balance'];
			}
			return (json_encode($json));

		} else {

			throw new MethodNotAllowedException();

		}

	} // end bets_places

	public function view($id = null) {

		if ($this->request->params['id']) {
			$wid = $this->request->params['id'];
		} elseif ($id) {
			$wid = $id;
		}

		$uid = $this->Auth->user('id');
		$table = $this->Bet->getTable($wid);

		if (empty($table)) {
			$this->flash(__('There are no matches for this week'), '/', 2);
		} else {
			$this->set('players', $this->Bet->getPlayers($wid, $uid));
			$this->set('table', $table);
			$this->set('places', $this->Bet->getStandings($wid));			
			$this->set('week', $this->Bet->Match->Week->find('first', ['recursive' => 0, 'conditions' => __('id = %s', $wid)]));
		}
		
	} // end view

} // end BetsController