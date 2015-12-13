<?php

/* /app/View/Helpers/ScoreHelper.php */

App::uses('AppHelper', 'View/Helper');

class ScoreHelper extends AppHelper {

	function aus($value) {
		if (!$value) return '-';
		return ($value<0) 
			? ('<div class="ausneg">-£' . number_format(abs((double)$value),2) . '</div>') 
			: ('<div class="auspos">£' . number_format((double)$value,2) . '</div>');
	}

	public function calc($pred, $res, $joker, $gotw) {

		if (!$pred || !$res || $res == 'P-P' || $res == 'A-A') return 0;

		$score = 0;
		list($pa,$pb) = explode('-', $pred);
		list($ra,$rb) = explode('-', $res);

		if (($pa==$ra) && ($pb==$rb)) {
			$score = 5;
		} elseif (($ra-$rb) == ($pa-$pb)) {
			$score = 3;
		} elseif ($this->sgn($ra-$rb) == $this->sgn($pa-$pb)) {
			$score = 1;
		}

		$score *= (1+$joker+$gotw);
		return $score;

	} // end calc

	public function sgn ($num) {
		
		if ($num==0) {
			return 0;
		} else {
			return ($num>0) ? 1 : -1;
		}

	} // end sgn

	function getmin($goal_count) {
	// function to return array keys of all values that match the minimum value in an array
		$matches = [];
		$min = min($goal_count);
		foreach($goal_count as $k=>$g) {
			if ($g==$min) $matches[]=$k;
		}
		return $matches;

	} // end getmin

	function generate_random_password($length=8) {
	//--------------------------------------------------------------------------
	//	name:				generate_random_password
	//	desc:				returns a randomly created password
	//	arguments:	length - default of 8
	//	returns:		string
	//--------------------------------------------------------------------------

		$pass = '';
		$string = '';
		$possible = '2346789abcdefghjkmnpqrtuvwxyzABCDEFGHJKLMNPQRTUVWXYZ';
		for ($p=0;$p<$length;$p++) {
			$string .= $possible[mt_rand(0, strlen($possible))];
		}

		return $string;

	} // end generate_random_password



}

