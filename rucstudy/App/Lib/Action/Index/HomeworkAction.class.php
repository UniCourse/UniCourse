<?php
/**
 * 作业控制器
 * by hfr
 */

class HomeworkAction extends CommonAction
{
	/**
	 * 课程作业主页
	 * by hfr at 201402081644
	 */
	public function index($cno)
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->hno = I('hno');


		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from homework where homework.cno='".I('cno')."' order by htime DESC");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$homeworkmodel = new Model();
		$temp = $homeworkmodel->query("select * from homework where homework.cno='".I('cno')."' order by htime DESC" . ' limit '. $page->firstRow.','.$page->listRows);

		foreach($temp as $key => $value){
			
			$homeworksubmitmodel = new Model();
			$this->homeworksubmit = $homeworksubmitmodel->query("select * from stu_homework where sno='".session('uid')."' and hno=".$value['hno']);
			//p($this->homeworksubmit);
			if($this->homeworksubmit)
			{
				$this->issubmit=1;
			}
			else 
			{
				$this->issubmit=0;
			}
			
			$homeworkfilemodel = new Model();
			$this->homeworkfile = $homeworkfilemodel->query("select * from homeworkfile where hno=".$value['hno']);
			
			$temp[$key]['issubmit'] = $this->issubmit;
			$temp[$key]['fname'] = $this->homeworksubmit[0]['fname'];
			$temp[$key]['furl'] = $this->homeworksubmit[0]['furl'];
			$temp[$key]['fno'] = $this->homeworksubmit[0]['fno'];
			$temp[$key]['homeworkfile'] = $this->homeworkfile;

			// array_push($this->homework[$key],$this->issubmit);
		}
		$this->homework = $temp;
		//p($temp);die;

		$this->display();
	}

	public function upload()
	{
		// if(!courseverify(I('cno')))
		// {
		// 	$data['status']=3;
		// 	$this->ajaxReturn($data,'json');
		// }
		
		
		// import('ORG.Net.UploadFile');
		// $upload = new UploadFile();// 实例化上传类
		// $upload->maxSize  = 31457280 ;// 设置附件上传大小
		// //$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		// //$upload->autoSub = true;
		// //$upload->subType = 'custom';
		// //$upload->subDir = 'Group';
		// $upload->saveRule = '';

		//创建目录
		mkdir('./Uploads/Course/',0777);
		mkdir('./Uploads/Course/'. I('cno') .'/',0777);
		mkdir('./Uploads/Course/'. I('cno') .'/'.I('hno').'/',0777);
		mkdir('./Uploads/Course/'. I('cno') .'/'.I('hno').'/'.session('uid').'/',0777);
		$furl = './Uploads/Course/'. I('cno') .'/'.I('hno').'/'.session('uid').'/';
		// $upload->savePath =  './Uploads/Course/'. I('cno') .'/'.I('hno').'/'.session('uid').'/';// 设置附件上传目录
		$data['furl']=$furl;
		$judge = uploadfiles($furl);
		if(!$judge[0]['savename'])
		{// 上传错误提示错误信息
			//$data['status'] = $upload->getErrorMsg();
			$data['status'] = $judge;
		}else
		{// 上传成功
			$filemodel = new Model();//数据库插入记录
			$file = $filemodel->execute("insert into stu_homework(sno,fname,ftime,hno,furl) values('".session('uid')."','".$_FILES['upload']['name']."',sysdate(),".I('hno').",'".$furl.$judge[0]['savename']."')");
			if($file)
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
		$data['time']=date('Y-m-d H:i:s',time());
		$this->ajaxReturn($data,'json');
	}

	public function deleteHomework()
	{
		$this->cno=I('cno');
		$this->hno=I('hno');
		
		$filenamemodel = new Model();
		$filename = $filenamemodel->query("select * from stu_homework  where sno='".session('uid')."' and hno=".I('hno'));
		
		$filedeletemodel = new Model();//数据库插入记录
		$filedelete = $filedeletemodel->execute("delete from stu_homework where sno='".session('uid')."' and hno=".I('hno'));
		
		//中文转码
		$filename[0]['fname']=iconv('UTF-8','GB2312',$filename[0]['fname']);
		//删除文件
		$deleteflag = unlink('./Uploads/Course/'. I('cno') .'/'.I('hno').'/'.session('uid').'/'.$filename[0]['fname']);
		
		if($deleteflag&&$filedelete)
		{
			//$this->success('删除成功！');
			$data['status']=1;
		}
		else
		{
			//$this->error('删除失败');
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

}
?>