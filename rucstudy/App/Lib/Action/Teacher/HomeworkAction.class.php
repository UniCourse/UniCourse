<?php 
/**
 * 
 * 老师课程作业控制器
 * created by hfr at 201402212227
 *
 */

class HomeworkAction extends CommonCourseAction
{
	//该课程所有作业
	public function index()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from homework where homework.cno='".I('cno')."'");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$allhomeworkmodel = new Model();
		$this->allhomework = $allhomeworkmodel->query("select * from homework where homework.cno='".I('cno')."' order by htime DESC limit ". $page->firstRow.",".$page->listRows);
		
		$this->display();
	}

	//课程作业细节
	public function detail()
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

		$model = new Model();
		$this->notupload = $model->query("select * from student,stu_course where student.sno=stu_course.sno and stu_course.cno='".I('cno')."' and not exists ( select * from stu_course,stu_homework where stu_course.sno=stu_homework.sno and student.sno=stu_homework.sno and stu_homework.hno=".I('hno')." and stu_course.cno='".I('cno')."' )");
		
		$this->display();
	}

	//添加作业
	public function addHomeworkHandle()
	{
		$this->cno=I('cno');
		$this->cname = getCourseName($this->cno);
		
		$model = new Model();
		//添加作业
		$addhomework = $model->execute("insert into homework(cno,htitle,hcontent,htime,deadline) values('".I('cno')."','".I('htitle')."','".$_POST['hcontent']."',sysdate(),'".I('dlyear')."')");
		$hno = mysql_insert_id();  //记录新添加的作业编号

		//添加日程
		$addschedule = $model->execute("insert into sch(rname,rnotes,rdeadline) values('【作业】".I('htitle')."','".$_POST['hcontent']."','".I('dlyear')."')");
		$schno = mysql_insert_id();  //记录新添加的日程编号
		$student = $model->query("select * from stu_course where cno='".I('cno')."'");

		/**
		 * 为课程添加相应的日程号 
		 * modified by hfr on 2013.9.5 11:49
		 */
		$addsch = $model->execute("update homework set sdno=".$schno." where hno=".$hno);

		foreach($student as $value)
		{//为每个选择该课的学生添加日程
			$result = $model->execute("insert into stu_sch(sno,sdno) values('" . $value['sno'] . "'," . $schno .")");
			if(!$result)
			{//如果插入失败，输出学生学号
				$this->error('fail to add stu_sch' . $value['sno']);
			}
		}
				
		
		if($_FILES['upload']['name'])
		{//有作业附件：如果本PHP接收到表单提交来的文件，且学生的日程表中添加了本作业的日程提醒			
			mkdir('./Uploads/Homework/',0777);
			mkdir('./Uploads/Homework/' . $hno . '/',0777);
			$furl =  './Uploads/Homework/'. $hno .'/';// 设置附件上传目录
			
			foreach ($_FILES['upload']['name'] as $key => $value) {
				$i=0;
				$exist = $model->query("select * from homeworkfile where fname='".$value."' and hno=".I('hno'));
				while($exist)
				{
					$i++;
					$dname="(".$i.")".$value;
					$exist = $model->query("select * from homeworkfile where fname='".$dname."' and hno=".I('hno'));
				}
				if($i)
				{
					$_FILES['upload']['name'][$key]=$dname;
				}
			}

			//上传文件
			$judge = uploadfiles($furl);
			if(!$judge[0]['savename'])
			{// 上传错误提示错误信息
				$data['status'] = $judge;
			}else
			{// 上传成功
				//数据库插入记录
				foreach ($_FILES['upload']['name'] as $key => $value) {
					$file = $model->execute("insert into homeworkfile(fname,ftime,hno,furl) values('" . $value . "',sysdate()," . $hno . ",'" . $furl . $judge[0]['savename'] . "')");
				}
			}
			
			if($addhomework && $judge && $file && $addschedule)
			{//如果添加作业成功，且上传文件成功，且将文件记录入数据库，且日程已添加
				/**
				 * createNews为产生新鲜事的函数
				 * 有无附件两个地方p1
				 */
				$n_contenturl = U('Index/Homework/index',array('cno'=>I('cno')));
				$n_content = I('htitle');
				$addcourse_news = createNews(session('uid'), I('cno'), $n_content, $n_contenturl, 3,$hno);

				if($addcourse_news)
				{
					$data['status']=1;				
					$data['fname'] = $_FILES['upload']['name'];
					$data['furl'] = $furl;
				}
			}
			else
			{
				$data['status']=5;
			}
		}
		else 
		{//无作业附件：
			if($addhomework && $addschedule)
			{
				/**
				 * createNews为产生新鲜事的函数
				 * 有无附件两个地方p2
				 */
				$n_contenturl = U('Index/Homework/index',array('cno'=>I('cno')));
				$n_content = I('htitle');
				$addcourse_news = createNews(session('uid'), I('cno'), $n_content, $n_contenturl, 3,$hno);

				if($addcourse_news)
				{
					$data['status']=1;
				}else
				{
					$data['status']=3;
				}
			}
			else
			{
				$data['status']=4;
			}
		}
		$this->ajaxReturn($data,'json');
	}

	//删除作业文件
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

	//上传作业文件
	public function uploadHomeworkFile()
	{		
		mkdir('./Uploads/Homework/',0777);
		mkdir('./Uploads/Homework/'. I('hno') .'/',0777);
		$furl =  './Uploads/Homework/'. I('hno') .'/';// 设置附件上传目录
		
		$model = new Model();
		foreach ($_FILES['upload']['name'] as $key => $value) {
				$i=0;
				$exist = $model->query("select * from homeworkfile where fname='".$value."' and hno=".I('hno'));
				while($exist)
				{
					$i++;
					$dname="(".$i.")".$value;
					$exist = $model->query("select * from homeworkfile where fname='".$dname."' and hno=".I('hno'));
				}
				if($i)
				{
					$_FILES['upload']['name'][$key]=$dname;
				}
			}
		
		$judge = uploadfiles($furl);
		if($judge!=1)
		{// 上传错误提示错误信息
			$data['status'] = $judge;
			$this->ajaxReturn($data,'json');
		}else
		{// 上传成功
			foreach ($_FILES['upload']['name'] as $key => $value) {
					$file = $model->execute("insert into homeworkfile(fname,ftime,hno,furl) values('" . $value . "',sysdate()," . $hno . ",'" . $furl . $value . "')");
				}			if($file)
			{
				$data['status']=1;				
				$data['fname'] = $_FILES['upload']['name'];
				$data['furl'] = $furl;
			}
			else
			{
				$data['status']=2;
			}
		}		
	}

	//删除作业
	public function deleteHomework()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->hno = I('hno');

		$model = new Model();
		$schno = $model->query("select sdno,htitle from homework where hno=".I('hno'));

		$deletesch = $model->execute("delete from sch where sdno=".$schno[0]['sdno']);

		$deletestusch = $model->execute("delete from stu_sch where sdno=".$schno[0]['sdno']);
		
		$delhwkfile = $model->execute("delete from homeworkfile where hno=".I('hno').";");
		
		$delstuhwk = $model->execute("delete from stu_homework where hno=".I('hno').";");

		//获取新鲜事的姓名和课程
		$n_name = M()->query("select tname from teacher where tno='".session('uid')."'");
		$n_cname = M()->query("select cname from course where cno='".I('cno')."'");

		//产生新鲜事
		$n_content = "<a href=\"#\">".$n_name[0]['tname']."</a>在课程<a href=".U('Index/Homework/index',array('cno'=>I('cno'))).">".$n_cname[0]['cname']."</a>中删除了作业：<br>".$schno[0]['htitle'];
		$addcourse_news = createNews(session('uid'), I('cno'), $n_content,3);
		
		$delhwk = $model->execute("delete from homework where hno=".I('hno'));

		$delnews = M()->execute("delete from news where n_contentid=".I('hno'));
		
		
		if($delhwk)
		{
			$this->success('删除成功');
		}
		else
		{
			$this->error('删除失败');
		}
	}
}

?>