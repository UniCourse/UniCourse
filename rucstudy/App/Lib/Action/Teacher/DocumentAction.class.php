<?php 
/**
 * 
 * 老师课程课件控制器
 * created by hfr at 201402212227
 *
 */

class DocumentAction extends CommonCourseAction
{
	public function index()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
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

	//上传课件
	public function uploadDocument()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

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
		if(!$judge[0]['savename'])
		{// 上传错误提示错误信息
			$data['status'] = 5;
			$data['info']=$judge;
		}else
		{// 上传成功
			foreach ($_FILES['upload']['name'] as $key => $value) {
					$file = $model->execute("insert into coursefile(fname,ftime,cno,furl) values('".$value."',sysdate(),'".I('cno')."','".$furl.$judge[0]['savename']."')");
					if($filenames)
					{
						$filenames = $filenames."，".$value;
					}else
					{
						$filenames = $value;
					}
				}
			$fno = mysql_insert_id();  //记录上传资料的编号
			$data[$key]['ftime']=date('Y-m-d H:i:s',time());
			$data[$key]['fno']=$fno;
			$data[$key]['fname']=$value;
			$data[$key]['furl']=$furl.$value;
			if($file)
			{//上传成功，添加新鲜事
				/**
				 * createNews为产生新鲜事的函数
				 */
				$n_contenturl = U('Index/Document/index',array('cno'=>I('cno')));
				$n_content = $filenames;
				$Nresult = createNews(session('uid'), I('cno'), $n_content, $n_contenturl, 4,$fno);

				if($Nresult)
				{
					$data['status']=1;	
					$data['info']="上传成功";								
				}
				else
				{
					$data['status']=2;
					$data['info']="上传成功，但未添加新鲜事";
				}
			}
			else
			{
				$data['status']=4;
				$data['info']="记录失败";
			}
		}
		$this->ajaxReturn($data,'json');
	}

	//删除课件
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

		$delnews = M()->execute("delete from news where n_contentid=".I('fno'));
		
		if($deletefile&&$deleteflag)
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