<?php

class Post extends AppModel {
	public $name = 'Post';
	public $belongsTo = ['User'];
	public $actsAs = ['Containable'];
	
} // end class