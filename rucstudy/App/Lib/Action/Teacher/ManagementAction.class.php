<?php 
/**
 * 
 * 老师课程管理控制器
 * created by hfr at 201402212227
 *
 */

class ManagementAction extends CommonCourseAction
{
	public function index()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

 		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from stu_course,student where stu_course.sno=student.sno and stu_course.cno='".I('cno')."'");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$teachermodel = new Model();
		$this->teacher = $teachermodel->query("select * from teacher,course where course.tno = teacher.tno and course.cno='".$this->cno."'");
		
		$studentmodel = new Model();
		$this->student = $studentmodel->query("select * from stu_course,student where stu_course.sno=student.sno and stu_course.cno='".I('cno')."' limit ". $page->firstRow.",".$page->listRows);

		$this->display();
	}
	
	//更新课程信息
	public function updateIntroductionHandle()
	{
		$intromodel1 = new Model();
		$intro1 = $intromodel1->execute("update course set checkway='".I('checkway')."' where cno='".I('cno')."'");
		
		$intromodel2 = new Model();
		$intro2 = $intromodel2->execute("update course set intro='".I('intro')."' where cno='".I('cno')."'");
		
		$intromodel3 = new Model();
		$intro3 = $intromodel3->execute("update course set cnotes='".I('cnotes')."' where cno='".I('cno')."'");
		
		if($intro1||$intro2||$intro3)
		{
			$data['status']=1;
		}
		else 
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	//删除学生
	public function deleteStudent()
	{
		$this->cno = I('cno');
		
		$deletemodel = new Model();
		
		foreach($_POST as $key => $value)
		{
			if($value=='on')
			{
				if(!($deletemodel->execute("delete from stu_course where sno='".$key."' and cno='".I('cno')."'")))
				{
					$data['status']=2;
					$this->ajaxReturn($data,'json');
				}
			}
		}
		
		$data['status']=1;
		$this->ajaxReturn($data,'json');
	}
	
	//添加学生
	public function addStudent()
	{
		$model = new Model();
		$existindatabase=$model->query("select sname from student where sno='".I('sno')."'");
		if(!$existindatabase[0]['sname'])
		{
			$data['status']=3;
			$this->ajaxReturn($data,'json');
		}
		$exist = $model->query("select sno from stu_course where sno='".I('sno')."' and cno='".I('cno')."'");
		if($exist[0]['sno'])
		{
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}
		$addstudentmodel = new Model();
		$addstudent = $addstudentmodel->execute("insert into stu_course(sno,cno,is_on) values('".I('sno')."','".I('cno')."',1)");
		if($addstudent)
		{
			$data['status']=1;
		}
		else
		{
			$this->error('添加失败');
			$data['status']=2;
		}
		$this->ajaxReturn($data,'json');
	}

	//批准学生加入申请
	public function approve()
	{
		$model = new Model();

		foreach($_POST as $key => $value)
		{
			if($value=='on')
			{
				$deleteapp = $model->execute("delete from apply where apply.app_cno='".I('cno')."' and apply.app_sno=".$key);
				$joincourse = $model->execute("insert into stu_course(sno,cno) values('".$key."','".I('cno')."')");
				if(!($deleteapp&&$joincourse))
				{
					$data['status']=0;
					$this->ajaxReturn($data,'json');			
				}
			}
		}

		$data['status']=1;
		$this->ajaxReturn($data,'json');
	}
}

?>