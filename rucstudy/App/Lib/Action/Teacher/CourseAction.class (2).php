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

	

	
	public function approve()
	{//批准学生加入申请
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

	public function home()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
// 		
 		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from notice where cno='".I('cno')."'");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$homemodel = new Model();
		$this->home = $homemodel->query("select * from notice where cno='".I('cno') . "' order by ntime DESC limit ". $page->firstRow.','.$page->listRows);
		
		$this->display();   //显示模板
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
			//$this->error('删除失败');
			$data['status']=1;
		}
		$this->ajaxReturn($data,'json');
	}
	
	public function addNotice()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
		//获取新鲜事的姓名和课程
		$n_name = M()->query("select tname from teacher where tno='".session('uid')."'");
		$n_cname = M()->query("select cname from course where cno='".I('cno')."'");

		$model = new Model();
		$addnotice = $model->execute("insert into notice(ntime,ncontent,cno) values(sysdate(),'".$_POST['notice']."','".I('cno')."')");
		
		if($addnotice)
		{//如果公告添加成功，添加公告新鲜事

			//产生新鲜事
			$n_content = "<a href=\"#\">".$n_name[0]['tname']."</a>在课程<a href=".U('Index/Notice/index',array('cno'=>I('cno'))).">".$n_cname[0]['cname']."</a>中发布了新公告：<br><a href=".U('Index/Notice/index',array('cno'=>I('cno'))).">".I('notice')."</a>";
			$addcourse_news = createNews(session('uid'), I('cno'), $n_content,2);

			//添加公告消息
			/*先插入到mn消息表中*/
			$addmn = $model->execute("insert into mn(cno,c_url,ob_url,mntime) values('". I('cno') ."','". U('Index/Course/home',array('cno' => I('cno')))."','". U('Index/Course/home',array('cno' => I('cno')))."',sysdate() )" );
			//echo $model->getLastSql(); //Debug：输出SQL语句
			$mid = mysql_insert_id();  //记录消息编号

			/*将这门课的学生选出，再将记录插入到对应的mn_stu表格中*/
			$sno= $model->query("select sno from stu_course where cno='" . I('cno')."'");
			//var_dump($sno);
			if($sno){
				foreach($sno as $stu){
					$addmn_user = $model->execute("insert into mn_user(mid,sno) values('".$mid."','" . $stu['sno']."')");
//					echo $model->getLastSql();
					if(!$addmn_user)
					{
						//$this->error('加入消息_用户表失败：' . $stu['sno']);
						$data['status']=3;
						$this->ajaxReturn($data,'json');
					}
				}
			}
			
			if($addcourse_news && $addmn)
			{
				//$this->success('公告添加成功，添加消息成功');
				$data['status']=1;
			}
			else
			{
				//$this->error('公告添加成功，添加消息失败');
				$data['status']=2;
			}
		}
		else
		{
			//$this->error('公告添加失败');
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	public function question()
	{
				
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from question where cno='" . $this->cno."'");
		// p($count);echo $count[0]['count(*)'];die;
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$questionmodel = new Model();
		$temp = $questionmodel->query("select * from question where cno='" . $this->cno."' order by raise_time DESC" . ' limit '. $page->firstRow.','.$page->listRows);
		//echo $questionmodel->getLastSql();die;

		foreach ($temp as $key => $value) {
			$result = $questionmodel->query("select * from q_attention where sno='".session('uid')."' and qno=".$value['qno']);
			$name = $questionmodel->query("select * from student where sno='".$value['raise_sno']."'");
			if(!$name)
			{
				$name = $questionmodel->query("select * from teacher where tno='".$value['raise_sno']."'");
				$temp[$key]['sname']=$name[0]['tname'];
			}else
			{
				$temp[$key]['sname']=$name[0]['sname'];
			}
			if($result)
			{
				$temp[$key]['isFocus']=1;
			}else
			{
				$temp[$key]['isFocus']=0;
			}
		}
		$this->question = $temp;
		
		// $replyquestionmodel = new Model();
		// $this->replyquestion = $replyquestionmodel->query('select * from reply,question where reply.qno=question.qno and question.cno=' . $this->cno);
		
		// $remarkreplyquestionmodel = new Model();
		// $this->remarkreplyquestion = $remarkreplyquestionmodel->query('select * from remark,reply,question where remark.rpno=reply.rpno and reply.qno=question.qno and question.cno=' . $this->cno);
	
		$this->display();
	}
	
	//老师在该课程提出的所有问题
	public function myQuestion()
	{
		//查询所有我提出的问题
		$myquestionmodel = new Model();
		$this->myquestion = $myquestionmodel->query('select * from question where raise_sno=' . session('uid')." order by raise_time DESC");
		//查询所有我提出问题的回答
		$replymyquestionmodel = new Model();
		$this->replymyquestion = $replymyquestionmodel->query('select * from reply,question where reply.qno=question.qno and raise_sno=' . session('uid')." order by rplytime DESC");
		//查询所有我提出问题回答的评论
		$remarkreplyquestionmodel = new Model();
		$this->remarkreplyquestion = $remarkreplyquestionmodel->query('select * from remark,reply,question where remark.rpno=reply.rpno and reply.qno=question.qno and question.raise_sno=' . session('uid')." order by rmtime ASC");
		
		//显示模板
		$this->display();
	}
	
	//该课程所有小组
	public function group()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
// 		
 		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from groups where cno='" . $this->cno."'");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$groupmodel = new Model();
		$this->group = $groupmodel->query("select * from groups where cno='" . $this->cno . "' limit ". $page->firstRow.",".$page->listRows);
				
		$this->display();
	}
	
	//小组细节
	public function groupDetail()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		$this->gno = I('gno');
		$this->gname = getGroupName($this->gno);
// 		$this->gname = I('gname');
// 		
 		//小组新鲜事分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query('select count(*) from grpnews where grpnews.gno='.I('gno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		//小组新鲜事
		$groupnewsmodel = new Model();
		$this->groupnews = $groupnewsmodel->query("select * from student,grpnews where student.sno=grpnews.n_sno and grpnews.gno=".I('gno').' order by n_time DESC' . ' limit '. $page->firstRow.','.$page->listRows);
		
		$groupdetailmodel = new Model();
		$this->groupdetail = $groupdetailmodel->query('select * from groups where gno=' . $this->gno);
	
		$groupmembermodel = new Model();
		$this->groupmember = $groupmembermodel->query('select * from stu_grp,student where student.sno=stu_grp.sno and stu_grp.gno=' . $this->gno);
	
		$groupfilemodel = new Model();
		$this->groupfile = $groupfilemodel->query("select * from grpfile where gno=" . $this->gno." order by ftime DESC");
		
		$this->display();
	}
	
	//该课程所有作业
	public function homework()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
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
	public function homeworkDetail()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
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
	
	public function addHomeworkHandle()
	{
		$this->cno=I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname=I('cname');
		
		$model = new Model();
		//添加作业
		$addhomework = $model->execute("insert into homework(cno,htitle,hcontent,htime,deadline) values('".I('cno')."','".I('htitle')."','".$_POST['hcontent']."',sysdate(),'".I('dlyear')."')");
		$hno = mysql_insert_id();  //记录新添加的作业编号
//		echo $model->getLastSql()."<br/>";  //Debug：输出SQL语句
//		var_dump($addhomework);  //Debug
//		die;

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
//			echo $model->getLastSql()."<br/>";  //Debug：输出SQL语句
			if(!$result)
			{//如果插入失败，输出学生学号
				$this->error('fail to add stu_sch' . $value['sno']);
			}
		}
				
		
		if($_FILES['upload']['name'])
		{//有作业附件：如果本PHP接收到表单提交来的文件，且学生的日程表中添加了本作业的日程提醒			
// 			import('ORG.Net.UploadFile');
// 			$upload = new UploadFile();// 实例化上传类
// 			$upload->maxSize  = 31457280 ;// 设置附件上传大小
			
// //			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
// //			$upload->autoSub = true;
// //			$upload->subType = 'custom';
// //			$upload->subDir = 'Group';
			
// 			$upload->saveRule = '';
			mkdir('./Uploads/Homework/',0777);
			mkdir('./Uploads/Homework/' . $hno . '/',0777);
			$furl =  './Uploads/Homework/'. $hno .'/';// 设置附件上传目录
			// 		p($upload);
			// 		p($_FILES);die;
			
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
			if(!$judge)
			{// 上传错误提示错误信息
				//$this->error($upload->getErrorMsg());
				$data['status'] = $judge;
			}else
			{// 上传成功
				//数据库插入记录
				foreach ($_FILES['upload']['name'] as $key => $value) {
					$file = $model->execute("insert into homeworkfile(fname,ftime,hno,furl) values('" . $value . "',sysdate()," . $hno . ",'" . $furl . $value . "')");
				}
			}
			
			if($addhomework && $judge && $file && $addschedule)
			{//如果添加作业成功，且上传文件成功，且将文件记录入数据库，且日程已添加
				
				//获取新鲜事的姓名和课程
				$n_name = M()->query("select tname from teacher where tno='".session('uid')."'");
				$n_cname = M()->query("select cname from course where cno='".I('cno')."'");

				//产生新鲜事
				$n_content = "<a href=\"#\">".$n_name[0]['tname']."</a>在课程<a href=".U('Index/Homework/index',array('cno'=>I('cno'))).">".$n_cname[0]['cname']."</a>中布置了新作业：<br><a href=".U('Index/Homework/index',array('cno'=>I('cno'))).">".$_POST['hcontent']."</a>";
				$addcourse_news = createNews(session('uid'), I('cno'), $n_content,3);

				if($addcourse_news)
				{
					//$this->success('有附件：布置成功，添加新鲜事成功',U('Teacher/Course/homework',array('cno' => I('cno'))));
					$data['status']=1;				
					$data['fname'] = $_FILES['upload']['name'];
					$data['furl'] = $furl;
				}
			}
			else
			{
				//$this->error('有附件：布置失败');
				$data['status']=0;
			}
		}
		else 
		{//无作业附件：
			if($addhomework && $addschedule)
			{
				//获取新鲜事的姓名和课程
				$n_name = M()->query("select tname from teacher where tno='".session('uid')."'");
				$n_cname = M()->query("select cname from course where cno='".I('cno')."'");

				//产生新鲜事
				$n_content = "<a href=\"#\">".$n_name[0]['tname']."</a>在课程<a href=".U('Index/Homework/index',array('cno'=>I('cno'))).">".$n_cname[0]['cname']."</a>中布置了新作业：<br><a href=".U('Index/Homework/index',array('cno'=>I('cno'))).">".I('hcontent')."</a>";
				$addcourse_news = createNews(session('uid'), I('cno'), $n_content,3);

				if($addcourse_news)
				{
					//$this->success('无附件：布置成功，添加新鲜事成功',U('Teacher/Course/homework',array('cno' => I('cno'))));
					$data['status']=1;
				}else
				{
					$data['status']=3;
				}
			}
			else
			{
				//$this->error('无附件：布置失败');
				$data['status']=0;
			}
		}
		$this->ajaxReturn($data,'json');
		
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
	
	public function uploadHomeworkFile()
	{		
		// import('ORG.Net.UploadFile');
		// $upload = new UploadFile();// 实例化上传类
		// $upload->maxSize  = 31457280 ;// 设置附件上传大小
		// // 		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		// // 		$upload->autoSub = true;
		// // 		$upload->subType = 'custom';
		// // 		$upload->subDir = 'Group';
		// $upload->saveRule = '';
		mkdir('./Uploads/Homework/',0777);
		mkdir('./Uploads/Homework/'. I('hno') .'/',0777);
		$furl =  './Uploads/Homework/'. I('hno') .'/';// 设置附件上传目录
		// 		p($upload);
		// 		p($_FILES);die;
		
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
			//$filemodel = new Model();//数据库插入记录
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
				$data['status']=0;
			}
		}		
	}
	
	public function deleteHomework()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->hno = I('hno');

		$model = new Model();
		$schno = $model->query("select sdno from homework where hno=".I('hno'));

		$deletesch = $model->execute("delete from sch where sdno=".$schno[0]['sdno']);

		$deletestusch = $model->execute("delete from stu_sch where sdno=".$schno[0]['sdno']);
		
		$delhwkfile = $model->execute("delete from homeworkfile where hno=".I('hno').";");
		
		$delstuhwk = $model->execute("delete from stu_homework where hno=".I('hno').";");
		
		$delhwk = $model->execute("delete from homework where hno=".I('hno'));
		
		
		if($delhwk)
		{
			$this->success('删除成功');
		}
		else
		{
			$this->error('删除失败');
		}
	}
	
	public function document()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from coursefile where cno='".I('cno')."' order by ftime DESC");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$filenamemodel = new Model();
		$this->filename = $filenamemodel->query("select * from coursefile where cno='".I('cno')."' order by ftime DESC limit ". $page->firstRow.",".$page->listRows);
		
		$this->display();
	}
	
	public function uploadDocument()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
		// import('ORG.Net.UploadFile');
		// $upload = new UploadFile();// 实例化上传类
		// $upload->maxSize  = 31457280 ;// 设置附件上传大小
		// // 		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		// // 		$upload->autoSub = true;
		// // 		$upload->subType = 'custom';
		// // 		$upload->subDir = 'Group';
		// $upload->saveRule = '';
		mkdir('./Uploads/Document/',0777);
		mkdir('./Uploads/Document/'. I('cno') .'/',0777);
		$furl =  './Uploads/Document/'. I('cno') .'/';// 设置附件上传目录

		$model = new Model();
		foreach ($_FILES['upload']['name'] as $key => $value) {
			$i=0;
			$exist = $model->query("select * from coursefile where fname='".$value."' and cno='".I('cno')."'");
			while($exist)
			{
				$i++;
				$dname="(".$i.")".$value;
				$exist = $model->query("select * from coursefile where fname='".$dname."' and cno='".I('cno')."'");
			}
			if($i)
			{
				$_FILES['upload']['name'][$key]=$dname;
			}
		}
		
		$judge = uploadfiles($furl);
		if($judge!=1)
		{// 上传错误提示错误信息
			//$this->error($upload->getErrorMsg());
			$data['status'] = $judge;
		}else
		{// 上传成功
			//$model = new Model();//数据库插入记录
			foreach ($_FILES['upload']['name'] as $key => $value) {
					$file = $model->execute("insert into coursefile(fname,ftime,cno,furl) values('".$value."',sysdate(),'".I('cno')."','".$furl.$value."')");
					if($filenames)
					{
						$filenames = $filenames."，".$value;
					}else
					{
						$filenames = $value;
					}
				}
			$fno = mysql_insert_id();  //记录上传资料的编号
			if($file)
			{//上传成功，添加新鲜事
				//获取新鲜事的姓名和课程
				$n_name = M()->query("select tname from teacher where tno='".session('uid')."'");
				$n_cname = M()->query("select cname from course where cno='".I('cno')."'");
				
				//产生新鲜事
				$n_content = "<a href=\"#\">".$n_name[0]['tname']."</a>在课程<a href=".U('Index/Document/index',array('cno'=>I('cno'))).">".$n_cname[0]['cname']."</a>中上传了新课件：<br><a href=".U('Index/Document/index',array('cno'=>I('cno'))).">".$filenames."</a>";
				$Nresult = createNews(session('uid'), I('cno'), $n_content,4);

				if($Nresult)
				{
					$data['status']=1;				
					$data['fname'] = $_FILES['upload']['name'];
					$data['furl'] = $furl;
					//$this->success('上传成功，添加新鲜事成功');
				}
				else
				{
					$data['status']=3;
				}
			}
			else
			{
				$data['status']=0;
				//$this->error('上传失败');
			}
		}
		$this->ajaxReturn($data,'json');
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
	
	public function introduction()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
// 		
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
	
	public function updateIntroduction()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
		$this->display();
	}
	
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
			//$this->success('修改成功',U('Teacher/Course/introduction',array('cno' => I('cno'))));
			$data['status']=1;
		}
		else 
		{
			//$this->error('修改失败');
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	//无用控制器
	public function studentManage()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
// 		$this->cname = I('cname');
		
		$studentmodel = new Model();
		$this->student = $studentmodel->query("select * from stu_course,student where stu_course.sno=student.sno and stu_course.cno='".I('cno')."'");
		
		$this->display();
	}
	
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
					//$this->error('删除失败');
					$data['status']=0;
					$this->ajaxReturn($data,'json');
				}
			}
		}
		
		//$this->success('删除成功');
		$data['status']=1;
		$this->ajaxReturn($data,'json');
	}
	
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
// 		echo $addstudentmodel->getLastSql();die;
		if($addstudent)
		{
			//$this->success('添加成功');
			$data['status']=1;
		}
		else
		{
			$this->error('添加失败');
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
		$furl=iconv('UTF-8','GBK',$furl);

		$fname=I('fname');
		$fname=iconv('UTF-8','GBK',$fname);
		
		if (!file_exists($furl)){
            header("Content-type: text/html; charset=UTF-8");
            $this->error("文件不存在");
        } else {
            $file = fopen($furl,"r");
            
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: ".filesize($furl));
            Header("Content-Disposition: attachment; filename=\"".$fname."\";");
            echo fread($file, filesize($furl));
            fclose($file);
        }

	}

}

?>