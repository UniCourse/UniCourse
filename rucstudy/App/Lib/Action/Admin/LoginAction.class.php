<?php 
class LoginAction extends Action
{
	public function login()
	{
		 //判断验证码是否正确
		if(I('code','','md5') != session('verify'))
		{//如果验证码输入错误
			$this->error('验证码错误');
		}
		
		$usernumber = I('usernumber');
		$password = I('password');

		$usermodel = new Model();
		$user = $usermodel->query('select * from admin where ano='. $usernumber . ' and apsw=md5("' . $password . '")');
		 
		if(!$user)
		{
			$this->error('账号或密码错误');
			return;
		}
		 
		session('uid', $usernumber);
		session('role', 'admin');
		$this->success('登录成功',U('Admin/Home/index'));
	}

	public function logout()
	{
		session_unset();
		session_destroy();
		$this->redirect('Admin/Index/index');
	}
}
?>