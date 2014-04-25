<?php 

Class StudentAction extends CommonAdminAction
{
	public function index()
	{
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from student");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

 		$model = new Model();
 		$this->sname = $model->query("select * from student order by sno ASC limit ". $page->firstRow.",".$page->listRows);
 		
    	$this->display();
	}

	/**
	 * 搜索学生判断函数
	 */
	public function searchjudge()
	{
		$this->sno==I('sno');

		$model = new Model();
		$student = $model->query("select sno from student where sno=".I('sno'));

		if($student[0]['sno'])
		{
			$data['status']=1;
			$data['sno']=I('sno');
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	public function searchresult()
	{
		$this->sno=I('sno');

		$model = new Model();
		$this->sname = $model->query("select * from student where sno=".I('sno'));

		$this->display('index');
	}

	//学生资料修改
	public function detail()
	{
 		$this->sno=I('sno');

 		$model = new Model();
 		$this->student = $model->query("select * from student where sno=".I('sno'));

 		$this->display();
	}

	/**
	 * 修改学生资料函数
	 */
	public function modify()
	{
		$model = new Model();
		$modify = $model->execute("update student set snotes='".I('snotes')."', smajor='".I('smajor')."', school='".I('school')."', sname='".I('sname')."', sex='".I('sex')."', sgrade=".I('sgrade')." where sno='".I('sno')."'");
		//$data['sql'] = $model->getLastSql();

		if($modify)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 修改学生密码函数
	 */
	public function modifypwd()
	{
		if($_POST["password"] != $_POST["passwordConfirm"])
		{
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}

		$model = new Model();
		$modify = $model->execute("update student set spsw=md5('" . I('password') . "') where sno='".I('sno')."'");
		//$data['sql'] = $model->getLastSql();

		if($modify)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 添加学生
	 */
	public function register()
	{
		$this->display();
	}

	public function handleregister()
	{
		if($_POST["password"] != $_POST["passwordConfirm"])
		{//如果两次密码输入不一致
			$data['status']=2;
		}
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("insert into student(sno,sname,sex,smajor,sgrade,spsw,snotes)values('" . $_POST["sno"] . "','" . $_POST["name"] . "','" . $_POST["sex"] . "','" . $_POST["major"] . "','" . $_POST["grade"] . "',md5('" . $_POST["password"] . "'),'" . $_POST["notes"] . "')");
		//echo $model->getLastSql(); //Debug：输出SQL语句
		if($result)
		{
			//如果插入成功
			$data['status']=1;
		}
		else
		{
			//如果插入失败
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 以下是日程管理
	 */
	public function schedule()
	{
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from sch,stu_sch where sch.sdno=stu_sch.sdno and sno=" . I('sno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$schedulemodel = new Model();
		$this->schedule = $schedulemodel->query("select * from sch,stu_sch where sch.sdno=stu_sch.sdno and sno=" . I('sno')." order by rdeadline DESC limit ". $page->firstRow.",".$page->listRows);

		$this->display();
	}

	public function deleteSchedule()
	{
		$deleteschedulemodel = new Model();
		$deleteschedule = $deleteschedulemodel->execute("delete from stu_sch where sdno=".I('sdno'));
		
		if($deleteschedule)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

}

 ?>