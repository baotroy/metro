<?php
App::uses('AppController', 'Controller');
App::uses('Metro', 'Lib');

class IndexController extends AppController {
	public $uses = array('Artist', 'Album', 'Lyrics', 'Featured');
	public $components = array('Common');
	function beforeFilter(){
		set_time_limit(0);
	}
	function index(){

		$saved = $this->get_artists();
		echo '<meta charset="utf-8">';
		echo '<pre>';print_r($saved); exit;
		// $arts[13198] = 'evanescence';
		// $this->get_album_tracks_lyrics($arts);
		exit;
	}

	/*
		http://www.metrolyrics.com/a1-albums-list.html
		ML.artistname.MS.
	*/
	function get_album_tracks_lyrics($list_arts = array()){

		// $arts = $this->Artist->getAll('list', array('id', 'link'), 1);
		$metro = new Metro;
		
		foreach ($list_arts as $art_id => $value) {
			$current_page = 0;
			$html;
			do{
				$current_page++;
				$url = ML.$value.MS.ALBUM.MS.'list'.MS.$current_page.DOT.PAGE_SUFFIX;
				$html = $metro->getPage($url);
				$pagers = $metro->get_pager($html);
				$data[$art_id][] = $metro->get_albums_and_tracks($html);
			}while($this->Common->is_next_page($pagers, $current_page));
			$feated_tracks[$art_id] = $metro->get_featured_tracks($html);
		}
		$saved_albums = 0;
		$saved_tracks = 0;
		foreach ($data as $art_id => $artist) {
			foreach ($artist as $page_key => $page) {
				$albums = $page['albums'];
				$tracks = $page['tracklists'];
				foreach ($albums as $album_key => $album) {

					$image_name = $this->Common->get_image_file_name($album['image']);

					if($this->Album->saveAll(array('name' => $album['name'], 'num_of_tracks' => $album['num_of_tracks'],
												'artist' => $art_id, 'year' => $album['year'], 'genre' => $album['genre'], 'image' => $image_name))){
						$album_id = $this->Album->id;
						$saved_albums++;
						if(@$tracks[$album_key]){
							foreach ($tracks[$album_key] as $track_key => $track) {
								$saved_tracks++;

								$tracks[$album_key][$track_key]['album'] = $album_id;
								$tracks[$album_key][$track_key]['artist'] = $art_id;
								$lyrics = $metro->get_lyrics($track['link']);
								
								$tracks[$album_key][$track_key]['link'] = $this->Common->shortenLyricsUrl($track['link']);
								$tracks[$album_key][$track_key]['content'] = $lyrics['lyrics'];
								$tracks[$album_key][$track_key]['writer'] = $lyrics['writer'];
								$tracks[$album_key][$track_key]['publisher'] = $lyrics['publisher'];
							}
							$this->Lyrics->saveAll($tracks[$album_key]);
						}
					}
				}
			}
		}
		//save featured tracks
		foreach ($feated_tracks as $art_id => $tracks) {
			foreach ($tracks as $track_key => $track) {
				$master_artist = $this->Artist->get_artist_from_name($track['master_artist'], array('id'));

				$feated_tracks[$art_id][$track_key]['artist'] = $master_artist['id'];
				$feated_tracks[$art_id][$track_key]['featured'] = $art_id;
				
				$lyrics = $metro->get_lyrics($track['link']);
				$feated_tracks[$art_id][$track_key]['link'] = $this->Common->shortenLyricsUrl($track['link']);
				$feated_tracks[$art_id][$track_key]['content'] = $lyrics['lyrics'];
				$feated_tracks[$art_id][$track_key]['writer'] = $lyrics['writer'];
				$feated_tracks[$art_id][$track_key]['publisher'] = $lyrics['publisher'];
				$saved_tracks++;
			}$this->Featured->saveAll($feated_tracks[$art_id]);
		}
		return array('saved_tracks' => $saved_tracks, 'saved_albums' => $saved_albums);
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
		$count = 0;
		foreach (Constant::$CHARS as $char) {
			$arts = $this->__get_artists_by_char($char);
			$this->Artist->saveMany($arts);	
			$count += count($arts);
		}
		return $count;
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
