<?php

class CompetitionsController extends AppController {
	public $name = 'Competitions';
	public $helpers = ['Html','Form'];

	public function index() {
	// shows a list of all competitions

		$arr = [
			'fields' => ['name', 'id', 'country'],
			'contain' => ['Match.id', 'Match.game'],
			'order' => 'id',
		];
		$this->set('comps', $this->Competition->find('all', $arr));

	} // end index

	public function show() {
		// this action handles ajax requests for competition names

		if ($this->request->is('ajax')) {
			$this->disableCache();
			$this->autoRender = false;
			$comps = $this->Competition->find('all', [
				'fields' => ['id', 'name', 'country'],
				'conditions' => ['name LIKE ' => '%'.$_GET['term'].'%'],
				'recursive' => 0
			]);

			foreach ($comps as $c) {
				$new[] = [
					'id' => $c['Competition']['id'], 
					'value' => $c['Competition']['name'],
					'country' => $c['Competition']['country']
				];
			}
			echo json_encode($new);
		}

	} // end show

	public function view($id = null) {
	// shows a particular competition
	// includes all matches within that competition

		if ($this->request->params['id']) {
			$cid = $this->request->params['id'];
		} elseif ($id) {
			$cid = $id;
		}

		$arr = [
			'conditions' => ['id' => $cid],
			'contain' => [
				'Match' => [
					'fields' => ['date', 'id', 'score', 'week_id'],
					'order' => ['date DESC'],
					'TeamA' => ['fields' => 'name'],
					'TeamB' => ['fields' => 'name']
				]
			]
		];

		$matches = $this->Competition->find('first', $arr);

		if (empty($matches) || is_null($cid)) {
			$this->flash(__('Sorry, there was no matching competition'), $this->referer, 2);
		} else {
			$this->set('matches', $matches);
		}
		
	} // end view

	public function add() {
	// adds a competition to the database

		$comps_list = $this->Competition->find('list', ['fields' => 'name']);

		if ($this->request->is('post')) {
			// check to see if that team exists already (assuming javascript didn't work)
			if (in_array($this->request->data['Competition']['name'], $comps_list)) {
				$this->Session->setFlash(__('There is already a competition with that name in the database'), 'custom-flash', ['class' => 'alert-box warning']);
			} else {
				if ($this->Competition->save($this->request->data)) {
					$this->Session->setFlash(__('Competition \'%s\' Saved', $this->request->data['Competition']['name']), 'custom-flash', ['myclass' => 'alert-box success']);
				} else {
					$this->Session->setFlash(__('There was a problem saving that competition'), 'custom-flash', ['myclass' => 'alert-box warning']);
				}
			}
		}

		$this->set('comps', $comps_list);

	} // end add
	
} // end class