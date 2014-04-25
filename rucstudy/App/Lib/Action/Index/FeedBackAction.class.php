<?php 
/**
 * 意见反馈控制器
 * by hfr 
 */

class FeedBackAction extends Action
{
	public function index()
	{
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from feedback");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$model = new Model();
		$mid = $model->query("select * from feedback order by fbtime DESC" . " limit ". $page->firstRow.",".$page->listRows);

		foreach ($mid as $key => $value) {
			$name = $model->query("select sname from student where sno='".$value['uid']."'");
			if(!$name)
			{
				$name = $model->query("select tname from teacher where tno='".$value['uid']."'");
				$mid[$key]['name']=$name[0]['tname'];
			}else
			{
				$mid[$key]['name']=$name[0]['sname'];
			}
			
		}
		$this->feedback = $mid;

		$this->display();
	}

	public function add()
	{
		$model = new Model();
		$result = $model->execute("insert into feedback(fbcontent,uid,fbtime,anonymous) values('".I('fbcontent')."','".session('uid')."',sysdate(),".I('anonymous').")");
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