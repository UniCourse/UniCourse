<?php
/**
 * 登录验证控制�?
 * created by hfr 
 * modified by slr
 */

class LoginAction extends Action 
{
	public function index()
	{
		if(isset($_SESSION['uid'])&&session('type')=='student')
		{
			$this->redirect('Index/Home/index');
		}
		if(isset($_SESSION['uid'])&&session('type')=='teacher')
		{
			$this->redirect('Teacher/Home/index');
		}

		$this->display();
	}

	public function login()
	{
		$usernumber = I('usernumber');
		$password = I('password','','md5');
		
		$susermodel = new Model();
		$suser = $susermodel->query("select * from student where sno='". $usernumber."'");

		if(!$suser)
		{
			$tusermodel = new Model();
			$tuser = $tusermodel->query("select * from teacher where tno='".$usernumber."'");
			if(!$tuser)
			{
				$data['status']=3;
				$this->ajaxReturn($data,'json');
			}
			else 
			{
				if($tuser[0]['tpsw']==$password)
				{
					/**
					*Modified by SLR
					*将老师所选课程存入SESSION，待修改
					*/
					$coursemodel = new Model();
					$this->course = $coursemodel->query("select course.cno, course.cname from course where course.tno='".$usernumber."'");
					
					setSession($tuser[0]['tname'],$usernumber,$this->course,'teacher');
					$data['status']=1;
					$this->ajaxReturn($data,'json');
				}
				else 
				{
					$data['status']=4;
					$this->ajaxReturn($data,'json');
				}
			}
		}else
		{
			if($suser[0]['spsw']==$password)
			{

				/**
				*Modified by SLR
				*将学生所选课程存入SESSION，待修改
				*/
				$coursemodel = new Model();
				$this->course = $coursemodel->query("select course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno='".$usernumber."'");
				
				setSession($suser[0]['sname'],$usernumber,$this->course,'student');
				$data['status']=2;
				$this->ajaxReturn($data,'json');
			}
			else
			{
				$data['status']=4;
				$this->ajaxReturn($data,'json');
			}
		}
		
		
		
	}
	
	//登出，清空session
	public function logout()
	{
		session_unset();
		session_destroy();
		$this->redirect('Index/Index/index');
		//header("Location: http://cas.ruc.edu.cn/cas");
	}
}
?>