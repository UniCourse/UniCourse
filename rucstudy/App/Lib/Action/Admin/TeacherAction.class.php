<?php 
/**
* 
*/
class TeacherAction extends CommonAdminAction
{
	public function index()
	{
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from teacher");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

 		$model = new Model();
 		$this->sname = $model->query("select * from teacher order by tno ASC limit ". $page->firstRow.",".$page->listRows);
 	
    	$this->display();
	}

	/**
	 * 搜索老师判断函数
	 */
	public function searchjudge()
	{
		$this->tno==I('tno');

		$model = new Model();
		$teacher = $model->query("select tno from teacher where tno=".I('tno'));

		if($teacher[0]['tno'])
		{
			$data['status']=1;
			$data['tno']=I('tno');
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	public function searchresult()
	{
		$this->cno=I('tno');

		$model = new Model();
		$this->sname = $model->query("select * from teacher where tno=".I('tno'));

		$this->display('index');
	}

	public function detail()
	{
		$this->tno==I('tno');

		$model = new Model();
		$this->teacher = $model->query("select * from teacher where tno=".I('tno'));

		$this->display();
	}

	/**
	 * 修改老师资料函数
	 */
	public function modify()
	{
		$model = new Model();
		$modify = $model->execute("update teacher set tname='".I('tname')."', sex='".I('sex')."', tschool='".I('tschool')."', phone='".I('phone')."', email='".I('email')."', tnotes='".I('tnotes')."', office='".I('office')."' where tno='".I('tno')."'");
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
		$modify = $model->execute("update teacher set tpsw=md5('" . I('password') . "') where tno='".I('tno')."'");
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

	//添加老师页面
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
		$result = $model->execute("insert into teacher(tno,tname,sex,phone,email,office,tschool,tpsw,tnotes) values('" . I('tno') . "','" . I('tname') . "','" . I('sex') . "','" . I('phone') . "','" . I('email') ."','" . I('office') ."','" . I('tschool') . "',md5('" . I('password') . "'),'" . I('tnotes') . "')");
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

}
 ?>