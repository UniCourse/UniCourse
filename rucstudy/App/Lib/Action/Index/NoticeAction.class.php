<?php
/**
 * 公告控制器
 * created by hfr
 */

class NoticeAction extends CommonAction
{
	/**
	 * 课程公告主页
	 * by hfr at 201402081621
	 */
	public function index($cno)
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		$homemodel = new Model();
		$this->home = $homemodel->query("select * from notice where cno='" . $this->cno."' order by ntime DESC");
		
		$this->display();
	}

}
?>