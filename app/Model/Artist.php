<?php

App::uses('Model', 'Model');

class Artist extends Model {
	public $name = 'Artist';
	public $useTable = 'artists';

	function getAll($type= 'all',$fields = array(), $limit = -1, $offset = 0){
		return $this->find($type, array('fields' => $fields, 'limit' => $limit, 'offset' => $offset));
	}

	function get_artist_from_name($name, $fields = array())
	{
		$res = $this->find('first', array('fields' => $fields, 'conditions' => array('name'=> $name)));
		if($res)
			return $res['Artist'];
		return false;
	}

	function is_exist($link)
	{
		//SELECT * FROM test WHERE texte LIKE '%something%' LIMIT 1
		return $this->find('all', array('fields' => array('*') ,'conditions' => array('link' => $link), 'limit' => 1));
	}
}