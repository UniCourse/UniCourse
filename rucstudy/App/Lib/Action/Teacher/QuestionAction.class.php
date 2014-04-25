<?php 
/**
 * 
 * 老师课程提问控制器
 * created by hfr at 201402212227
 *
 */

class QuestionAction extends CommonCourseAction
{
	public function index()
	{
				
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from question where cno='" . $this->cno."'");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$questionmodel = new Model();
		$temp = $questionmodel->query("select * from question where cno='" . $this->cno."' order by raise_time DESC" . ' limit '. $page->firstRow.','.$page->listRows);

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

		$this->display();
	}

	//提问
	public function createQuestion() 
	{		
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$Qresult = $model->execute("insert into question(qtitle,cno,attnum,raise_sno,raise_time,rplynum,content,stu_tea) values('" . $_POST['title'] . "','" . I('cno') . "',0,'" . $_SESSION['uid'] . "',sysdate(),0,'" . $_POST['content'] . "','1')" );
		$qno = mysql_insert_id();  //记录新增问题编号
		if($Qresult)
		{//如果插入成功，添加新鲜事
			/**
			 * createNews为产生新鲜事的函数
			 */
			$n_contenturl = U('Index/Question/questionDetail',array('cno'=>I('cno'),'qno'=>$qno));
			$n_content = I('title');
			$Nresult = createNews(session('uid'), I('cno'), $n_content, $n_contenturl, 1,$qno);

			if($Nresult)
			{//如果新鲜事插入成功
				$data['status']=1;
				$data['info']="发布成功";
			}
			else{
				$data['status']=2;
				$data['info']="问题发布成功，但没有添加新鲜事";
			}
			//注意：这里的跳转有问题！！希望用Ajax实现提示，取消页面跳转
		}
		else
		{//如果插入失败
			$data['status']=3;
			$data['info']="问题插入失败";
		}
		$this->ajaxReturn($data,'json');
		
	}

	public function questionDetail() {
		$this->cno=I('cno');
		$this->cname = getCourseName(I('cno'));

		$model = new Model();
		$result = $model->execute("update mq_user set mq_user.is_usersread=1 where mq_user.userid='".session('uid')."' and mq_user.mid=(select mq.mid from mq where mq.mid=mq_user.mid and mq.qno=".I('qno').")");

		//回答分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from reply where qno=" . $_GET['qno']);
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();


		//查询输出结果
		$temp1 = $model->query("select * from question where qno='" . $_GET['qno'] . "'");
		$temp2 = $model->query("select * from reply where qno='" . $_GET['qno'] . "'"." order by rplytime DESC" . ' limit '. $page->firstRow.','.$page->listRows);
		$temp3 = $model->query("select * from remark,reply where qno='" . $_GET['qno'] . "' and remark.rpno=reply.rpno"." order by rmtime ASC");

		foreach ($temp1 as $key => $value) {
			$name1 = $model->query("select * from student where sno='".$value['raise_sno']."'");
			if(!$name1)
			{
				$name1 = $model->query("select * from teacher where tno='".$value['raise_sno']."'");
				$temp1[$key]['sname']=$name1[0]['tname'];
			}else
			{
				$temp1[$key]['sname']=$name1[0]['sname'];
			}
		}
		$this->question=$temp1;
		foreach ($temp2 as $key => $value) {
			$name2 = $model->query("select * from student where sno='".$value['rp_sno']."'");
			if(!$name2)
			{
				$name2 = $model->query("select * from teacher where tno='".$value['rp_sno']."'");
				$temp2[$key]['sname']=$name2[0]['tname'];
			}else
			{
				$temp2[$key]['sname']=$name2[0]['sname'];
			}
			$is_voted = $model->query("select * from is_voted where sno='" . $_SESSION['uid'] . "' and rpno='" . $value['rpno'] . "'");
			if(!$is_voted)
			{//如果没有赞过或踩过
				$temp2[$key]['is_up']=false;
				$temp2[$key]['is_down']=false;
			}
			else if($is_voted[0]['up_down'] == 0)
			{//如果已经踩过
				$temp2[$key]['is_up']=false;
				$temp2[$key]['is_down']=true;
			}
			else
			{//如果已经赞过
				$temp2[$key]['is_up']=true;
				$temp2[$key]['is_down']=false;
			}
		}
		$this->reply = $temp2;
		foreach ($temp3 as $key => $value) {
			$name3 = $model->query("select * from student where sno='".$value['rm_sno']."'");
			if(!$name3)
			{
				$name3 = $model->query("select * from teacher where tno='".$value['rm_sno']."'");
				$temp3[$key]['sname']=$name3[0]['tname'];
			}else
			{
				$temp3[$key]['sname']=$name3[0]['sname'];
			}
			
			$temp3[$key]['rmcontent']=parseAt($temp3[$key]['rmcontent']);
		}
		$this->remark = $temp3;

		//查询是否已关注该问题
		$isfocusmodel = new Model();
		$this->isFocus = $isfocusmodel->query("select * from q_attention where sno='" . $_SESSION["uid"] . "' and qno='" . $_GET['qno'] . "'");
		
		//查询是否已感谢该问题
		$txmodel = new Model();
		$this->is_thx = $txmodel->query("select * from is_thx where sno='" . session('uid')."'");
		
		//显示模板
		$this->display();
	}

	//添加回复
	public function replyQuestion() {		
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("insert into reply(qno,rpcontent,rp_sno,thsnum,upno,downno,rplytime,weight,stu_tea) values('" . $_POST['qno'] . "','" . $_POST['content'] . "','" . $_SESSION['uid'] . "',0,0,0,sysdate(),0,'1')" );
		
		//判断是否插入成功
		if($result)
		{
			/**
			 * 生成消息
			 * Modified by hfr at 201402091545
			 */
			if(createReplyMessage(I('qno')))
			{
				$data['status']=1;
				$this->ajaxReturn($data,'json');
			}else{
				$data['status']=3;
				$this->ajaxReturn($data,'json');
			}
			
		}
		else
		{//如果插入失败
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}
		
	}

	//删除回复
	public function deleteReply()
	{
		$model = new Model();

		$result = $model->execute("delete from reply where rpno=".I('rpno'));

		if($result)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=2;
		}
		$this->ajaxReturn($data,'json');
	}

	//删除评论
	public function deleteRemark()
	{
		$model = new Model();

		$result = $model->execute("delete from remark where rmno=".I('rmno'));

		if($result)
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

	//增加评论
	public function createRemark() {
		
		
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("insert into remark(rpno,rmcontent,rm_sno,rmtime,stu_tea) values(" . $_POST['rpno'] . ",'" . $_POST['content'] . "','" . $_SESSION['uid'] . "',sysdate(),1)" );

		if($result)
		{//如果插入成功
			$rmno=mysql_insert_id();
			$data['rmcontent']=handleAt($_POST['content']);
			$data['rmno']=$rmno;
			$rmtime = $model->query("select * from remark where rmno=".$rmno);
			$data['rmtime']=$rmtime[0]['rmtime'];

			/*找出rpno对应的qno，然后再找出问题提出者*/
			$rpno=$_POST['rpno'];//回答编号
			$tmp1=$model->query("select rp_sno,qno from reply where rpno='".$rpno."'");
			$qno=$tmp1[0]['qno'];//问题编号
			/**
			 * 生成消息
			 * Modified by hfr at 201402091617
			 */
			if(createCommentMessage($qno,I('rpno')))
			{
				$data['status']=1;
				$this->ajaxReturn($data,'json');
			}else{
				$data['status']=3;
				$this->ajaxReturn($data,'json');
			}
		}
		else
		{//如果插入失败
			$data['status']=2;
		}
		$this->ajaxReturn($data,'json');
		
	}

	//关注/取消关注函数
	public function focus()
	{//关注问题

		$model = new Model(); // 实例化一个model对象没有对应任何数据表
		//检查是否已经关注过
		$is_focus = $model->query("select * from q_attention where sno='" . session('uid') . "' and qno=" . I('qno'));
		if(!$is_focus)
		{
			$result = $model->execute("insert into q_attention(sno,qno,att_time) values('" . session('uid') . "'," . I('qno') . ",sysdate())");

			if($result)
			{//如果插入成功
				$data['status']=1;
				$this->ajaxReturn($data,'json');
			}
			else
			{//如果插入失败
				$data['status']=2;
				$this->ajaxReturn($data,'json');
			}
		}
		else
		{//如果尝试重复关注
			$data['status']=3;
				$this->ajaxReturn($data,'json');
		}
	
	}
	
	//取消关注
	public function unFocus()
	{

		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		//检查是否已经关注过
		$is_focus = $model->query("select * from q_attention where sno='" . session('uid') . "' and qno=" . I('qno'));
		if($is_focus)
		{
			$result = $model->execute("delete from q_attention where sno='" . session('uid') . "' and qno=" . I('qno'));

			if($result)
			{//如果插入成功
				$data['status']=1;
				$data['info']="操作成功";
				$this->ajaxReturn($data,'json');
			}
			else
			{//如果插入失败
				$data['status']=2;
				$data['info']="操作失败";
				$this->ajaxReturn($data,'json');
			}
		}
		else 
		{
			$data['status']=3;
			$data['info']="未关注该问题";
		}
	}
	public function deleteQuestion()
	{
		//验证提问是否属于该学生
		if(!questionverify(I('qno'))){
			$data['status']=2;
			$data['info']="权限认证失败，可能不是您提出的问题";
			$this->ajaxReturn($data,'json');
		}

		$model = new Model();
		$result = $model->execute("delete from question where qno=".I('qno'));

		M()->execute("delete from news where n_contentid=".I('qno'));

		if($result)
		{
			$data['status']=1;
			$data['info']="删除成功";
		}else
		{
			$data['info']="删除失败";
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	public function getCourseUserList(){
		$cno=I('cno');
		$data['user']=M()->query("select student.sno,student.sname from student,stu_course where stu_course.sno=student.sno and stu_course.cno='".$cno."'");
		$data['status']=0;
		$this->ajaxReturn($data);

	}
	public function up() {
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		//查询是否赞过或踩过
		$is_voted = $model->query("select * from is_voted where sno='" . $_SESSION['uid'] . "' and rpno='" . I('rpno') . "'");
		if(!$is_voted)
		{//如果没有赞过或踩过
			$result = $model->execute("insert into is_voted(sno,rpno,up_down) values('" . $_SESSION['uid'] . "'," . I('rpno') . ",'1')");
			if($result)
			{//如果插入成功
				$data['status']=1;
				// $this->success('点赞成功');
			}
			else
			{//如果插入失败
				$data['status']=0;
				// $this->error('点赞失败');
			}
		}
		else if($is_voted[0]['up_down'] == 0)
		{//如果已经踩过
			$result = $model->execute("update is_voted set up_down=1 where sno='". $_SESSION['uid'] . "' and rpno='" . I('rpno') . "'");
			if($result)
			{//如果插入成功
				$data['status']=1;
				// $this->success('点赞成功');
			}
			else
			{//如果插入失败
				$data['status']=0;
				// $this->error('点赞失败');
			}
		}
		else
		{//如果已经赞过
			$result=$model->execute("delete from is_voted where sno='" . session('uid') . "' and rpno='" . I('rpno') . "'");
			if($result) $data['status']=1;
			else $data['status']=0;
		}
		$upno = $model->query("select upno from reply where rpno=" . I('rpno'));
		$downno = $model->query("select downno from reply where rpno=" . I('rpno'));
		$data['upno']=$upno[0]['upno'];
		$data['downno']=$downno[0]['downno'];

		$this->ajaxReturn($data,'json');
	}
	
}

?>