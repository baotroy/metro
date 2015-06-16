<?php 
require_once('phpQuery.php');
class Metro{

	function getPage($url){
		return file_get_contents($url);
	}

	function getElement($input, $tag, $class= ''){
		if($tag == '') return '';
		$sPattern ="";
		$sPattern .= "/<*".$tag;
						
		if($class!=''){
			$sPattern .= ' class=\"'.$class.'\*"';
		}

		$sPattern .='>(.*?)<\/'.$tag.'>/s';
		preg_match_all($sPattern, $input, $matches);
	
		return $matches;
	}

	function relToAbs($text, $base)
	{
	  if (empty($base))
	    return $text;
	  // base url needs trailing /
	  if (substr($base, -1, 1) != "/")
	    $base .= "/";
	  // Replace links
	  $pattern = "/<a([^>]*) " .
	             "href=\"[^http|ftp|https|mailto]([^\"]*)\"/";
	  $replace = "<a\${1} href=\"" . $base . "\${2}\"";
	  $text = preg_replace($pattern, $replace, $text);
	  // Replace images
	  $pattern = "/<img([^>]*) " . 
	             "src=\"[^http|ftp|https]([^\"]*)\"/";
	  $replace = "<img\${1} src=\"" . $base . "\${2}\"";
	  $text = preg_replace($pattern, $replace, $text);
	  // Done
	  return $text;
	}

	function getTotalListOfArtistAlpha($html, $char){
		if(!$html) return false;
		$data = array();
		if(is_array($html)){
			foreach ($html as $key => $value) {
				$data = array_merge($this->getArtistListFromSingleAplpha($value), $data);
			}
		}
		else{
			$data = $this->getArtistListFromSingleAplpha($html, $char);
		}
		return $data;
	}

	//get artist list from single html file
	function getArtistListFromSingleAplpha($page, $char){
		//get tbody tag html
		$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 
		$tbody = $this->getElement($page, 'tbody');
		$tbody = $tbody[0][0];

		$dom = new DOMDocument;
		//load html text to DOM
		@$dom->loadHTML($tbody);
		//GET ALL A tag
		$data = array();
		$links = $dom->getElementsByTagName('a');
		foreach ($links as $link){
		    //Extract and show the "href" attribute. 
		    //get href
		    $href = $link->getAttribute('href');
		    //get a tag value
		    $atagValue = trim($link->nodeValue);
		    $atagValue = trim(substr($atagValue, 0, strrpos($atagValue, 'Lyrics')));
		    $href = $this->shortenAlbumUrl($href);
		    $data[] = array('char' => $char,'link' => $href , 'name' => $atagValue);
		}
		return $data;
	}
	//lấy số lượng ca sĩ trong 1 ký tự Alpha
	function getTotalRecordOfArtist($page){
		$res = $this->getElement($page, 'strong');
		if(!isset($res[1][0])) return 0;
		return trim($res[1][0]);
	}

	function getNumOfPage($num){
		$val = round( (intval($num) / intval(ART_PER_PAGE)), 5);
		$roundzero = round($val, 0);
		if($val > $roundzero) return $roundzero+1;
		return $roundzero;
	}

	//lay danh sach bai hat k theo album
	function get_tracks_by_artist($page){
		//get tbody tag html
		$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 
		$tbody = $this->getElement($page, 'tbody');
		$tbody = $tbody[0][0];

		$dom = new DOMDocument;
		//load html text to DOM
		@$dom->loadHTML($tbody);
		//GET ALL A tag
		$data = array();
		$links = $dom->getElementsByTagName('a');
		foreach ($links as $link){
		    //Extract and show the "href" attribute. 
		    //get href
		    $href = $link->getAttribute('href');
		    //get a tag value
		    $atagValue = trim($link->nodeValue);
		    $atagValue = $this->remove_lyrics_suffix($atagValue);
		    $data[] = array('link' => $href , 'name' => $atagValue);
		}
		return $data;
	}

	//lay danh sach album va track list trong album
	// function get_albums_and_tracks($page){
	// 	$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 
	// 	$xml = new DOMDocument();
	//     @$xml->loadHTML($page); // path of your XML file ,make sure path is correct
	//     $xpd = new DOMXPath($xml);
	//     false&&$result_data = new DOMElement(); //this is for my IDE to have intellysense
	//     $res = $xpd->query("//div[@class='switchable albums clearfix']/*");  // change the table naem here
	    
	//     $albums = array();
	//     $tracklists = array();
	//     $featured = false;
	//     foreach($res as $key => $result){
	//     	$tracklist = array();    
	//         $album = $result->getElementsByTagName('h3');//get albums
	//         $songs = $result->getElementsByTagName('li');
	//         $links = $result->getElementsByTagName('a');

	//         foreach ($album  as $ab) {
	//         	if(strtolower($ab->nodeValue) == strtolower('Songs Featured In')){
	//         		$featured = true;
	//         		continue;
	//         	}
	// 	    	$albums[] = $ab->nodeValue;	
	// 	    }
		    
	//         foreach ($songs as $k => $song) {
	//         	$link = $links[$k+1]->getAttribute('href');
	//         	$tracklist[] = array('link' => $this->shortenLyricsUrl($link), 'name' => trim($song->nodeValue), 'link_ref'=>$link);
	//         }
	//         //if($tracklist)
	//        	$tracklists[]=$tracklist;
	        
	        
	//     }
	//     if(count($albums) > count($tracklists)){
	//     	end($albums);
	//     	unset($albums[key($albums)]);
	//     }
	//     else if(count($albums) < count($tracklists)){
	//     	end($tracklists);
	//     	unset($tracklists[key($tracklists)]);
	//     }
	//     return array('albums' => $albums, 'tracklists'=> $tracklists);
	// }

	//lay so page
	function get_pager($page){
		preg_match_all ("/<span class=\"pages\">([^`]*?)<\/span>/", $page, $out);
		if(!isset($out[0][0])) return array();
		$doc = phpQuery::newDocument($out[0][0]);
		$n = trim(pq('a')->html());
		return explode("\n", $n);
	}

	function get_lyrics($url){
		$page = $this->getPage($url);
		$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 
		$data = array();
		//preg_match_all("/<div id=\"lyrics-body-text\">([^`]*?)<\/div>/", $page, $out);

		$doc = phpQuery::newDocument($page);
		$lyrics = trim(pq('div#lyrics-body-text')->html());
		$meta =trim(pq('p.writers')->html());
		if($meta){
			$meta = explode('<strong>', $meta);
		}
		if(@$lyrics)
		{
			if($lyrics == '<p class="verse"></p>') $lyrics = NULL;
			$data['lyrics'] = $lyrics;
			//preg_match_all("/<p class=\"writers\">([^`]*?)<\/p>/", $page, $meta);
			
			if(isset($meta[1])){
				$data['writer'] = strip_tags(str_replace(WRITER,'',$meta[1]));
			}
			else{
				$data['writer'] = '';
			}
			if(isset($meta[2])){
				$data['publisher'] = trim(substr(strip_tags(str_replace(PUBLISHER,'',$meta[2])), strlen(LYRICS)+1));
			}
			else
			{
				$data['publisher'] ='';
			}
			return $data;
		}
		return FALSE;
	}
	function shortenAlbumUrl($url){
		if(!$url) return false;
		$url = substr($url, strlen(ML));
		$url = substr($url, 0, strlen($url)-strlen(MS.LYRICS.DOT.PAGE_SUFFIX));
		return $url;
	}

	// function get_featured_list($artist_link){
	// 	$url = ML.$artist_link.MS.FEAT.DOT.PAGE_SUFFIX;
	// 	$html = $this->getPage($url);
	// 	$doc = phpQuery::newDocument($html);
	// 	$tracks = array();
	// 	foreach (pq('table.songs-table a') as $a) {
	// 		$href = pq($a)->attr('href').'<br>';
	// 		$name = 
	// 	}exit;
	// }

	function get_albums_and_tracks($page){
		$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 
	    $doc = phpQuery::newDocument($page);

	    $albums = array();
	    $tracklists = array();
	    foreach (pq('div.album-track-list') as $ab_key => $albblock) {
	    	$an = trim(strip_tags(pq($albblock)->find('h3 span')->html()));
	    	//get meta (year, genre, coverURL, number of tracks)
	    	$num_tracks = pq($albblock)->find('meta[itemprop="numTracks"]')->attr('content');
	    	$year = pq($albblock)->find('meta[itemprop="copyrightYear"]')->attr('content');
	    	$genre = pq($albblock)->find('meta[itemprop="genre"]')->attr('content');
	    	$img_link = pq($albblock)->find('.album-img img')->attr('src');
	    	$album['name'] = $an;
	    	$album['num_of_tracks'] = $num_tracks;
	    	$album['year'] = $year;
	    	$album['genre'] = $genre;
	    	$album['image'] = $img_link;

			$albums[] = $album;
			$tracks = pq($albblock)->find('li a:lastChilld');
			foreach (pq($tracks) as $tk => $track) {
				// $track_link = pq($track)->attr('href');
				// $track_name = pq($track)->html();
				$tracklists[$ab_key][] = $this->__get_track_from_a_tag($track);	
			}
		}
	    return array('albums' => $albums, 'tracklists'=> $tracklists);
	}

	function get_featured_tracks($page)
	{
		$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 
	    $doc = phpQuery::newDocument($page);

		$elem = 'div#featured tbody tr';
		$featured_tracks = array();
		foreach (pq($elem) as $key => $tr) {
			$track = pq($tr)->find('a');
			$featured_art = pq($tr)->find('td:nth-child(3)');
			$featured_tracks[] = $this->__get_track_from_a_tag($track, $featured_art);

		}
		return $featured_tracks;
	}

	function get_artist_banner($link){
		$url = ML.$link.MS.LYRICS.DOT.PAGE_SUFFIX;
		$page = $this->getPage($url);
		$page = mb_convert_encoding($page , 'HTML-ENTITIES', 'UTF-8'); 

		$doc = phpQuery::newDocument($page);

		$img = pq('#bg-top img')->attr('pagespeed_lazy_src');
		if(!$img){
			$img = pq('#bg-top img')->attr('src');
		}
		return $img;
	}

	function __get_track_from_a_tag($track, $featured_art = false)
	{
		$track_link = pq($track)->attr('href');
		$track_name = pq($track)->html();
		$track_name = $this->remove_lyrics_suffix($track_name);
		if(!$featured_art)
			return array('link' => $track_link, 'name' => trim($track_name));	
		$art_name = trim(pq($featured_art)->html());
		return array('link' => $track_link, 'name' => trim($track_name), 'master_artist' => $art_name);	
	}

	function remove_lyrics_suffix($text)
	{
		return trim(substr($text, 0, strrpos($text, 'Lyrics')));
	}
}

 ?>