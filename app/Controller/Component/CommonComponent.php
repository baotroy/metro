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
	
}