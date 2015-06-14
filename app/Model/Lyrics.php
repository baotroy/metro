<?php

App::uses('AppModel', 'Model');

class Lyrics extends AppModel {
	public $name = 'Lyrics';
	public $useTable = 'lyrics';

	function getByAlbum($album_id){
		return $this->find('all', array('fields'=> array('*'),
										'conditions' => array('album' =>$album_id)));
	}
}
