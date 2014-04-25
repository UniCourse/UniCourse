<?php 
/**
 * 全局函数
 * created by hfr
 */

//打印结果
function p($array)
{
    dump($array, 1, '<pre>', 0);
}

//由课程号获得课程名
function getCourseName($cno)
{
    $courseNamemodel = new Model();
    $courseName = $courseNamemodel->query("select * from course where cno='".$cno."'");
    
    return $courseName[0]['cname'];
}

//由小组编号获得小组名称
function getGroupName($gno)
{
    $groupNamemodel = new Model();
    $groupName = $groupNamemodel->query("select * from groups where gno=".$gno);

    return $groupName[0]['gname'];
}

//文件下载
function fileDownload($furl)
{
    //中文转码
    $furl=iconv('UTF-8','GBK',$furl);

    $fname=I('fname');
    $fname=iconv('UTF-8','GBK',$fname);
        
    if (!file_exists($furl)){
        header("Content-type: text/html; charset=UTF-8");
    } else {
        $file = fopen($furl,"r");
        
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ".filesize($furl));
        Header("Content-Disposition: attachment; filename=\"".$fname."\";");
        echo fread($file, filesize($furl));
        fclose($file);
        return true;
    }
    return false;
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 */
 function send_mail($to, $name, $subject = '', $body = '', $attachment = null){
    $config = C('THINK_EMAIL');
    //vendor('PHPMailer.class#smtp');
    vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail             = new PHPMailer(); //PHPMailer对象
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
                                               // 1 = errors and messages
                                               // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    //$mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
 }

//注册用，产生随机数
function rand_number ($min, $max) {
    return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
}

/**
* 产生新鲜事的函数
* Create by hfr at 201402082358
* type: 1 question 2 notice 3 homework 4 document
*/
function createNews($sno, $cno, $n_content, $n_contenturl,$type,$n_contentid=''){
    $sname = getUserName($sno);
    if(!$sname[0]['sname']){
        $name = $sname[0]['tname'];
    }else{
        $name = $sname[0]['sname'];
    }
    
    $result = M()->execute("insert into news(n_sno,n_sname,n_surl,n_cno,n_cname,n_curl,n_content,n_contenturl,n_time,n_type,n_contentid) values('".$sno."','".$name."','','".$cno."','".getCourseName($cno)."','".U('Index/Notice/index',array('cno'=>$cno))."','".$n_content."','".$n_contenturl."',sysdate(),".$type.",'".$n_contentid."')");
    if($result){
        return 1;
    }else{
        return 0;
    }
}

/**
* 产生最初消息的函数
* Create by hfr at 201402092153
*
* $content 消息内容
*/
function createRawMessage(){
    $temp = M()->query("select sname from student where sno='".session('uid')."'");
    if(!$temp){
        $temp = M()->query("select tname from teacher where tno='".session('uid')."'");
        $result = M()->execute("insert into message(actor_no, actor_name, actor_url, m_time) values('".session('uid')."', '".$temp[0]['tname']."', '',sysdate())");
    }else{
        $result = M()->execute("insert into message(actor_no, actor_name, actor_url, m_time) values('".session('uid')."', '".$temp[0]['sname']."', '',sysdate())");
    }


    if($result){
        return mysql_insert_id();
    }else{
        return 0;
    }
}

/**
* 产生最终消息的函数
* Create by hfr at 201402091446
*
* $content 消息内容
*/
function createFormalMessage($qno,$mid,$type){
    $temp = M()->query("select qtitle,cno from question where qno=".$qno);

    $result1 = M()->execute("update message set position_no = '".$qno."' where m_id = ".$mid);
    $result2 = M()->execute("update message set position_name = '".$temp[0]['qtitle']."' where m_id = ".$mid);
    $result3 = M()->execute("update message set position_url = '".U('Index/Question/questionDetail', array('qno'=>$qno, 'cno'=>$temp[0]['cno']))."' where m_id = ".$mid);
    $result4 = M()->execute("update message set m_type = ".$type." where m_id = ".$mid);
   

    if($result1&&$result2&&$result3&&$result4){
        return 1;
    }else{
        return 0;
    }
}

/**
* 回答提问产生消息的函数
* Create by hfr at 201402091455
* Modified by hfr at 201403051021
* 
* $content 消息内容
* $qno 产生消息的提问
*/
function createReplyMessage($qno){
    $mid = createRawMessage();

    if(!createFormalMessage($qno,$mid,1)){
        return 0;
    }

    //给问题提出者发送消息
    $raiseuser = M()->query("select raise_sno from question where qno=".$qno);
    M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$raiseuser[0]['raise_sno']."',0)");

    //*给问题所有回答者发送消息
    $replyuser = M()->query("select distinct rp_sno from reply where qno=".$qno);
    foreach ($replyuser as $key => $value) {
        # code...
        M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$value['rp_sno']."',0)");
    }

    //给问题关注者发送消息
    $focususer = M()->query("select distinct sno from q_attention where qno=".$qno);
    foreach ($focususer as $key => $value) {
        # code...
         M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$value['sno']."',0)");
    }

    //把提示自己的消息删除
    $isexsit = M()->query("select * from message_user where m_uid='".session('uid')."' and m_id=".$mid." and is_read=0");
    if($isexsit){
        M()->execute("delete from message_user where m_uid='".session('uid')."' and m_id=".$mid." and is_read=0");
    }

    return 1;
}

/**
* 评论回答产生消息的函数
* Created by hfr at 201403051021
* 
* $content 消息内容
* $qno 产生消息的提问
*/
function createCommentMessage($qno,$rpno){
    $mid = createRawMessage();

    if(!createFormalMessage($qno,$mid,2)){
        return 0;
    }

    //给问题提出者发送消息
    $raiseuser = M()->query("select raise_sno from question where qno=".$qno);
    M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$raiseuser[0]['raise_sno']."',0)");

    //**给问题此条回答者发送消息
    $replyuser = M()->query("select distinct rp_sno from reply where rpno=".$rpno);
    foreach ($replyuser as $key => $value) {
        # code...
        M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$value['rp_sno']."',0)");
    }

    //给该提问的其他评论者发送消息
    $remarkuser = M()->query("select distinct rm_sno from remark where rpno=".$rpno);
    foreach ($remarkuser as $key => $value) {
        # code...
        M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$value['rm_sno']."',0)");
    }

    //给问题关注者发送消息
    $focususer = M()->query("select distinct sno from q_attention where qno=".$qno);
    foreach ($focususer as $key => $value) {
        # code...
         M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$value['sno']."',0)");
    }

    //把提示自己的消息删除
    $isexsit = M()->query("select * from message_user where m_uid='".session('uid')."' and m_id=".$mid." and is_read=0");
    if($isexsit){
        M()->execute("delete from message_user where m_uid='".session('uid')."' and m_id=".$mid." and is_read=0");
    }

    return 1;
}

/**
 * 将消息设置为已读
 */
function readMessage($qno)
{
    $temp = M()->query("select m_id from message where position_no=".$qno);
    foreach ($temp as $key => $value) {
        $result = M()->execute("update message_user set is_read=1 where m_id=".$value['m_id']);
    }
    if($result){
        return 1;
    }else{
        return 0;
    }
}

/**
 * 输入@后显示课程中学生名单
 * created by hfr at 201402192323
 * @input 待搜索的字符串
 * @cno 某门课程
 */
function atCourseNameList($input,$cno)
{
    return M()->query("select * from student,stu_course where stu_course.sno=student.sno and stu_course.cno='".$cno."' and sname like '%".$input."%'");
}

/**
 * 输入@后显示小组中学生名单
 * created by hfr at 201402192323
 * @input 待搜索的字符串
 * @cno 某门课程
 */
function atGroupNameList($input,$gno)
{
    return M()->query("select * from student,stu_grp where stu_grp.sno=student.sno and stu_grp.gno='".$gno."' and sname like '%".$input."%'");
}

/**
 * 获取姓名
 * created by hfr at 201402161304
 * modified by hfr at 201402231645
 * @sno 被@的用户的学号
 */
function getUserName($uno)
{
    $result = M()->query("select * from student where sno='".$uno."'");
    if(!$result){
        $result = M()->query("select * from teacher where tno='".$uno."'");
    }
    return $result;
}

/**
 * 发送@消息
 * created by hfr at 201402161307
 * @sno 被@者的学号
 * @qno 被@的问题
 */
function createAtMessage($sno,$qno)
{
    //产生原始消息
    $mid = createRawMessage();

    //产生最终消息
    if(!createFormalMessage($qno,$mid,3)){
        //失败
        return 0;
    }

    //发送消息
    $result = M()->execute("insert into message_user(m_id, m_uid, is_read) values(".$mid.", '".$sno."',0)");

    if($result)
    {
        //成功
        return 1;
    }else{
        //失败
        return 0;
    }
}

/**
 * 上传文件
 * created by hfr at 201302192328
 * @url 文件路径
 */
function uploadfiles($url)
{
    import('ORG.Net.UploadFile');
    $upload = new UploadFile();// 实例化上传类
    $upload->maxSize  = 31457280 ;// 设置附件上传大小
    //$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    //$upload->autoSub = true;
    //$upload->subType = 'custom';
    //$upload->subDir = 'Group';
    // $upload->saveRule = '';
    $upload->savePath =  $url;// 设置附件上传目录
    if(!$upload->upload()){
        return $upload->getErrorMsg();
    }else{
        return $upload->getUploadFileInfo();
    }
}

/**
 * 提取@人id，并生成超链接和消息
 * created by hfr at 201402212007
 * @content 消息内容 
 */
function handleAt($content, $qno)
{
    //匹配@人字符串
    $pattern = "|@(\S+)\(([0-9]+)\)\s|";
    preg_match_all($pattern, $content, $result, PREG_PATTERN_ORDER);

    //发送@消息
    foreach ($result[2] as $key => $value)
    {
        createAtMessage($value, $qno);
    }

    //$1为姓名，$2为学号
    $replacement = "<a href='#'>@$1</a>";
    return preg_replace($pattern, $replacement, $content);
}
function parseAt($content){
    //匹配@人字符串
    $pattern = "|@(\S+)\(([0-9]+)\)\s|";
    preg_match_all($pattern, $content, $result, PREG_PATTERN_ORDER);
    //$1为姓名，$2为学号
    $replacement = "<a href='#'>@$1</a>";
    return preg_replace($pattern, $replacement, $content);

}

function setSession($name,$id,$course,$type){
    if($type=='student'){
        session('selectedCourses',$course);
        session('uid', $id);
        session('uname',$name);
        session('type', 'student');

        $isAssistant = M()->query("select * from assi_course where ano = '".session('uid')."'");
        if($isAssistant[0]){
            session('isAssistant',1);
        }else{
            session('isAssistant',0);
        }
    }
    if($type=='teacher'){
        session('selectedCourses',$course);
        session('uid', $id);
        session('tname',$name);
        session('type', 'teacher');
    }
}

?>