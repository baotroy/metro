<?php
App::uses('AppController', 'Controller');
App::uses('Metro', 'Lib');

class PagesController extends AppController {
	public $uses = array('Artist', 'Album', 'Lyric');
	public function display() {
		set_time_limit(0);
		echo '<meta charset="utf-8">';
		echo 'started<br/>...<br/>';
		$a =array();

		foreach (Constant::$CHARS as $char) {
			$arts = $this->get_artists($char);
			$a[$char] = count($arts);
			$this->Artist->saveMany($arts);	
		}
		
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($a); exit;
		echo 'finish!';exit;
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($arts); exit;

	}
	function test(){
		$a =$this->get_artists('d');
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($a); exit;
	}
	function get_artists($char){
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
