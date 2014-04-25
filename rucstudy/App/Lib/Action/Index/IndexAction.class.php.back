<?php
/**
 * 首页跳转控制器
 * by hfr
 */

class IndexAction extends Action {
	public function index()
    {
    	if(isset($_SESSION['uid'])&&session('type')=='student')
		{
			$this->redirect('Index/Home/index');
		}
		if(isset($_SESSION['uid'])&&session('type')=='teacher')
		{
			$this->redirect('Teacher/Home/index');
		}

		//CAS Server的登陆URL
		$loginServer = "http://cas.ruc.edu.cn/cas/login";
		//CAS Server的验证URL
		$validateServer = "http://cas.ruc.edu.cn/cas/serviceValidate";

		//当前集成系统所在的服务器和端口号，服务器可以是机器名、域名或ip，建议使用域名。端口不指定的话默认是80
		//以及新增加的集成登录入口
		$myurl = "http://unicourse.ruc.edu.cn/index/index/cas";

		//判断是否有验证成功后需要跳转页面，如果有，增加跳转参数
		if(isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
			$myurl = $myurl."?redirectUrl=".$_REQUEST["redirectUrl"];
		}

		//判断是否已经登录
		if(isset($_REQUEST["ticket"]) && !empty($_REQUEST["ticket"])) {
			//获取登录后的返回信息
			try{
				$validateurl = $validateServer."?ticket=".$_REQUEST["ticket"]."&service=".$myurl;
				header("Content-Type:text/html;charset=utf-8");
				$validateResult = file_get_contents($validateurl);
				$validateResult = iconv("gb2312", "utf-8//IGNORE",$validateResult);
				//节点替换，去除sso:，否则解析的时候有问题
				$validateResult = preg_replace("/sso:/","",$validateResult);
				
				$validateXML = simplexml_load_string($validateResult);
				//获取验证成功节点
				$successnode = $validateXML->authenticationSuccess[0];
				if(!empty($successnode)){
					//获取用户账户
					$userid = $successnode->user;
				
					//实现集成系统的登录（需要集成系统开发人员完成）
					//............实现代码...................
					//实现登录完毕！
					
					//如果登录成功，执行下面代码，否则按集成系统业务逻辑处理
					//集成系统的首页URL
					$Rurl = "http://unicourse.ruc.edu.cn/index/index/cas";
					if(isset($_REQUEST["redirectUrl"]) && !empty($_REQUEST["redirectUrl"])) {
						$Rurl = $_REQUEST["redirectUrl"];
					}
					//重定向浏览器 
					header("Location: ".$Rurl);
					exit;
				}else{
					//重定向浏览器 
					header("Location: ".$loginServer."?service=".$myurl); 
					//确保重定向后，后续代码不会被执行 
					exit;
				}
			}catch(Exception $e){
				echo "出错了";
				echo $e-> getMessage(); 
			}
		}else{
			//重定向浏览器 
			header("Location: ".$loginServer."?service=".$myurl); 
			//确保重定向后，后续代码不会被执行 
			exit;
		}

		$this->display();
    }

    //统一认证
    public function cas()
    {
		$validateServer = "http://cas.ruc.edu.cn/cas/serviceValidate";
    	$validateurl = $validateServer."?ticket=".I("ticket")."&service="."http://unicourse.ruc.edu.cn/index/index/cas";
		header("Content-Type:text/html;charset=utf-8");
		$validateResult = file_get_contents($validateurl);
		// $validateResult = iconv("gb2312", "utf-8//IGNORE",$validateResult);
		//节点替换，去除sso:，否则解析的时候有问题
		$validateResult = preg_replace("/sso:/","",$validateResult);
		$validateXML = simplexml_load_string($validateResult);
		//获取验证成功节点
		$successnode = $validateXML->authenticationSuccess[0];
		if(!empty($successnode)){
			//获取用户账户
			//modified by hfr at 201403112216
			$usernumber = $successnode->user."";
			$username = $successnode->attributes->attribute[4]->attributes()['value']."";
			$useremail = $successnode->attributes->attribute[1]->attributes()['value']."";
		}else{
			echo "CAS链接失败";
			exit;
		}
		
		//统一认证登录
		$susermodel = new Model();
		$suser = $susermodel->query("select * from student where sno='". $usernumber."'");

		if(!$suser)
		{
			$tusermodel = new Model();
			$tuser = $tusermodel->query("select * from teacher where tno='".$usernumber."'");
			if(!$tuser)
			{
				if(strlen($usernumber)==10)
				{
					M()->execute("insert into student(sno, sname, spsw) values('".$usernumber."','".$username."',md5('".$useremail."'))");					
					/**
					*Modified by SLR
					*将学生所选课程存入SESSION，待修改
					*/
					$coursemodel = new Model();
					$this->course = $coursemodel->query("select course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno='".$usernumber."'");
					
					setSession($suser[0]['sname'],$usernumber,$this->course,'student');
				}
				if(strlen($usernumber)==8)
				{
					M()->execute("insert into teacher(tno, tname, tpsw) values('".$usernumber."','".$username."',md5('".$useremail."'))");					
					/**
					*Modified by SLR
					*将老师所选课程存入SESSION，待修改
					*/
					$coursemodel = new Model();
					$this->course = $coursemodel->query("select course.cno, course.cname from course where course.tno='".$usernumber."'");
					
					setSession($tuser[0]['tname'],$usernumber,$this->course,'teacher');
				}
			}
			else 
			{
				/**
				*Modified by SLR
				*将老师所选课程存入SESSION，待修改
				*/
				$coursemodel = new Model();
				$this->course = $coursemodel->query("select course.cno, course.cname from course where course.tno='".$usernumber."'");
				
				setSession($tuser[0]['tname'],$usernumber,$this->course,'teacher');
				
				//登入
				$this->redirect('Teacher/Home/index');
			}
		}else
		{
			/**
			*Modified by SLR
			*将学生所选课程存入SESSION，待修改
			*/
			$coursemodel = new Model();
			$this->course = $coursemodel->query("select course.cno, course.cname from stu_course, course where stu_course.cno=course.cno and stu_course.sno='".$usernumber."'");
			
			setSession($suser[0]['sname'],$usernumber,$this->course,'student');
			
			//登入
			$this->redirect('Index/Home/index');
		}
    }
}