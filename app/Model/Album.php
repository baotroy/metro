<?php

App::uses('AppModel', 'Model');

class Album extends AppModel {
	public $name = 'Album';
	public $useTable = 'albums';

	function getAll(){
		return $this->find('all', array('fields'=> array('id', 'Album.name', 'year', 'artist', 'Artist.name')));
	}

	function getByArtist($artist_id){
		return $this->find('all', array('fields'=> array('id', 'name', 'year'),
										'conditions' => array('artist' =>$artist_id)));
	}
}
