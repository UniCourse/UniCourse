<?php
/**
 * 用户资料控制器
 * created by hfr
 */

class UserProfileAction extends CommonAction {
	
	//显示模板
	public function index() {
		
		//查询当前用户资料
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$this->student = $model->query("select * from student where sno=" . session('uid'));
		
		$this->display();
	}
	
	//修改个性签名
	public function handleNotes() {
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("update student set snotes='" . I('notes') . "' where sno=" . session('uid'));
		
		if($result)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
		
	}
	
	//修改专业
	public function handleMajor() {
		if(I('major')==null)
		{
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}
		
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("update student set smajor='" . I('major') . "' where sno=" . session('uid'));
		
		if($result)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	//修改学院
	public function handleDept() {
		if(I('dept')==null)
		{
			$data['status']=0;
			$this->ajaxReturn($data,'json');
		}

		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $model->execute("update student set school='" . I('dept') . "' where sno=" . session('uid'));
		
		if($result)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	//修改密码
	public function handlePassword() {
		$model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$confirm = $model->query("select * from student where sno='".session('uid')."' and spsw=md5('".I('ipassword')."')");
		if(!$confirm)
		{
			$data['status']=2;
			$this->ajaxReturn($data,'json');
		}

		$result = $model->execute("update student set spsw=md5('" . I('password') . "') where sno=" . session('uid'));
		
		if($result)
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