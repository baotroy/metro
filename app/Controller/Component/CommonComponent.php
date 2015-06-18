<?php 
App::uses('Component', 'Controller');
class CommonComponent extends Component {
	public function is_next_page($pagers = array(), $current_page = 0)
	{
		if(empty($pagers)) return false;
		$end_pager = end($pagers);
		if($end_pager == '...') return true;
		if($end_pager > $current_page) return true;
		return false;
	}
	function shortenLyricsUrl($url){
		if(!$url) return false;
		$url = substr($url, strlen(ML));
		
		$url = substr($url, 0, strlen($url)-(strlen(DOT.PAGE_SUFFIX)));
		return $url;
	}
	function get_image_file_name($url)
	{
		return substr($url, strrpos($url, '/') + 1);
	}





	function clean($url) {
	   	$url = strtolower($url);
	    $url = strip_tags($url);
	    $url = stripslashes($url);
	    $url = html_entity_decode($url);

	    # Remove quotes (can't, etc.)
	    $url = str_replace('\'', '', $url);

	    # Replace non-alpha numeric with hyphens
	    
	    $url = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $url);
		$url = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $url);
		$url = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $url);
		$url = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $url);
		$url = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $url);
		$url = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $url);
		$url = preg_replace("/(đ)/", 'd', $url);
		$url = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $url);
		$url = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $url);
		$url = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $url);
		$url = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $url);
		$url = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $url);
		$url = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $url);
		$url = preg_replace("/(Đ)/", 'D', $url);
	    $url = trim($url, '-');

	    $match = '/[^a-z0-9]+/';
	    $replace = '-';
	    $url = preg_replace($match, $replace, $url);
	    rtrim($url, '-');
	    return $url;
	}
	
}