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
	//lay danh sach album va traack list trong album
	function get_albums_and_tracks($page){
		$xml = new DOMDocument();
	    @$xml->loadHTML($page); // path of your XML file ,make sure path is correct
	    $xpd = new DOMXPath($xml);
	    false&&$result_data = new DOMElement(); //this is for my IDE to have intellysense
	    $res = $xpd->query("//div[@class='switchable albums clearfix']/*");  // change the table naem here
	    
	    $albums = array();
	    $tracklists = array();
	    foreach($res as $key => $result){
	    	$tracklist = array();    
	        $album = $result->getElementsByTagName('h3');//get albums
	        $songs = $result->getElementsByTagName('li');
	        $links = $result->getElementsByTagName('a');
	        foreach ($album  as $ab) {
		    	$albums[] = $ab->nodeValue;	
		    }

	        foreach ($songs as $k => $song) {
	        	$link = $links[$k+1]->getAttribute('href');
	        	$tracklist[] = array('link' =>$link, 'name' => trim($song->nodeValue));
	        }
	        if($tracklist)
	       		$tracklists[]=$tracklist;
	    }
	    if(count($albums) > count($tracklists)){
	    	end($albums);
	    	unset($albums[key($albums)]);
	    }
	    return array('albums' => $albums, 'tracklists'=> $tracklists);
	}

	//lay so page
	function get_pager($page){
		preg_match_all ("/<span class=\"pages\">([^`]*?)<\/span>/", $page, $out);
		if(!isset($out[0][0])) return array();
		$doc = phpQuery::newDocument($out[0][0]);
		$n = trim(pq('a')->html());
		return explode("\n", $n);
	}

	function get_lyrics($page){
		$data = array();
		preg_match_all ("/<div id=\"lyrics-body-text\">([^`]*?)<\/div>/", $page, $out);
		if(isset($out[1][0]))
		{
			$data['lyrics'] = $out[1][0];
			preg_match_all("/<p class=\"writers\">([^`]*?)<\/p>/", $page, $meta);
			
			$data['writer'] = strip_tags(substr($meta[0][0], 52));
			$data['publisher'] = substr(strip_tags(substr($meta[0][1], 53)), 8);
			return $data;
		}
		return FALSE;
	}

}

 ?>