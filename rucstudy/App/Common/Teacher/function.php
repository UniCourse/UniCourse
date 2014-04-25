<?php 
/**
 * teacher模块函数
 * created by hfr
 */

//课程认证，判断该老师是否教授该门课程
function courseverify($cno)
{
	//判断该老师是否教授这门课程
	$partnermodel = new Model();
	$partner = $partnermodel->query("select * from course where course.cno='".$cno."'");
	
	if($partner[0]['tno']==session('uid'))
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
?>