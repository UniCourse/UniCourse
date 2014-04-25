<?php 

Class CourseAction extends CommonAdminAction{
	public function index()
	{
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from course");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$model = new Model();
		$this->course = $model->query("select * from course limit ". $page->firstRow.",".$page->listRows);

		$this->display();
	}

	/**
	 * 以下是资料管理部分
	 */
	public function searchjudge()
	{
		$this->cno=I('cno');

		$model = new Model();
		$course = $model->query("select cno from course where cno=".I('cno'));

		if($course[0]['cno'])
		{
			$data['status']=1;
			$data['cno']=I('cno');
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	public function searchresult()
	{
		$this->cno=I('cno');

		$model = new Model();
		$this->course = $model->query("select * from course where cno=".I('cno'));

		$this->display('index');
	}

	public function detail()
	{
		$this->cno=I('cno');

		$model = new Model();
		$this->course = $model->query("select * from course where cno=".I('cno'));

		$this->display();
	}

	public function modify()
	{
		$model = new Model();
		$modify = $model->execute("update course set cname='".I('cname')."', cschool='".I('cschool')."', checkway='".I('checkway')."', intro='".I('intro')."', cnotes='".I('cnotes')."', tno='".I('tno')."' where cno=".I('cno'));
		
		if($modify)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	//添加课程页面
	public function register()
	{
		$model = new Model();
		$this->teacher = $model->query("select * from teacher order by tno ASC");

		$this->display();
	}

	public function handleregister()
	{
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("insert into course(cno,cname,cschool,tno,checkway,intro,cnotes)values('".I('cno')."','" . I('cname') . "','" . I('cschool') . "','" . I('tno') . "','" . I('checkway') ."','" . I('intro') ."','" . I('cnotes') . "')");
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
	 * 以下是公告管理
	 */
	public function home()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

 		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from notice where cno=".I('cno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$homemodel = new Model();
		$this->home = $homemodel->query("select * from notice where cno=".I('cno') . ' order by ntime DESC limit '. $page->firstRow.','.$page->listRows);
		
		$this->display();
	}

	public function deleteHome()
	{
		$deletehomemodel = new Model();
		$deletehome = $deletehomemodel->execute("delete from notice where nno=".I('nno'));
		
		if($deletehome)
		{
			//$this->success('删除成功');
			$data['status']=1;
		}
		else
		{
			// $this->error('删除失败');
			$data['status']=1;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 以下是问答管理
	 */
	public function question()
	{
		$this->cno = I('cno');  //课程编号
		$this->cname = getCourseName($this->cno);
		
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from question where cno=" . I('cno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		//查询该课程所有问题
		$questionmodel = new Model();
		$this->question = $questionmodel->query("select * from question where cno=" . I('cno')." order by raise_time DESC limit ". $page->firstRow.",".$page->listRows);

		$this->display();
	}

	public function deleteQuestion()
	{
		$model = new Model();
		$result = $model->execute("delete from question where qno=".I('qno'));

		if($result)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	public function reply()
	{
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from reply where qno=".I('qno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$model = new Model();
		$this->reply = $model->query("select * from reply where qno=".I('qno')." order by rplytime DESC limit ". $page->firstRow.",".$page->listRows);

		$this->display();
	}

	public function deleteReply()
	{
		$model = new Model();
		$result = $model->execute("delete from reply where rpno=".I('rpno'));

		if($result)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 以下是小组管理
	 */
	public function group()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName(I('cno'));

		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from groups where cno=".I('cno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$model = new Model();
		$this->group = $model->query("select * from groups where cno=".I('cno')." limit ". $page->firstRow.",".$page->listRows);

		$this->display();
	}

	public function groupDetail()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->gno = I('gno');
		$this->gname = getGroupName($this->gno);
		
		$groupdetailmodel = new Model();
		$this->groupdetail = $groupdetailmodel->query('select * from groups where gno=' . $this->gno);
	
		$groupmembermodel = new Model();
		$this->groupmember = $groupmembermodel->query('select * from stu_grp,student where student.sno=stu_grp.sno and stu_grp.gno=' . $this->gno);
	
		$groupfilemodel = new Model();
		$this->groupfile = $groupfilemodel->query("select * from grpfile where gno=" . $this->gno." order by ftime DESC");
		
		$this->display();
	}

	/**
	 * 以下是作业管理
	 */
	public function homework()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from homework where homework.cno=".I('cno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$allhomeworkmodel = new Model();
		$this->allhomework = $allhomeworkmodel->query("select * from homework where homework.cno=".I('cno')." order by htime DESC limit ". $page->firstRow.",".$page->listRows);
		
		$this->display();
	}
	
	public function homeworkDetail()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->hno = I('hno');
		
		$homeworkdetailmodel = new Model();
		$this->homeworkdetail = $homeworkdetailmodel->query("select * from student,stu_homework where student.sno=stu_homework.sno and stu_homework.hno=".I('hno'));
		
		$homeworkfilemodel = new Model();
		$this->homeworkfile = $homeworkfilemodel->query("select * from homeworkfile where hno=".I('hno')." order by ftime DESC");
		
		$homeworkmodel = new Model();
		$this->homework = $homeworkmodel->query("select * from homework where hno=".I('hno'));
		
		
		$this->display();
	}

	public function deleteHomework()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->hno = I('hno');
		
		$delhwkfilemodel = new Model();
		$delhwkfile = $delhwkfilemodel->execute("delete from homeworkfile where hno=".I('hno').";");
		
		$delstuhwkmodel = new Model();
		$delstuhwk = $delstuhwkmodel->execute("delete from stu_homework where hno=".I('hno').";");
		
		$delhwkmodel = new Model();
		$delhwk = $delhwkmodel->execute("delete from homework where hno=".I('hno'));
		
		
		if($delhwk)
		{
			$this->success('删除成功');
		}
		else
		{
			$this->error('删除失败');
		}
	}

	public function deleteHomeworkFile()
	{
		$model = new Model();
		$filename = $model->query("select * from homeworkfile where fno=".I('fno'));
		
		//中文转码
		$filename[0]['furl']=iconv('UTF-8','GB2312',$filename[0]['furl']);
		//删除文件
		$deleteflag = unlink($filename[0]['furl']);

		$deletefile = $model->execute("delete from homeworkfile where fno=".I('fno'));
		
		if($deletefile && $deleteflag)
		{
			$this->success('删除成功');
		}
		else
		{
			$this->error('删除失败');
		}
	}

	/**
	 * 以下是课件管理
	 */
	public function document()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from coursefile where cno=".I('cno')." order by ftime DESC");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$filenamemodel = new Model();
		$this->filename = $filenamemodel->query("select * from coursefile where cno=".I('cno')." order by ftime DESC limit ". $page->firstRow.",".$page->listRows);
		
		$this->display();
	}
	
	public function deleteDocument()
	{
		$filenamemodel = new Model();
		$filename = $filenamemodel->query("select * from coursefile where fno=".I('fno'));
		
		//中文转码
		$filename[0]['furl']=iconv('UTF-8','GB2312',$filename[0]['furl']);
		//删除文件
		$deleteflag = unlink($filename[0]['furl']);
		
		$deletefilemodel = new Model();
		$deletefile = $deletefilemodel->execute("delete from coursefile where fno=".I('fno'));
		
		if($deletefile&&$deleteflag)
		{
			//$this->success('删除成功');
			$data['status']=1;
		}
		else
		{
			//$this->error('删除失败');
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 下载模块
	 */
	public function download()
	{
		$furl=I('furl');

		//中文转码
		$furl=iconv('UTF-8','GB2312',$furl);
		
		$fname=I('fname');
		if (!file_exists($furl)){
            header("Content-type: text/html; charset=utf-8");
            $this->error("File not found!");
        } else {
            $file = fopen($furl,"r");
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: ".filesize($furl));
            Header("Content-Disposition: attachment; filename=".$fname);
            echo fread($file, filesize($furl));
            fclose($file);
        }

	}

	/**
	 * 设置助教
	 * by hfr at 201403242254
	 */
	public function assistant(){
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		$this->assistant = M()->query("select * from assi_course,student where assi_course.ano = student.sno and assi_course.cno='".I('cno')."'");

		$this->display();
	}

	public function setassistant(){
		$isexist = M()->query("select * from student where sno='".I('sno')."'");
		if(!$isexist){
			$data['status'] = 0;
			$this->ajaxReturn($data,'json');
		}
		$result = M()->execute("insert into assi_course(ano,cno) values('".I('sno')."','".I('cno')."')");
		if($result){
			$data['status'] = 1;
			$this->ajaxReturn($data, 'json');
		}
		$data['status'] = 0;
		$this->ajaxReturn($data,'json');
	}

	public function deleteassistant(){
		$result = M()->execute("delete from assi_course where ano='".I('sno')."'");
		if($result){
			$data['status'] = 1;
			$this->ajaxReturn($data, 'json');
		}
		$data['status'] = 0;
		$this->ajaxReturn($data,'json');
	}
}
 ?>