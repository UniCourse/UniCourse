<?php 
/**
 * 小组控制器
 * by hfr
 * Modified by slr
 */

class GroupAction extends CommonAction
{
	/**
	 * 小组首页
	 * by hfr at 201402081632
	 */
	public function index($cno)
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
		$groupmodel = new Model();
		$this->group = $groupmodel->query("select * from groups where cno='" . $this->cno."'");
		// p($this->group);die;
		
		$grouppartnermodel = new Model();
		$this->grouppartner = $grouppartnermodel->query("select * from groups,stu_grp where stu_grp.gno=groups.gno and groups.cno='" . $this->cno . "' and stu_grp.sno=" . session('uid'));
				
		$this->display();
	}

	public function mygroup()
	{
		$groupmodel = new Model();
		$this->group = $groupmodel->query('select * from course,groups,stu_grp where course.cno=groups.cno and stu_grp.gno=groups.gno and stu_grp.sno='.session('uid'));
		
		$coursemodel = new Model();
		$this->course = $coursemodel->query('select * from course,stu_course where course.cno=stu_course.cno and stu_course.sno='.session('uid'));

		$this->display();
	}
	
	public function home($gno)
	{		
		//权限验证
		if(!groupverify(I('gno')))
		{
			$this->error('没有操作权限');
		}

		$this->gno = I('gno');
		$this->gname = getGroupName($this->gno);

		//小组新鲜事分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query('select count(*) from grpnews where grpnews.gno='.I('gno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		//小组新鲜事
		$groupnewsmodel = new Model();
		$this->groupnews = $groupnewsmodel->query("select * from student,grpnews where student.sno=grpnews.n_sno and grpnews.gno=".I('gno').' order by n_time DESC' . ' limit '. $page->firstRow.','.$page->listRows);
		

		//小组添加成员
		$partnermodel = new Model();
		$this->partner = $partnermodel->query("select * from student,stu_grp where student.sno=stu_grp.sno and stu_grp.gno=".I('gno'));
		
		$usermodel = new Model();
		$a = $this->user = $usermodel->query("select * from stu_course,groups,student where stu_course.sno=student.sno and groups.cno=stu_course.cno and groups.gno=".I('gno'));
		
		
		for($i=0;$i<count($this->user);$i++)
		{
			for($j=0;$j<count($this->partner);$j++)
			{
				
				if($this->user[$i]['sno']==$this->partner[$j]['sno'])
				{
					unset($a[$i]);
					break;
				}
			}
		}
		$this->user = $a;


		/**	我把File也放在了首页
		* Modified by slr
		*/

		$filenamemodel = new Model();
		$this->filename = $filenamemodel->query("select * from grpfile where gno=".I('gno')." order by ftime DESC");

		/**
		 * 本人的其他小组
		 * Modified by hfr at 2013.9.6 15:11
		 */
		$model = new Model();
		$this->mygroup = $model->query("select * from groups,stu_grp where stu_grp.gno=groups.gno and stu_grp.sno=".session('uid'));

		$this->display();
	}

	public function addGroupNews()
	{
		$model = new Model();
		$result = $model->execute("insert into grpnews(gno,n_sno,n_content,n_time) values(".I('gno').",'".session('uid')."','".$_POST['content']."',sysdate())");

		if($result)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	public function deleteGroupNews()
	{
		$model = new Model();
		$result = $model->execute("delete from grpnews where n_no=".I('n_no'));

		if($result)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	public function upload()
	{
// 		//权限验证
// 		if(!groupverify(I('gno')))
// 		{
// 			$data['status']=2;
// 			$this->ajaxReturn($data,'json');
// 		}

// 		import('ORG.Net.UploadFile');
// 		$upload = new UploadFile();// 实例化上传类
// 		$upload->maxSize  = 31457280 ;// 设置附件上传大小
// // 		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
// // 		$upload->autoSub = true;
// // 		$upload->subType = 'custom';
// // 		$upload->subDir = 'Group';
// 		$upload->saveRule = '';
		mkdir('./Uploads/Group/',0777);
		mkdir('./Uploads/Group/'. I('gno') .'/',0777);
		$furl =  './Uploads/Group/'. I('gno') .'/';// 设置附件上传目录

		$filemodel = new Model();
		foreach ($_FILES['upload']['name'] as $key => $value) {
			$i=0;
			$exist = $filemodel->query("select * from grpfile where fname='".$value."' and gno=".I('gno'));
			while($exist)
			{
				$i++;
				$dname="(".$i.")".$value;
				$exist = $filemodel->query("select * from grpfile where fname='".$dname."' and gno=".I('gno'));
			}
			if($i)
			{
				$_FILES['upload']['name'][$key]=$dname;
			}
		}

		
		$judge = uploadfiles($furl);
		if(!$judge[0]['savename'])
		{// 上传错误提示错误信息
			$data['status'] = $judge;
			$this->ajaxReturn($data,'json');
		}else
		{// 上传成功
			//$filemodel = new Model();//数据库插入记录
			$model = new Model();
			foreach ($_FILES['upload']['name'] as $key => $value)
			{
				$data[$key]="insert into grpfile(fname,ftime,gno,gurl,is_toclass) values('".$value."',sysdate(),".I('gno').",'".$furl.$judge[0]['savename']."',0)";
				$file = $model->execute("insert into grpfile(fname,ftime,gno,gurl,is_toclass) values('".$value."',sysdate(),".I('gno').",'".$furl.$judge[0]['savename']."',0)");
				if($filenames)
					{
						$filenames = $filenames."，".$value;
					}else
					{
						$filenames = $value;
					}
				
			}

			if($file)
			{
				$data['status']=1;				
				$data['fname'] = $_FILES['upload']['name'];
				$data['furl'] = $furl;
				$news = $model->execute("insert into grpnews(gno,n_sno,n_content,n_time) values(".I('gno').",'".session('uid')."','上传了新文件：".$filenames."',sysdate())");
			}
			else 
			{
				$data['status']=0;
			}
		}
		$this->ajaxReturn($data,'json');
	}
	
	public function deleteGroupFile()
	{
		//权限验证
		if(!groupverify(I('gno')))
		{
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}

		$filenamemodel = new Model();
		$filename = $filenamemodel->query("select * from grpfile where fno=".I('fno'));
		
		//中文转码
		$filename[0]['gurl']=iconv('UTF-8','GB2312',$filename[0]['gurl']);
		//删除文件
		$deleteflag = unlink($filename[0]['gurl']);
		
		$deletefilemodel = new Model();
		$fname = $deletefilemodel->query("select * from grpfile where fno=".I('fno'));
		$deletefile = $deletefilemodel->execute("delete from grpfile where fno=".I('fno'));
		
		if($deletefile&&$deleteflag)
		{
			//$this->success('删除成功');
			$model = new Model();
			$news = $model->execute("insert into grpnews(gno,n_sno,n_content,n_time) values(".I('gno').",'".session('uid')."','删除文件：".$fname[0]['fname']."',sysdate())");
			$data['status']=1;
		}
		else
		{
			//$this->error('删除失败');
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	public function addAction()
	{
		if(!courseverify(I('cno')))
		{
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}
		
		$cno1 = I('cno');
		$gname1 = I('gname');
		$gintro1 = I('gintro');
		
		//新建小组
 		$groupmodel = new Model();
 		$result1 = $groupmodel->execute("insert into groups(cno,gname,gnum,gintro) values(".$cno1.",'".$gname1."',0,'".$gintro1."')");
		
 		//查询新建小组
		$groupnamemodel = new Model();
		$groupname = $groupnamemodel->query("select * from groups where gname='".$gname1."'");
		$gno = $groupname[0]['gno'];
		
		//增加学生-小组关系
		$result2 = $groupmodel->execute("insert into stu_grp(sno,gno) values('".session('uid')."',".$gno.")");

		if( ($result1)&&($result2) )
		{
			$data['status']=1;
			$this->ajaxReturn($data,'json');
		}
		else
		{
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}
	}
	
	public function addUserHandle()
	{
		//权限验证
		if(!groupverify(I('gno')))
		{
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}

		$addmodel = new Model();
		$model = new Model();
		
		foreach($_POST as $key => $value)
		{
			if($value=='on')
			{
				if(!($addmodel->execute("insert into stu_grp(sno,gno) values('".$key."',".I('gno').")")))
				{
					$data['status']=0;
					$this->ajaxReturn($data,'json');
				}else
				{
					$name = $model->query("select sname from student where sno=".$key);
					if($content=='')
					{
						$content = $content.$name[0]['sname'];
					}else
					{
						$content = $content."，".$name[0]['sname'];
					}
					
				}
			}
		}

		$result = $model->execute("insert into grpnews(gno,n_sno,n_content,n_time) values(".I('gno').",'".session('uid')."','添加新成员：".$content."',sysdate())");
		
		$data['status']=1;
		$this->ajaxReturn($data,'json');
	}
	
	public function quit()
	{
		//权限验证
		if(!groupverify(I('gno')))
		{
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}

		$quitmodel = new Model();
		$quit = $quitmodel->execute("delete from stu_grp where sno=".session('uid')." and gno=".I('gno'));
		
		if($quit)
		{
			//若小组内没有成员，则删除小组
			$querygrpmodel = new Model();
			$querygrp = $querygrpmodel->query("select * from stu_grp where gno=".I('gno'));
			if(!$querygrp)
			{
				$deletegrpmodel = new Model();
				$deletegrp = $deletegrpmodel->execute("delete from groups where gno=".I('gno'));
				$data['status']=1;
			}
			else {
				$data['status']=1;
				$model = new Model();
				$name=$model->query("select sname from student where sno='".session('uid')."'");
				$news = $model->execute("insert into grpnews(gno,n_sno,n_content,n_time) values(".I('gno').",'".session('uid')."','".$name[0]['sname']."退出了本小组',sysdate())");
			}
		}
		else{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	public function getGroupUserList(){
		$gno=I('gno');
		$data['status']=0;
		$data['user']=M()->query("select * from student,stu_grp where stu_grp.sno=student.sno and stu_grp.gno='".$gno."'");
		return $data;

	}
}
?>