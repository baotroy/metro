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
		$arts = $this->Artist->getAll('list', array('id', 'link'), -1, 5117);

		$count = $this->get_banner($arts);
		echo $count;
	}

	/*
		http://www.metrolyrics.com/a1-albums-list.html
		ML.artistname.MS.
	*/
	function agetlyrics(){
		$arts = $this->Artist->getAll('list', array('id', 'link'), 1000);
	
		$count = $this->get_album_tracks_lyrics($arts);
		print_r($count);
	}
	function get_banner($artist_list = array())
	{
		$ignore = array('1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '7.jpg', '8.jpg', '6.jpg', '9.jpg', '10.jpg');
		$count = 0;
		
		foreach ($artist_list as $art_id => $link) {
			$metro = new Metro;
			$link_ref = $metro->get_artist_banner($link);
			$img = $this->Common->get_image_file_name($link_ref);
			if(!in_array($img, $ignore)){
				if(@copy($link_ref, DL_PATH.DIR_PROFILE.$img)){
					$this->Artist->save(array('id' => $art_id, 'cover' => $img));
					$count++;
				}
			}
			
		}
		return $count;
	}

	function get_cover($files = array()){
		$ignore = array('nopic.jpg', 'no-album.jpg');
		foreach ($files as $key => $file) {
			
			if(!in_array($file, $ignore)){
				$source = 'http://netstorage.metrolyrics.com/albums/'.$file;
				@copy($source, DL_PATH.$file);
			}
		}
	}
	function get_album_tracks_lyrics($list_arts = array()){
		$metro = new Metro;
		
		foreach ($list_arts as $art_id => $value) {
			$current_page = 0;
			$html = '';
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

				//kiem tra neu ca si chinh cua bai hat ton tai thi cap nhat vao field artist, neu khong thi ghi ten day du ca si vao featured_name
				if($master_artist){
					$feated_tracks[$art_id][$track_key]['artist'] = $master_artist['id'];
				}
				else{
					$feated_tracks[$art_id][$track_key]['master_featured'] = $track['master_artist'];
				}
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
	
	function get_artists() {
		$count = 0;
		foreach (Constant::$CHARS as $char) {
			$arts = $this->__get_artists_by_char($char);
			$this->Artist->saveMany($arts);	
			$count += count($arts);
		}
		return $count;
	}

	function update_artists() {
		$count = 0;
		foreach (Constant::$CHARS as $char) {
			$arts = $this->__get_artists_by_char($char);
			
			foreach ($arts as $key => $art) {
				if(!$this->Artist->is_exist($art['link'])){
					$this->Artist->create();
					$this->Artist->save($art);
					$this->get_banner($art['link']);
					$count++;	
				}
			}
		}
		return $count;
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
