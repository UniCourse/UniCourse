<?php 
/**
 * 日程控制器
 * created by hfr
 */

class ScheduleAction extends CommonAction
{
	public function index()
	{
		//新鲜事分页显示
		import('ORG.Util.Page');
		$countmodel = new Model();
		$count=$countmodel->query("select count(*) from sch,stu_sch where sch.sdno=stu_sch.sdno and sno = " . session('uid'));
		$page=new Page($count[0]['count(*)'],10);
		$this->page = $page->show();

		$schedulemodel = new Model();
		$this->schedule = $schedulemodel->query("select * from sch,stu_sch where sch.sdno=stu_sch.sdno and sno = " . session('uid')." order by rdeadline DESC limit ". $page->firstRow.",".$page->listRows);
		
		$this->display();
	}
	
	public function addScheduleHandle()
	{
		$addschedulemodel = new Model();
		$addschedule = $addschedulemodel->execute("insert into sch(rname,rnotes,rdeadline) values('".I('rname')."','".I('rnotes')."','".I('rdeadline')."')");
		
		$sdno=mysql_insert_id();
		
		$getsdnomodel = new Model();
		$getsdno = $getsdnomodel->query("select max(sdno) from sch");
		
		$addstuschmodel = new Model();
		$addstusch = $addstuschmodel->execute("insert into stu_sch(sno,sdno) values('".session('uid')."',".$getsdno[0]['max(sdno)'].")");
		
		if($addschedule&&$addstusch)
		{
			
			/*将日程提示形成消息，并添加到日程消息-用户表中*/
			$msmodel=new Model();
			
			$msmodel->execute("insert into ms(msdate,msd_url,sdno,msname,sch_url,mstime)values('".I('rdeadline')."','".U('Index/Schedule/index')."','".$sdno."','".I('rname')."','".U('Index/Schedule/index')."',sysdate())" );
			
			//echo $msmodel->getLastSql();
			
			$mid=mysql_insert_id();

	
			$sno= $msmodel->query("select sno from stu_sch where sdno='" . $sdno."'");
	
			if($sno ){
				foreach($sno as $stu){
					$msmodel->execute("insert into ms_user(mid,userid) values('".$mid."','" . $stu['sno']."')");					
				}
			}

			$data['status']=1;
		}
		else
		{
			$data['status']=0;
		}
		$this->ajaxReturn($data,'json');
	}
	
	public function deleteSchedule()
	{
		$deleteschedulemodel = new Model();
		$deleteschedule = $deleteschedulemodel->execute("delete from stu_sch where sdno=".I('sdno'));
		
		if($deleteschedule)
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