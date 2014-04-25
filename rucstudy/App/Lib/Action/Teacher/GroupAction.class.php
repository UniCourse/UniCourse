<?php 
/**
 * 
 * 老师课程小组控制器
 * created by hfr at 201402212227
 *
 */

class GroupAction extends CommonCourseAction
{
	//该课程所有小组
	public function index()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);

 		//分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from groups where cno='" . $this->cno."'");
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		$groupmodel = new Model();
		$this->group = $groupmodel->query("select * from groups where cno='" . $this->cno . "' limit ". $page->firstRow.",".$page->listRows);
				
		$this->display();
	}

	//小组细节
	public function groupDetail()
	{
		$this->cno = I('cno');
		$this->cname = getCourseName($this->cno);
		$this->gno = I('gno');
		$this->gname = getGroupName($this->gno);

 		//小组新鲜事分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query('select count(*) from grpnews where grpnews.gno='.I('gno'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();
		
		//小组新鲜事
		$groupnewsmodel = new Model();
		$this->groupnews = $groupnewsmodel->query("select * from student,grpnews where student.sno=grpnews.n_sno and grpnews.gno=".I('gno').' order by n_time DESC' . ' limit '. $page->firstRow.','.$page->listRows);
		
		$groupdetailmodel = new Model();
		$this->groupdetail = $groupdetailmodel->query('select * from groups where gno=' . $this->gno);
	
		$groupmembermodel = new Model();
		$this->groupmember = $groupmembermodel->query('select * from stu_grp,student where student.sno=stu_grp.sno and stu_grp.gno=' . $this->gno);
	
		$groupfilemodel = new Model();
		$this->groupfile = $groupfilemodel->query("select * from grpfile where gno=" . $this->gno." order by ftime DESC");
		
		$this->display();
	}
}

?>