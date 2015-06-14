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
	function exact_album_name_year($album_name = ''){
		if($album_name == '') return '';
		$data= array();
		if(@$album_name[strlen($album_name)-6] == '(' && @$album_name[strlen($album_name)-1] == ')')
		{
			$data['album_name'] = trim(substr($album_name,0, strlen($album_name)-6));
			$data['album_year'] = trim(substr($album_name, strlen($album_name)-5, 4));
		}else{
			$data['album_name'] = $album_name;
			$data['album_year'] = '';
		}
		return $data;
	}
}