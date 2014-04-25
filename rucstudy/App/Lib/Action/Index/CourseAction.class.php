<?php
/**
 * 课程控制器
 * by hfr
 */

class CourseAction extends CommonAction
{
	//查询所有课程
	public function index()
	{
		// //分页显示
		// import('ORG.Util.Page');
		// $countmodel = new Model();
		// $count=$countmodel->query('select count(*) from course');
		// $page=new Page($count[0]['count(*)'],10);
		// $this->page = $page->show();

		$coursemodel = new Model();
		// $course = $coursemodel->query("select cno,cname,tname from course,teacher where course.tno=teacher.tno limit ". $page->firstRow.",".$page->listRows);
		$course = $coursemodel->query("select cno,cname,tname from course,teacher where course.tno=teacher.tno");
		// $mycourse = $coursemodel->query("select course.cno,cname,tname from course,teacher,stu_course where stu_course.sno='".session('uid')."' and stu_course.cno=course.cno and course.tno=teacher.tno limit ". $page->firstRow.",".$page->listRows);
		$mycourse = $coursemodel->query("select course.cno,cname,tname from course,teacher,stu_course where stu_course.sno='".session('uid')."' and stu_course.cno=course.cno and course.tno=teacher.tno");

		foreach ($course as $key => $value) {
			if(in_array($value,$mycourse))
			{
				$course[$key]['isin']=1;
			}
			else
			{
				$course[$key]['isin']=0;
			}
		}

		$this->course = $course;

		$this->display();
	}
	
	/**
	 * 申请加入课程
	 * modified by hfr at 2013.9.5 14:03
	 */
	public function apply()
	{
		$model = new Model();
		$result = $model->execute("insert into apply(app_sno,app_cno) values('".session('uid')."','".I('cno')."')");

		if($result)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

}
?>