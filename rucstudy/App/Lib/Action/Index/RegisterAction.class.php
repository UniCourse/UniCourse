<?php
/**
 * 注册控制器
 * created by hfr
 */
class RegisterAction extends Action {
	
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
	

}
?>