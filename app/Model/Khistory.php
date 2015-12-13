<?php
class Khistory extends AppModel {
	public $name = 'Khistory';
	public $belongsTo = ['User', 'Week', 'Team', 'Killer'];
	public $actsAs = ['Containable'];

}