<?php 

/**
 * 
 * 学生首页控制器
 * @author hfr & tyy
 *
 */
class HomeAction extends CommonAction
{
	public function index()
	{
		//查询学生选修课程
		$coursemodel = new Model();
		$this->course = $coursemodel->query("select stu_course.sno, course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno=".session('uid'));
		
		//查询日程提醒
		$schedulemodel = new Model();
		$this->schedule = $schedulemodel->query("select * from sch,stu_sch where sch.sdno=stu_sch.sdno and stu_sch.sno=" . session('uid')." and rdeadline>=sysdate() order by rdeadline ASC limit 0,5");

		$page = 0;

		$this->display();  //显示模板
	}

	public function morenews()
	{
		$page = I('page');
		$type = I('type');
		$cno = I('cno');

		if($type==0&&$cno==0)
		{
			$result = M()->query("select * from news,stu_course where news.n_cno=stu_course.cno and stu_course.sno = '".session('uid')."' order by n_time DESC". ' limit '. ($page).',10');
		}else{
			if($type==0&&$cno!=0)
			{
				$result = M()->query("select * from news where news.n_cno='".I('cno')."' order by n_time DESC". ' limit '. ($page).',10');
			}
			if($type!=0&&$cno==0)
			{
				$result = M()->query("select * from news,stu_course where news.n_cno=stu_course.cno and stu_course.sno = '".session('uid')."' and news.n_type=".I('type')." order by n_time DESC". ' limit '. ($page).',10');
			}
			if($type!=0&&$cno!=0)
			{
				$result = M()->query("select * from news where news.n_cno='".I('cno')."' and news.n_type=".I('type')." order by n_time DESC". ' limit '. ($page).',10');				
			}
		}

		if($result){
			$data['status'] = 0;
			$data['data'] = $result;
		}
		else 
			$data['status']=1;
		$this->ajaxReturn($data,'json');
	}

	/**
	 * ajax获取消息
	 * modified by hfr at 201402091830
	 */
	public function getmsg()
	{
		$model = new Model();
		$message = $model->query("select * from message,message_user where message.m_id=message_user.m_id and message_user.m_uid='".session('uid')."' and message_user.is_read=0");

		$data['message']=$message;

		$this->ajaxReturn($data,'json');

	}

	//显示历史消息
	public function showmsg()
	{
		//新鲜事分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from message,message_user where message_user.m_uid='".session('uid')."' and message.m_id=message_user.m_id");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$model = new Model();
		$this->message = $model->query("select * from message,message_user where message_user.m_uid='".session('uid')."' and message.m_id=message_user.m_id order by m_time DESC limit ". $page->firstRow.",".$page->listRows);
		
		$this->display();
	}

	/**
	 * 删除消息
	 */
	public function deleteMessage()
	{
		$mid = I('mid');

		$result = M()->execute("update message_user set is_read = 1 where m_id=".$mid);

		if($result){
			$data['status']=1;
		}else{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 输入@后显示课程名单
	 * created by hfr at 201402192335
	 */
	public function getCourseNameList()
	{
		$input = I('input');
		$cno = I('cno');

		$names = atCourseNameList($input,$cno);
		$data['names'] = $names;

		$this->ajaxReturn($data,'json');
	}

	/**
	 * 输入@后显示小组名单
	 * created by hfr at 201402192335
	 */
	public function getGroupNameList()
	{
		$input = I('input');
		$gno = I('gno');

		$names = atGroupNameList($input,$gno);
		$data['names'] = $names;

		$this->ajaxReturn($data,'json');
	}

}
?>