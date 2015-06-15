<?php

App::uses('AppModel', 'Model');

class Featured extends AppModel {
	public $name = 'Featured';
	public $useTable = 'featured';

	function getByAlbum($album_id){
		return $this->find('all', array('fields'=> array('*'),
										'conditions' => array('album' =>$album_id)));
	}
}
