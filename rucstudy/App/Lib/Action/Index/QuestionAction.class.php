<?php 
/**
 * 问答控制器
 * created by hfr
 * modified by tyy
 */

class QuestionAction extends CommonAction
{
	/**
	 * 课程提问
	 * by hfr at 201402081629
	 */
	public function index($cno)
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
		$temp = $questionmodel->query("select * from question where cno='" . $this->cno."' order by raise_time DESC" . " limit ". $page->firstRow.",".$page->listRows);


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

	//所有问题
	public function allQuestion()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from question");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		//查询所有问题
		$questionmodel = new Model();
		$temp = $questionmodel->query('select * from question,course where question.cno=course.cno order by raise_time DESC' . ' limit '. $page->firstRow.','.$page->listRows);

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

		$coursemodel = new Model();
		$this->course = $coursemodel->query("select course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno=".session('uid'));		
		
		$this->display();
	}
	
	//我关注的问题
	public function myFocusQuestion()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query('select count(*) from question,q_attention where question.qno=q_attention.qno and q_attention.sno=' . session('uid'));

		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$questionmodel = new Model();
		$temp = $questionmodel->query('select * from question,course,q_attention where question.cno=course.cno and question.qno=q_attention.qno and q_attention.sno=' . session('uid').' order by raise_time DESC' . ' limit '. $page->firstRow.','.$page->listRows);

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

		$coursemodel = new Model();
		$this->course = $coursemodel->query("select course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno=".session('uid'));

		$this->display();
	}
	
	//我提出的问题
	public function myQuestion()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		
		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from question where raise_sno=" . session('uid'));

		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$myquestionmodel = new Model();
		$temp = $myquestionmodel->query('select * from student,question,course where question.cno=course.cno and student.sno=question.raise_sno and raise_sno=' . session('uid')." order by raise_time DESC" . ' limit '. $page->firstRow.','.$page->listRows);
		
		foreach ($temp as $key => $value) {
			$result = $myquestionmodel->query("select * from q_attention where sno='".session('uid')."' and qno=".$value['qno']);
			if($result)
			{
				$temp[$key]['isFocus']=1;
			}else
			{
				$temp[$key]['isFocus']=0;
			}
		}
		$this->question = $temp;

		$coursemodel = new Model();
		$this->course = $coursemodel->query("select course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno=".session('uid'));
		
		$this->display();
	}

	/**
	 * 提问详细
	 * Modified by hfr at 201402081934
	 */
	public function questionDetail($qno,$mid='') {
		$this->cno=I('cno');
		$this->cname = getCourseName(I('cno'));

		$check = M()->query("select * from question where qno = ".I('qno'));
		if(!$check){
			$this->error("没有该问题");
		}

		//将消息设为已读
		$tmp=readMessage(I('qno'));
		//回答分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from reply where qno='" . $_GET['qno'] . "'");

		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$model = new Model();
		//查询输出结果
		$temp1 = $model->query("select * from question where qno=" . $_GET['qno']);
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
				$is_voted = $model->query("select * from is_voted where sno='" . $_SESSION['uid'] . "' and rpno='" . I('rpno') . "'");
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
		
		//显示模板
		$this->display();
	}

	/**
	 * 插入问题
	 */
	public function createQuestion() {
		if(!courseverify(I('cno')))
		{
			$data['status']=3;
			$this->ajaxReturn($data,'json');
		}

		//获取新鲜事的姓名和课程
		$n_name = M()->query("select sname from student where sno='".session('uid')."'");
		$n_cname = M()->query("select cname from course where cno='".I('cno')."'");

		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$Qresult = $model->execute("insert into question(qtitle,cno,attnum,raise_sno,raise_time,rplynum,content) values('" . I('title') . "','" . I('cno') . "',0,'" . session('uid') . "',sysdate(),0,'" . $_POST['content'] . "')" );
		

		if($Qresult)
		{//如果插入成功
			/**
			 * createNews为产生新鲜事的函数
			 */
			$qno=$model->query("select max(qno) from question;");
			$n_contenturl = U('Index/Question/questionDetail',array('cno'=>I('cno'),'qno'=>mysql_insert_id()));
			$n_content = I('title');
			$Nresult = createNews(session('uid'), I('cno'), $n_content, $n_contenturl, 1,$qno[0]['max(qno)']);

			if($Nresult)
			{//问题插入成功，新鲜事插入成功
				$data['status']=1;
				$this->ajaxReturn($data,"json");
			}
			else
			{//问题插入成功，新鲜事插入失败
				$data['status']=2;
				$this->ajaxReturn($data,"json");
			}
			
		}
		else
		{//如果问题插入失败
			$data['status']=0;
			$this->ajaxReturn($data,"json");
		}	
		
	}

	/**
	 * 添加回复
	 */
	public function replyQuestion() {
		
		//插入回答
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("insert into reply(qno,rpcontent,rp_sno,thsnum,upno,downno,rplytime,weight) values('" . $_POST['qno'] . "','" . $_POST['content'] . "','" . $_SESSION['uid'] . "',0,0,0,sysdate(),0)" );
		
		//判断是否插入成功
		if($result)
		{
			/**
			 * 生成消息
			 * Modified by hfr at 201402091545
			 */
			if(createQuestionMessage(I('qno')))
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
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}
		
	}

	/**
	 * 回答评论
	 */
	public function commentQuestion() {
		
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("insert into remark(rpno,rmcontent,rm_sno,rmtime) values(" . $_POST['rpno'] . ",'" . $_POST['content'] . "','" . $_SESSION['uid'] . "',sysdate())" );
		$rmno=mysql_insert_id();
		/**
		 * 返回时间
		 * Modified by hfr at 2013.9.6 23:11
		 */
		$data['rmcontent']=handleAt($_POST['content']);
		$data['rmno']=$rmno;
		$rmtime = $model->query("select * from remark where rmno=".$rmno);
		$data['rmtime']=$rmtime[0]['rmtime'];

		if($result)
		{//如果插入成功
		
			/*找出rpno对应的qno，然后再找出问题提出者*/
			$rpno=$_POST['rpno'];//回答编号
			$tmp1=$model->query("select rp_sno,qno from reply where rpno='".$rpno."'");
			$qno=$tmp1[0]['qno'];//问题编号
			
			/**
			 * 生成消息
			 * Modified by hfr at 201402091617
			 */
			if(createQuestionMessage($qno))
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
	
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}
		
	}

	/**
	 * 关注/取消关注函数
	 */
	public function focus()
	{//关注问题

		$model = new Model(); // 实例化一个model对象没有对应任何数据表
		//检查是否已经关注过
		$is_focus = $model->query("select * from q_attention where sno='" . session('uid') . "' and qno='" . I('qno') . "'");
		if(!$is_focus)
		{
			$result = $model->execute("insert into q_attention(sno,qno,att_time) values('" . session('uid') . "'," . I('qno') . ",sysdate())");

			if($result)
			{//如果插入成功
				$this->success('关注成功',U('Teacher/ShowQuestion/index',array('qno' => $_GET['qno'])));
			}
			else
			{//如果插入失败
				$this->error('关注失败');
			}
		}
		else
		{//如果尝试重复关注
			$this->error('您已关注过该问题');
		}
	
	}
	
	//取消关注
	public function unFocus()
	{

		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		//检查是否已经关注过
		$is_focus = $model->query("select * from q_attention where sno='" . session('uid') . "' and qno='" . I('qno') . "'");
		if($is_focus)
		{
			$result = $model->execute("delete from q_attention where sno='" . session('uid') . "' and qno='" . I('qno') . "'");

			if($result)
			{//如果插入成功
				//$this->success('取消关注成功',U('Index/ShowQuestion/index',array('qno' => $_GET['qno'])));
				$data['status']=1;
				$this->ajaxReturn($data,'json');
			}
			else
			{//如果插入失败
				//$this->error('取消关注失败');
				$data['status']=0;
				$this->ajaxReturn($data,'json');
			}
		}
		else 
		{
			$this->error('您未关注过该问题');
		}
	}
	
	/**
	 * 赞同回复	
	 * 
	 */
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
	
	
	/**
	 * 不赞同回复
	 * 
	 */	
	public function down() {
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		//查询是否赞过或踩过
		$is_voted = $model->query("select * from is_voted where sno='" . $_SESSION['uid'] . "' and rpno='" . I('rpno') . "'");
		if(!$is_voted)
		{//如果没有赞过或踩过
			$result = $model->execute("insert into is_voted(sno,rpno,up_down) values('" . $_SESSION['uid'] . "'," . I('rpno') . ",'0')");
			if($result)
			{//如果插入成功
				$data['status']=1;
				// $this->success('点踩成功');
			}
			else
			{//如果插入失败
				$data['status']=0;
				// $this->error('点踩失败');
			}
		}
		else if($is_voted[0]['up_down'] == 1)
		{//如果已经赞过
			$result = $model->execute("update is_voted set up_down=0 where sno='". $_SESSION['uid'] . "' and rpno='" . I('rpno') . "'");
			if($result)
			{//如果插入成功
				$data['status']=1;
				// $this->success('点踩成功');
			}
			else
			{//如果插入失败
				$data['status']=0;
				// $this->error('点踩失败');
			}
		}
		else
		{//如果已经踩过
			$data['status']=2;
			// $this->error('您已踩过该回答');
		}
		$upno = $model->query("select upno from reply where rpno=" . I('rpno'));
		$downno = $model->query("select downno from reply where rpno=" . I('rpno'));
		$data['upno']=$upno[0]['upno'];
		$data['downno']=$downno[0]['downno'];

		$this->ajaxReturn($data,'json');
		
	}

	//删除提问
	public function deleteQuestion()
	{
		//验证提问是否属于该学生
		if(!questionverify(I('qno'))){
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}

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

	//删除回答
	public function deleteReply()
	{
		//验证回答是否属于该学生
		if(!replyverify(I('rpno'))){
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}

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

	//删除评论
	public function deleteRemark()
	{
		//验证评论是否属于该学生
		if(!remarkverify(I('rmno'))){
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}

		$model = new Model();
		$result = $model->execute("delete from remark where rmno=".I('rmno'));

		if($result)
		{
			$data['status']=1;
		}else
		{
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

}
?>