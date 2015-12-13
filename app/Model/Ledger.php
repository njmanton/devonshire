<?php

// nothing really much for this model to do, except define relationship with User model

class Ledger extends AppModel {
	public $name = 'Ledger';
	public $belongsTo = 'User';
	public $actsAs = ['Containable'];

}