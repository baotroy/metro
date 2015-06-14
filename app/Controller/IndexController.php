<?php
App::uses('AppController', 'Controller');
App::uses('Metro', 'Lib');

class IndexController extends AppController {
	public $uses = array('Artist', 'Album', 'Lyric');
	function index(){
		exit();
		$this->get_artists();
		echo 'done...';
		exit;
	}
	function get_artists() {
		set_time_limit(0);
		foreach (Constant::$CHARS as $char) {
			$arts = $this->__get_artists_by_char($char);
			$this->Artist->saveMany($arts);	
		}
	}

	function __get_artists_by_char($char){
		$num_artist = 0;	
		$num_page = 1;	
		$current_page =1;
		$url = URL_ART;
		$url .= $char.MS.$current_page.DOT.PAGE_SUFFIX;
		$metro = new Metro;
		$html  = $metro->getPage($url);

		//lay so luong ca si trong alpha page
		$num_artist = $metro->getTotalRecordOfArtist($html);
		$num_page = $metro->getNumOfPage($num_artist);

		$arts = array();
		for($current_page = 1; $current_page <= $num_page; $current_page++){
			$url = URL_ART.$char.MS.$current_page.DOT.PAGE_SUFFIX;
			$html  = $metro->getPage($url);
			$db = $metro->getTotalListOfArtistAlpha($html, $char);
			$arts = array_merge($arts, $db);
		}
		return $arts;
	}
}
