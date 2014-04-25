<?php
/**
 * 课件控制器
 * by hfr
 */

class DocumentAction extends CommonAction
{
	/**
	 * 课程课件主页
	 * by hfr at 201402081621
	 */
	public function index($cno)
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
		$this->filename = M()->query("select * from coursefile where cno='".I('cno')."' order by ftime DESC");
	
		$this->display();
	}

}
?>