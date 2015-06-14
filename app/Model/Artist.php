<?php

App::uses('Model', 'Model');

class Artist extends Model {
	public $name = 'Artist';
	public $useTable = 'artists';

	function getAll($type= 'all',$fields = array(), $limit = 5, $offset = 0){
		return $this->find($type, array('fields' => $fields, 'limit' => $limit, 'offset' => $offset));
	}
}