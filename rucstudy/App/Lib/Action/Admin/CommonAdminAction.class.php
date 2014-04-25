<?php 
class CommonAdminAction extends Action
{
	public function _initialize()
	{
		if(!(isset($_SESSION['uid'])&&session('role')=='admin'))
		{
			$this->redirect('Admin/Index/index');
		}
	}
}
?>