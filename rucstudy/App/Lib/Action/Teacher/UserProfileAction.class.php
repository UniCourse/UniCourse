<?php

class UserProfileAction extends CommonTeacherAction {
	
	//显示模板
	public function index() {
		
		//查询当前用户资料
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$this->teacher = $model->query("select * from teacher where tno=" . session('uid'));

		$this->display();
	}
	
	/**
	 * 处理表单函数
	 */
	public function handleDept() {
		//修改学院
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$flag = $model->execute("update teacher set tschool='" . I('dept') . "' where tno=" . session('uid'));
	
		if($flag)
		{
			$data['status']=0;
			$data['info']="学院修改成功";
			$this->ajaxReturn($data);
			//$this->success('修改“学院”成功');
		}
		else
		{
			$data['status']=1;
			$data['info']="学院修改失败";
			$this->ajaxReturn($data);
			//$this->error('修改“学院”失败');
		}
	
	}
	
	public function handlePhone() {
		//修改电话
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$flag = $model->execute("update teacher set phone='" . I('phone') . "' where tno=" . session('uid'));
	
		if($flag)
		{
			$data['status']=0;
			$data['info']="电话修改成功";
			$this->ajaxReturn($data);
			//$this->success('修改“电话”成功');
		}
		else
		{
			$data['status']=1;
			$data['info']="学院修改失败";
			$this->ajaxReturn($data);
			//$this->error('修改“电话”失败');
		}
	
	}
	
	public function handleEmail() {
		//修改email
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$flag = $model->execute("update teacher set email='" . I('email') . "' where tno=" . session('uid'));
	
		if($flag)
		{
			$data['status']=0;
			$data['info']="Email修改成功";
			$this->ajaxReturn($data);
			//$this->success('修改“Email”成功');
		}
		else
		{
			$data['status']=1;
			$data['info']="Email修改失败";
			$this->ajaxReturn($data);
			//$this->error('修改“Email”失败');
		}
	
	}
	
	public function handleOffice() {
		//修改email
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$flag = $model->execute("update teacher set office='" . I('office') . "' where tno=" . session('uid'));
	
		if($flag)
		{
			$data['status']=0;
			$data['info']='办公室修改成功';
			$this->ajaxReturn($data);
			//$this->success('修改“办公室”成功');
		}
		else
		{
			$data['status']=1;
			$data['info']='办公室修改失败';
			$this->ajaxReturn($data);
			//$this->error('修改“办公室”失败');
		}
	
	}
	
	public function handleNotes() {
		//修改备注
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$flag = $model->execute("update teacher set tnotes='" . I('tnotes') . "' where tno=" . session('uid'));
	
		if($flag)
		{
			$data['status']=0;
			$data['info']='备注修改成功';
			$this->ajaxReturn($data);
			//$this->success('修改“备注”成功');
		}
		else
		{
			$data['status']=1;
			$data['info']='备注修改成功';
			$this->ajaxReturn($data);
			
			//$this->error('修改“备注”失败');
		}
	
	}
	
	public function handlePassword() {
		//修改密码
		if($_POST["password"] != $_POST["passwordConfirm"])
		{
			$this->error('两次密码输入不一致！请重新输入');
		}
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$flag = $model->execute("update teacher set tpsw=md5('" . I('password') . "') where tno='" . session('uid')."'");
		
	if($flag)
		{
			$data['status']=0;
			$data['info']='修改密码成功';
			$this->ajaxReturn($data);
			//$this->success('修改密码成功');
		}
		else
		{
			$data['status']=1;
			$data['info']='修改密码失败';
			$this->ajaxReturn($data);
		}
	}

}

?>