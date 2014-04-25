<?php 
/**
 * 
 * 老师课程控制器
 * @author hfr & tyy
 *
 */

class CourseAction extends CommonCourseAction
{
	public function index()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		$model = new Model();
		$this->apply = $model->query("select * from apply,student where apply.app_sno=student.sno and apply.app_cno='".I('cno')."'");

		$this->display();
	}

}

?>