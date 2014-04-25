<?php
/**
 * 助教控制器
 * by hfr at 201403232320
 */

class AssistantAction extends CommonAction
{
	//助教主页
	public function index()
	{
		$this->course = M()->query("select * from course,assi_course where assi_course.ano = '".session('uid')."' and assi_course.cno = course.cno");
		$this->display();
	}

	public function enter($cno)
	{
		$teacher = M()->query("select * from course where cno=".$cno);

		session_unset();

		session('tname',$teacher[0]['tname']);
		session('uid', $teacher[0]['tno']);
		session('type', 'teacher');

		$this->redirect('Teacher/Course/index',array('cno'=>$cno));
	}

}
?>