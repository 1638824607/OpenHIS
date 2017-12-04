<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

#ini_set('display_errors', 1);            //错误信息
#ini_set('display_startup_errors', 1);    //php启动错误信息
#error_reporting(-1);                    //打印出所有的 错误信息


/**
 * 安装向导
 */
header('Content-type:text/html;charset=utf-8');
// 检测是否安装过
if (file_exists('./install.lock')) {
    echo '你已经安装过该系统，重新安装需要先删除./Public/install/install.lock 文件';
    die;
}
// 同意协议页面
if (@!isset($_GET['c']) || @$_GET['c'] == 'agreement') {
    require './agreement.html';
}
// 检测环境页面
if (@$_GET['c'] == 'test') {

    require './test.html';
}
// 创建数据库页面
if (@$_GET['c'] == 'create') {
    $DB_TYPE =  extension_loaded('mysql')?'mysql':(extension_loaded('mysqli')?'mysqli':'none');

    require './create.html';
}
// 安装成功页面
if (@$_GET['c'] == 'success') {
    // 判断是否为post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = $_POST;

        // 导入sql数据并创建表
        $db_file = (isset($_POST['demo']) && $_POST['demo']=='demo')?'./db_demo.sql':'./db.sql';

        if(!file_exists($db_file))exit('错误：安装所需的数据库文件不存在，'.$db_file);

        $db_str = file_get_contents($db_file);
        $sql_array = preg_split("/;[\r\n]+/", str_replace('dzm_', $data['DB_PREFIX'], $db_str));

        #管理员
        $sql_user = "insert into %shis_member (user_name, password, create_time) VALUES ('".$_POST['adm_u']."','".encrypt_password2($_POST['adm_p'])."',".time().")";

        #医院信息
        $sql_hospital = "insert into %shis_hospital (hospital_name,create_time,hid,owner_name) VALUES ('".$_POST['hospital_name']."',".time().",'%s','".$_POST['owner_name']."')";

        #权限信息 dzm_his_auth_group_access
        $sql_access = "insert into %shis_auth_group_access (uid, group_id) VALUES (%s,%s)";

        $sql_wx = "insert into %shis_wxmp (userid) VALUES (%s)";


        if(extension_loaded('mysqli'))goto mysqli;
        if(extension_loaded('mysql'))goto mysql;
        goto err;
        mysql:
        $DB_TYPE = 'mysql';
        $link = mysql_connect("{$data['DB_HOST']}:{$data['DB_PORT']}", $data['DB_USER'], $data['DB_PWD']);
        if(!$link) exit('无法连接到数据库服务器，请检查配置:'.mysql_error());
        if(!mysql_select_db($data['DB_NAME'])) {
            if(!mysql_query("CREATE DATABASE ".$data['DB_NAME'])) exit('指定的数据库不存在\n\n系统尝试创建失败，请通过其他方式建立数据库,'.mysql_error());
            #选择刚刚创建的库
            mysql_select_db($data['DB_NAME']);
        }

        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {
                $rs = mysql_query($v,$link);
                mysql_free_result($rs);
            }
        }

        if($db_file == './db_demo.sql'){
            #更新管理员密码

            mysql_query("update ".$data['DB_PREFIX']."his_member set user_name='".$_POST['adm_p']."',password='".encrypt_password2($_POST['adm_p'])."' where uid=1",$link);

            #更新医院名称
            mysql_query("update ".$data['DB_PREFIX']."his_hospital set hospital_name='".$_POST['hospital_name']."',owner_name='".$_POST['owner_name']."' where id=1",$link);


        }else{
            #初始化
            mysql_query(sprintf($sql_user,$data['DB_PREFIX']),$link);

            $uid = mysql_insert_id($link);

            mysql_query(sprintf($sql_hospital,$data['DB_PREFIX'],$uid),$link);

            mysql_query(sprintf($sql_access,$data['DB_PREFIX'],$uid,1),$link);
            #mysql_query(sprintf($sql_access,$data['DB_PREFIX'],$uid,2),$link);
            #mysql_query(sprintf($sql_access,$data['DB_PREFIX'],$uid,3),$link);

            mysql_query(sprintf($sql_wx,$data['DB_PREFIX'],$uid),$link);
        }


        mysql_close($link);

        goto end;

        mysqli:
        $DB_TYPE = 'mysqli';


        // 连接数据库
        $link = @new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}", $data['DB_USER'], $data['DB_PWD']);

        if($link->connect_error){
            die("<script>alert('数据库链接失败:".addslashes($link->connect_error)."');history.go(-1)</script>");
        }


        // 设置字符集
        $link->query("SET NAMES 'utf8'");
        $link->server_info > 5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
        // 创建数据库并选中
        if (!$link->select_db($data['DB_NAME'])) {
            $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $data['DB_NAME'] . ' DEFAULT CHARACTER SET utf8;';
            $link->query($create_sql) or die('创建数据库失败');
            $link->select_db($data['DB_NAME']);
        }

        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {
                $link->query($v);
            }
        }

        if($db_file == './db_demo.sql'){
            #更新管理员密码

            $link->query("update ".$data['DB_PREFIX']."his_member set user_name='".$_POST['adm_p']."',password='".encrypt_password2($_POST['adm_p'])."' where uid=1");

            #更新医院名称
            $link->query("update ".$data['DB_PREFIX']."his_hospital set hospital_name='".$_POST['hospital_name']."',owner_name='".$_POST['owner_name']."' where id=1");


        }else{

            #初始化
            $link->query(sprintf($sql_user,$data['DB_PREFIX']));

            $uid = $link->insert_id;

            $link->query(sprintf($sql_hospital,$data['DB_PREFIX'],$uid));

            $link->query(sprintf($sql_access,$data['DB_PREFIX'],$uid,1));
            #$link->query(sprintf($sql_access,$data['DB_PREFIX'],$uid,2));
            #$link->query(sprintf($sql_access,$data['DB_PREFIX'],$uid,3));

            $link->query(sprintf($sql_wx,$data['DB_PREFIX'],$uid));
        }

        $link->close();

        goto end;

        end:

        $MAIN_SERVER_DOMAIN = 'http://'.$_SERVER['HTTP_HOST'].'/';

        $db_str = <<<php
<?php
return array(

//*************************************数据库设置*************************************
    'DB_TYPE'               =>  '{$DB_TYPE}',                 // 数据库类型
    'DB_HOST'               =>  '{$data['DB_HOST']}',     // 服务器地址
    'DB_NAME'               =>  '{$data['DB_NAME']}',     // 数据库名
    'DB_USER'               =>  '{$data['DB_USER']}',     // 用户名
    'DB_PWD'                =>  '{$data['DB_PWD']}',      // 密码
    'DB_PORT'               =>  '{$data['DB_PORT']}',     // 端口
    'DB_PREFIX'             =>  '{$data['DB_PREFIX']}',   // 数据库表前缀
    'MAIN_SERVER_DOMAIN'             =>  '{$MAIN_SERVER_DOMAIN}',   // 主域名
    #用户权限管理
    'AUTH_CONFIG'=>array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => '{$data['DB_PREFIX']}his_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => '{$data['DB_PREFIX']}his_auth_group_access', //用户组明细表
        'AUTH_RULE' => '{$data['DB_PREFIX']}his_auth_rule', //权限规则表
        'AUTH_USER' => '{$data['DB_PREFIX']}his_member'//用户信息表
    ),
);
php;

        // 创建数据库链接配置文件
        file_put_contents('../../Application/Common/Conf/db.php', $db_str);
        @touch('./install.lock');
        if(file_exists('../../index.html'))unlink('../../index.html');
        require './success.html';
        exit;

        err:

        exit('数据库驱动只支持：mysql,mysqli');
    }

}



function encrypt_password2($password=''){
    $options = array(
        'cost' => 12,
        'salt' => @mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
    );
    return @password_hash($password, PASSWORD_BCRYPT, $options); //使用BCRYPT算法加密密码
}