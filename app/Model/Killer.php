<?php
class Killer extends AppModel {
	public $name = 'Killer';
	public $hasMany = ['Kentry', 'Khistory'];
	public $belongsTo = [
		'User',
		'Week' => [
			'classname' => 'Week',
			'foreignKey' => 'start_week'
		]
	];
	public $actsAs = ['Containable'];

	public function getKiller($game, $userid) {
		// this function calculates the killer table for a given week

		$sql = 'SELECT 
							K.id,
							K.pred,
							K.killer_id,
							K.lives,
							K.round_id,
							K.week_id,
							K.match_id,
							U.id,
							U.username,
							M.id,
							M.`date`,
							M.score,
							TA.name,
							TB.name,
							W.`start`,
							W.id
							FROM kentries K
							INNER JOIN users U on U.id = K.user_id
							LEFT JOIN matches M on M.id = K.match_id
							INNER JOIN weeks W on W.id = K.week_id
							LEFT JOIN teams TA on TA.id = M.teama_id
							LEFT JOIN teams TB on TB.id = M.teamb_id
							WHERE killer_id = ?
							ORDER BY round_id DESC, lives DESC';
		$db = $this->Kentry->getDataSource();
		$data = $db->fetchAll($sql, [$game]);

		if (empty($data)) {
			return null;
		}

		// transform the returned data
		$killer = [];
		foreach($data as $d) {
			if (isset($d['M']['score'])) {
				list($h, $a) = explode('-', $d['M']['score']);
				if ($h > $a) {
					$res = '1';
				} elseif ($h < $a) {
					$res = '2';
				} elseif ($h == $a) {
					$res = 'X';
				}
			}
			/*if (isset($d['K']['pred'])) {
				$pred = $d['K']['pred'];
			}*/
			// is the score correct
			$correct = true;
			$pred = '';

			if (isset($d['K']['pred'])) {
				if ($d['K']['pred']=='1') {
					$pred = 'Home';
				} elseif ($d['K']['pred']=='2') {
					$pred = 'Away';
				} elseif ($d['K']['pred']=="X") {
					$pred = 'Draw';
				}
			}

			if (isset($d['M']['score'])) {
				$correct = ($res == $d['K']['pred']);
			}

			// set up dates
			$now = new DateTime();
			$wd = new DateTime($d['W']['start']);
			$wd->add(new DateInterval(DEADLINE_OFFSET));
			$expired = ($now >= $wd);

			// is there a real match with a prediction entered?
			$match = (($d['M']['id']) && $d['K']['pred']);

			// if expired, show the prediction if set, otherwise 'no match'
			// if not show the match only for that user, otherwise 'match entered'

			$label = '';
			if ($match) {
				if ($expired) {
					$label = __('%s v %s', $d['TA']['name'], $d['TB']['name']);
				} else {
					$label = ($userid == $d['U']['id']) ? __('%s vs. %s', $d['TA']['name'], $d['TB']['name']) : 'match entered';
				}
			} else {
				$label = 'no match entered';
			}

			// user has lost a life if the result is known and the prediction's wrong, or we've hit the deadline with no prediction
			$lostlife = (($expired && !$match) || !$correct);

			$rid = $d['K']['round_id'];
			$uid = $d['U']['id'];

			$killer[$rid]['week'] = $d['W']['id'];
			$killer[$rid]['start'] = $d['W']['start'];
			$killer[$rid]['expired'] = $expired;
			$killer[$rid]['rows'][$uid] = [
				'kid' => $d['K']['id'],
				'name' => $d['U']['username'],
				'date' => $d['M']['date'],
				'score' => $d['M']['score'],
				'pred' => $pred,
				'mid' => $d['K']['match_id'],
				'label' => $label,
				'lives' => $d['K']['lives'],
				'lostlife' => $lostlife,
				'dead' => (($d['K']['lives'] < 2 ) && $lostlife)
			];

		}

		return $killer;

	} // end getKiller
}