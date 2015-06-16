<?php

App::uses('AppModel', 'Model');

class Album extends AppModel {
	public $name = 'Album';
	public $useTable = 'albums';

	function getAll($type = 'all', $fields = array('*'), $limit = -1){
		return $this->find($type, array('fields'=> $fields, 'limit' => $limit));
	}

	function getByArtist($artist_id){
		return $this->find('all', array('fields'=> array('id', 'name', 'year'),
										'conditions' => array('artist' =>$artist_id)));
	}


}
