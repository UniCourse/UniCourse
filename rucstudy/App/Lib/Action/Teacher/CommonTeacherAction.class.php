<?php
class CommonTeacherAction extends Action
{
	public function _initialize()
	{
		if(!isset($_SESSION['uid']))
		{
			$this->redirect('Index/Index/index');
		}
		else
		{
			if(session('type')!='teacher')
			{
				$this->redirect('Index/Index/index');
			}
		}
	}
}
?>