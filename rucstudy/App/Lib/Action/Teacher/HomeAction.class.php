<?php 

class HomeAction extends CommonTeacherAction
{
	public function index()
	{
		$coursemodel = new Model();
		$this->course = $coursemodel->query("select * from course where course.tno=".session('uid'));
		
		$this->display();
	}

	public function createcourse()
	{
		$model = new Model();
		$result = $model->execute("insert into course(cname,cschool,tno,intro,cnotes) values('".I('cname')."','".I('cschool')."','".session('uid')."','".I('intro')."','".I('cnotes')."')");

		if($result)
		{
			$data['status']=1;
		}else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
}


?>