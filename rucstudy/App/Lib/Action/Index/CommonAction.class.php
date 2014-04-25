<?php
/**
 * 登录验证及基础控制器
 * by hfr
 */
class CommonAction extends Action
{
	public function _initialize()
	{
		if(!isset($_SESSION['uid']))
		{
			$this->redirect('Index/Index/index');
		}
		else 
		{
			if(session('type')!='student')
			{
				$this->redirect('Index/Index/index');
			}
		}
	}

	/**
	 * 下载模块
	 * modified by hfr at 201402081923
	 */
	public function documentDownload()
	{
		$temp = M()->query("select * from coursefile where fno=".I('fno'));
		$furl = $temp[0]['furl'];

		if(fileDownload($furl)){

		}else{
			$this->error("文件不存在");
		}

	}

	/**
	 * 下载模块
	 * modified by hfr at 201402081923
	 */
	public function homeworkDownload()
	{
		$temp = M()->query("select * from homeworkfile where fno=".I('fno'));
		$furl = $temp[0]['furl'];

		if(fileDownload($furl)){

		}else{
			$this->error("文件不存在");
		}

	}

	/**
	 * 下载模块
	 * modified by hfr at 201402081923
	 */
	public function studentHomeworkDownload()
	{
		$temp = M()->query("select * from stu_homework where fno=".I('fno'));
		$furl = $temp[0]['furl'];

		if(fileDownload($furl)){

		}else{
			$this->error("文件不存在");
		}

	}

	/**
	 * 下载模块
	 * modified by hfr at 201402081923
	 */
	public function groupDownload()
	{
		$temp = M()->query("select * from grpfile where fno=".I('fno'));
		$furl = $temp[0]['gurl'];

		if(fileDownload($furl)){

		}else{
			$this->error("文件不存在");
		}

	}
	
	
}
?>