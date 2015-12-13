<?php

class Competition extends AppModel {
	public $name = 'Competition';
	public $hasMany = ['Match'];
	public $actsAs = ['Containable'];

}