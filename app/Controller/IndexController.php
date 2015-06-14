<?php
App::uses('AppController', 'Controller');
App::uses('Metro', 'Lib');

class IndexController extends AppController {
	public $uses = array('Artist', 'Album', 'Lyrics');
	public $components = array('Common');
	function beforeFilter(){
		set_time_limit(0);
	}
	function index(){

//		$this->get_artists();
		exit;
	}

	/*
		http://www.metrolyrics.com/a1-albums-list.html
		ML.artistname.MS.
	*/
	function test(){

		// $arts = $this->Artist->getAll('list', array('id', 'link'), 1);
		// echo '<meta charset="utf-8">';
		// echo '<pre>';print_r($); exit;
//		$arts = array('23773', 'linkin-park');
		//var_dump(strpos('/music/the-hunting-party/linkin-park', ML));exit;
		$arts[23773] = 'linkin-park';
		$metro = new Metro;
		
		$metro->get_albums_and_tracks2('http://www.metrolyrics.com/linkin-park-albums-list.html');
		exit;
		foreach ($arts as $art_id => $value) {
			$current_page = 0;
			do{
				$current_page++;
				$url = ML.$value.MS.ALBUM.MS.'list'.MS.$current_page.DOT.PAGE_SUFFIX;
				$html = $metro->getPage($url);
				$pagers = $metro->get_pager($html);
				$data[$art_id][] = $metro->get_albums_and_tracks($html);
			}while(false);
			//while($this->Common->is_next_page($pagers, $current_page));
		}
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($data); exit;
		foreach ($data as $art_id => $artist) {
			foreach ($artist as $page_key => $page) {
				$albums = $page['albums'];
				$tracks = $page['tracklists'];
				foreach ($albums as $album_key => $albumName) {
					$album_meta = $this->Common->exact_album_name_year($albumName);
					if($this->Album->saveAll(array('name' => $album_meta['album_name'], 'artist' => $art_id, 'year' => $album_meta['album_year']))){
						//album id vua dc save
						$album_id = $this->Album->id;
						foreach ($tracks[$album_key] as $key => $value) {
							$tracks[$album_key][$key]['album'] = $album_id;
							$tracks[$album_key][$key]['artist'] = $art_id;
							$lyrics = $metro->get_lyrics($value['link_ref']);
							
							$tracks[$album_key][$key]['content'] = $lyrics['lyrics'];
							$tracks[$album_key][$key]['writer'] = $lyrics['writer'];
							$tracks[$album_key][$key]['publisher'] = $lyrics['publisher'];
						}
						$this->Lyrics->saveAll($tracks[$album_key]);
					}
				}
			}
		}exit;
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($data); exit;
		$metro = new Metro;
		
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($r); exit;
	}
	function lists(){
		$arts = $this->Artist->getAll('list', array('name'), 1);
		$albums = array();
		$tracks = array();
		foreach ($arts as $key => $value) {
			$albums[$key] = $this->Album->getByArtist($key);
			foreach ($albums[$key] as $al_key => $album) {
				$tracks[$album['Album']['id']] = $this->Lyrics->getByAlbum($album['Album']['id']);
			}
		}
		$this->set('arts', $arts);
		$this->set('albums', $albums);
		$this->set('tracks', $tracks);
	}
	function get_artists() {
		set_time_limit(0);
		//foreach (Constant::$CHARS as $char) {
			$arts = $this->__get_artists_by_char(1);
			$this->Artist->saveMany($arts);	
		//}
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
