<?php 


Class RbacAction extends Action
{
	public function index()
	{

	}

	public function role()
	{
		$model = new Model();
		$this->role = $model->query("select * from admin_role");

		$this->display();
	}

	public function node()
	{

	}

	public function addUser()
	{

	}

	public function addRole()
	{
		$this->display();
	}

	public function addRoleHandle()
	{
		$model = new Model();
		$handle = $model->execute("insert into admin_role(name,remark,status) values('".I('name')."','".I('remark')."',".I('status').")");

		if($handle)
		{
			$data['status']=1;
		}
		else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}

	public function addNode()
	{

	}

}
 ?>