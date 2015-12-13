<?php

class Team extends AppModel {
	public $name = 'Team';
	public $hasMany = [
		'homeTeam' => [
			'className' => 'Match',
			'foreignKey' => 'teama_id'
		],
		'awayTeam' => [
			'className' => 'Match',
			'foreignKey' => 'teamb_id'
		],
		'Khistory'
	];
	public $actsAs = ['Containable'];

}