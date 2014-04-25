<?php 
/**
 * index模块函数
 * created by hfr
 */

//小组认证，判断该学生是否属于该小组
function groupverify($gno)
{
	//判断该学生是否在这个小组内
	$partnermodel = new Model();
	$partner = $partnermodel->query("select * from stu_grp,student where stu_grp.sno=student.sno and stu_grp.gno=".$gno);
	$flag=0;
	foreach($partner as $key=>$value)
	{
		if($value['sno']==$_SESSION['uid'])
		{
			$flag=1;
			break;
		}
	}
	
	if($flag==1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//课程认证，判断该学生是否属于该课程
function courseverify($cno)
{
	//判断该学生是否在这个小组内
	$partnermodel = new Model();
	$partner = $partnermodel->query("select * from stu_course,student where stu_course.sno=student.sno and stu_course.cno=".$cno);
	$flag=0;
	foreach($partner as $key=>$value)
	{
		if($value['sno']==$_SESSION['uid'])
		{
			$flag=1;
			break;
		}
	}

	if($flag==1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//提问认证，判断该提问是否为该学生提出
function questionverify($qno)
{
	$result = M()->query("select * from question where qno=".$qno." and raise_sno='".session('uid')."'");

	if($result){
		return 1;
	}else{
		return 0;
	}
}

//回答认证，判断该回答是否为该学生提出
function replyverify($rpno)
{
	$result = M()->query("select * from reply where rpno=".$rpno." and rp_sno='".session('uid')."'");

	if($result){
		return 1;
	}else{
		return 0;
	}
}

//评论认证，判断该评论是否为该学生提出
function remarkverify($rmno)
{
	$result = M()->query("select * from remark where rmno=".$rmno." and rm_sno='".session('uid')."'");

	if($result){
		return 1;
	}else{
		return 0;
	}
}

?>