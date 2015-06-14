<?php 
App::uses('Component', 'Controller');
class CommonComponent extends Component {
	public function is_next_page($pagers = array(), $current_page = 0)
	{
		if(empty($pagers)) return false;
		$end_pager = end($pagers);
		if($end_pager > $current_page) return true;
		return false;
	}
}