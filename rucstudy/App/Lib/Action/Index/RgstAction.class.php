<?php
/**
 * 注册控制器
 * created by hfr
 */
class RgstAction extends Action {
	
	public function index() {

		$this->display(); //显示模板
	}
	
	
	public function regverify()
	{
		$verifycode = md5(rand_number(1,10000000));

		$model = new Model();
		$result = $model->execute("insert into regverify(sno,sname,sex,smajor,sgrade,spsw,snotes,email,verifycode,isverified) values('".I('sno')."','".I('sname')."','".I('sex')."','".I('major')."','".I('grade')."',md5('".I('passwordFirst')."'),'".I('notes')."','".I('sno')."@ruc.edu.cn','".$verifycode."','0')");

		if(!$result)
		{
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}

		$url = U('Index/Register/handleverify',array('sno'=>I('sno'), 'verifycode'=>$verifycode));
		
		$to = I('sno').'@ruc.edu.cn';
		$name = I('sno');
		$subject = 'UniCourse注册验证（系统自动发送，请勿回复）';
		$body = "<div>亲爱的 ".I('sname')." 同学：<br>您好！<br><br>感谢您选择UniCourse课程平台！请点击以下链接以完成注册：<br><a href='http://unicourse.ruc.edu.cn".$url."'>http://unicourse.ruc.edu.cn".$url."</a><br><br><span style='font-weight:bold;'>这是系统自动发送的邮件，请勿回复！</span><br>如需帮助，请电邮至：unicourse@163.com<br><br><br><br>UniCourse开发小组 敬上</div>";

		if(send_mail($to,$name,$subject,$body))
		{
			$data['status']=1;
		}else
		{
			$data['status']=2;
		}
		$this->ajaxReturn($data,'json');

	}

	/**
	 * 忘记密码页面
	 * created by hfr at 201404212231
	 */
	public function handleverify()
	{
		$model = new Model();
		$result = $model->query("select * from regverify where verifycode = '".I('verifycode')."' and sno = '".I('sno')."'");

		if($result)
		{
			$handle = $model->execute("insert into student(sno,sname,sex,smajor,sgrade,spsw,snotes) values('".$result[0]['sno']."','".$result[0]['sname']."','".$result[0]['sex']."','".$result[0]['smajor']."','".$result[0]['sgrade']."','".$result[0]['spsw']."','".$result[0]['snotes']."')");
			if($handle)
			{
				$this->success('验证成功！正在返回登录页，请稍等……',U('Index/Index/Index'));
			}
			else
			{
				$this->error('您已经验证过了，可以直接登陆！',U('Index/Index/Index'));
			}
		}
		else
		{
			$this->error('非法操作。',U('Index/Index/Index'));
		}
	}

	/**
	 * 忘记密码申请页面
	 * created by hfr at 201404212231
	 */
	public function forgetpwpage(){
		$this->display();
	}
	
	/**
	 * 忘记密码处理方法
	 * created by hfr at 201404212231
	 */
	public function forgetpasswd(){
		$verifycode = md5(rand_number(1,10000000));

		$model = new Model();
		$result = $model->execute("insert into forgetpasswd(uno,verifycode,isverified,att_time) values('".I('uno')."','".$verifycode."','0',sysdate())");

		$result = $model->query("select * from student where sno='".I('uno')."'");
		if(!$result){
			$result = $model->query("select * from teacher where tno='".I('uno')."'");
			if(!$result)
			{
				$data['status']=1;
				$data['info']='用户不存在';
				$this->ajaxReturn($data,'json');
			}else{
				$uname = $result[0]['tno'];
			}
		}else{
			$uname = $result[0]['sno'];
		}

		$url = U('Index/Rgst/resetpw',array('uno'=>I('uno'), 'verifycode'=>$verifycode));
		
		$to = I('uno').'@ruc.edu.cn';
		$name = I('uno');
		$subject = 'UniCourse忘记密码验证（系统自动发送，请勿回复）';
		$body = "<div>亲爱的 ".$uname." ：<br>您好！<br><br>请点击以下链接以修改密码：<br><a href='http://unicourse.ruc.edu.cn".$url."'>http://unicourse.ruc.edu.cn".$url."</a><br>如非本人操作，请勿点击。<br><span style='font-weight:bold;'>这是系统自动发送的邮件，请勿回复。</span><br>如需帮助，请电邮至：unicourse@163.com<br><br><br><br>UniCourse开发小组 敬上</div>";

		if(send_mail($to,$name,$subject,$body))
		{
			$data['status']=0;
			$data['info']="发送邮件成功";
		}else
		{
			$data['status']=2;
			$data['info']="发送邮件失败";
		}
		$this->ajaxReturn($data,'json');
	}

	/**
	 * 忘记密码修改页面
	 * created by hfr at 201404212231
	 */
	public function resetpw(){
		$this->uno = I('uno');
		$isvalid = M()->query("select * from forgetpasswd where verifycode='".I('verifycode')."' and uno='".I('uno')."' and isverified='0'");
		if($isvalid){
			$setverified = M()->execute("update forgetpasswd set isverified='1' where uno='".I('uno')."'");
			$_SESSION['_u']=$this->uno;
			$this->display();
		}else{
			$this->display("errorcode");
		}
	}

	/**
	 * 忘记密码修改方法
	 * created by hfr at 201404212231
	 */
	public function handleresetpw(){
		$uno = $_SESSION['_u'];
		$upasswd = I('upasswd');
		$result = M()->query("select * from student where sno='".$uno."'");
		if(!$result){
			$result = M()->query("select * from teacher where tno='".$uno."'");
			if($result){
				$issuccess = M()->execute("update teacher set tpsw=md5('".$upasswd."') where tno='".$uno."'");
			}else{
				$data['status']=0;
				$this->ajaxReturn($data,'json');
			}
		}else{
			$issuccess = M()->execute("update student set spsw=md5('".$upasswd."') where sno='".$uno."'");
		}
		if($issuccess){
			$data['status']=0;
			$data['info']='修改成功';
			$this->ajaxReturn($data,'json');
		}else{
			$data['status']=1;
			$data['info']="修改失败，您可以尝试换一个密码或者重新发送邮件";
			$this->ajaxReturn($data,'json');
		}
	}

}
?>