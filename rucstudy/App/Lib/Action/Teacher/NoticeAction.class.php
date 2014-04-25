<?php 
/**
 * 
 * 老师课程公告控制器
 * created by hfr at 201402212227
 *
 */

class NoticeAction extends CommonCourseAction
{
	//公告首页
	public function index()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		
		$homemodel = new Model();
		$this->home = $homemodel->query("select * from notice where cno='".I('cno') . "' order by ntime DESC  ");
		
		$this->display();
	}

	//删除公告
	public function deleteNotice()
	{
		$deletenoticemodel = new Model();
		$deletenotice = $deletenoticemodel->execute("delete from notice where nno=".I('nno'));
		$deletenews = M()->execute("delete from news where n_contentid=".I('nno'));
		
		if($deletenotice&&$deletenews)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=2;
			$data['info']="删除失败";
		}
		$this->ajaxReturn($data,'json');
	}

	//添加公告
	public function addNotice()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		$n_name = M()->query("select tname from teacher where tno='".session('uid')."'");
		$n_cname = M()->query("select cname from course where cno='".I('cno')."'");

		$model = new Model();
		$addnotice = $model->execute("insert into notice(ntime,ncontent,cno) values(sysdate(),'".I('notice')."','".I('cno')."')");
		$nno=mysql_insert_id(); 	
		if($addnotice)
		{//如果公告添加成功，添加公告新鲜事
			$data['content']=I('notice');
			$data['time']=date('Y-m-d H:i:s',time());	
			$data['nno']=$nno;
			/**
			 * createNews为产生新鲜事的函数
			 */
			$n_contenturl = U('Index/Notice/index',array('cno'=>I('cno')));
			$n_content = I('notice');
			$addcourse_news = createNews(session('uid'), I('cno'), $n_content, $n_contenturl, 2, $nno);
			
			if($addcourse_news)
			{
				$data['status']=1;
			}
			else
			{
				$data['status']=2;
			}
		}
		else
		{
			$data['status']=3;
			$data['info']="发布失败";
		}
		$this->ajaxReturn($data,'json');
	}
}

?>