<?php
/**
 * 课程简介控制器
 * created by hfr
 */

class IntroductionAction extends CommonAction
{
	/**
	 * 课程介绍主页
	 * by hfr at 201402081621
	 */
	public function index($cno)
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
		$teachermodel = new Model();
		$this->teacher = $teachermodel->query("select * from teacher,course where course.tno = teacher.tno and course.cno='".$this->cno."'");
	
		$this->display();
	}

}
?>