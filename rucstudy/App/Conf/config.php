<?php
return array(
	//'配置项'=>'配置值'
	
	//开启应用分组
	'APP_GROUP_LIST' => 'Index,Admin,Teacher',
	'DEFAULT_GROUP' => 'Index',
	
	//配置数据库
	'DB_TYPE'   => 'mysql',
	'DB_HOST' => '127.0.0.1',
	'DB_USER' => '####',
	'DB_PWD' => '####',
	'DB_NAME' => '####',
	'DB_PORT' => '3306',
	'DB_PREFIX' => '',
	
	/*点语法默认解析
	'TMPL_VAR_IDENTIFY' => 'array',*/
	
	'TMPL_FILE_DEPR' => '_',
	
	//调试工具	
	//'SHOW_PAGE_TRACE' => true,
	
	//url模式：0：普通模式（默认） 1：pathinfo模式 2：rewrite模式 3：兼容模式
	'URL_MODEL' => 2,
		
	//变量过滤
	'VAR_FILTERS'=>'htmlspecialchars',

	//URL伪静态
	'URL_HTML_SUFFIX'=>'html',

	//URL不区分大小写
	'URL_CASE_INSENSITIVE' =>true,

	//邮件配置
 	'THINK_EMAIL' => array(
		'SMTP_HOST'   => '####', //SMTP服务器
		'SMTP_PORT'   => '####', //SMTP服务器端口
		'SMTP_USER'   => '####', //SMTP服务器用户名
		'SMTP_PASS'   => '####', //SMTP服务器密码
		'FROM_EMAIL'  => '####', //发件人EMAIL
		'FROM_NAME'   => 'UniCourse 管理员', //发件人名称
		'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
		'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
 	),
	
);
?>
