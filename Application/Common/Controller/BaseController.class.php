<?php
namespace Common\Controller;
define('DEFAULT_KEY',"PHPDAC");
use Think\Controller;
/**
 * Base基类控制器
 */
class BaseController extends Controller{
    /**
     * 初始化方法
     */
    public $client_ip;
    protected $dev_mode=0;
    public function _initialize(){
        $this->client_ip = get_client_ip();
        if($this->client_ip==C('DEV_IP'))$this->dev_mode=1;
    }

    /**
    * 空方法
    * @Author   malixiao
    * @DateTime 2017-08-16
    * @param    [type]     $name [description]
    * @return   [type]           [description]
    */
    public function _empty($name){
    	layout(false);
        $this->display("Public/404");
    }
    
    /**
     * 错误返回
     * @Author   malixiao
     * @DateTime 2017-08-09
     * @param    string     $msg    [description]
     * @param    array      $fields [description]
     * @return   [type]             [description]
     */
    protected function ajaxError($msg='', $fields=array(), $status='error', $backUrl='')
    {
        header('Content-Type:application/json; charset=utf-8');
        $data = array('status'=>$status, 'msg'=>$msg, 'fields'=>$fields, 'backUrl'=>$backUrl);
        die( json_encode($data) );
    }
    
    /**
     * @Author   malixiao
     * @DateTime 2017-08-09
     * @param    [type]     $msg   [description]
     * @param    array      $_data [description]
     * @return   [type]            [description]
     */
    protected function ajaxSuccess($msg, $_data=array(), $status='success', $backUrl='')
    {
        header('Content-Type:application/json; charset=utf-8');
        $data = array('status'=>$status, 'msg' => $msg ,'data'=>$_data, 'backUrl'=>$backUrl);
        die( json_encode($data) );
    }


    public function resJSON($status=0,$msg='ok',$data=null,$stop=true){
        header('Content-Type:application/json; charset=utf-8');
        $r = array(
            'status'=>$status,
            'msg'=>$msg
        );
        if($data)$r['data']=$data;

        echo json_encode($r);
        if($stop)exit;
    }


    public function encrypt($txt, $key = '', $expiry = 0) {
        strlen($key) > 5 or $key = DEFAULT_KEY;
        $str = $txt.substr($key, 0, 3);
        return str_replace(array('+', '/', '0x', '0X'), array('-P-', '-S-', '-Z-', '-X-'), $this->mycrypt($str, $key, 'ENCODE', $expiry));
    }

    public function decrypt($txt, $key = '') {
        strlen($key) > 5 or $key = DEFAULT_KEY;
        $str = $this->mycrypt(str_replace(array('-P-', '-S-', '-Z-', '-X-'), array('+', '/', '0x', '0X'), $txt), $key, 'DECODE');
        return substr($str, -3) == substr($key, 0, 3) ? substr($str, 0, -3) : '';
    }

   public function mycrypt($string, $key, $operation = 'DECODE', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + $GLOBALS['DT_TIME'] : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - $GLOBALS['DT_TIME'] > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    #跨平台获取openid
    public function getOpenid($appid){
        $wx_cache_id = I('get.wx_cache_id',0);
        if($wx_cache_id){
            $r = M('His_wxopenid_cache')->where("id='$wx_cache_id'")->find();
            if(!$r)exit('wx_cache_id invalid');

            #删除？？
            M('His_wxopenid_cache')->where("id='$wx_cache_id'")->delete();
            return $r['openid'];
        }

        $data = array(
            'appid'=>$appid,
            'url'=>$this->get_url(),
            );

        $id = M('His_wxopenid_cache')->data($data)->add();
        if(!$id)exit('His_wxopenid_cache insert fail');
        $url = C('MAIN_SERVER_DOMAIN').'Wx/openid?wx_cache_id='.$id;
        header("Location:$url");exit;
    }


    /**
     * 获取当前页面完整URL地址
     */
    public function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }


    public function getRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        if(C('REDIS_AUTH'))$redis->auth(C('REDIS_AUTH'));

        return $redis;
    }

    #修改用户金额

    /**
     *
     * @param array $arr
     * @return bool
     * Author: wsl
     */
    public function userMoney(
        $arr = array(
            'operator_id' => 0,#操作人id
            'hospital_id' => 0,#所属医院id
            'user_id' => 0,#交易人id
            'type_id' => 0,#交易类型，0入，1出
            'amount' => 0,#本次交易金额必传
            'pkg_id' => 0,#相关订单id
            'memo' => '',#交易说明
        )
    )
    {

        #extract($arr);
        if(!$arr['user_id']||!$arr['amount'])return false;

        if(!in_array($arr['type_id'],array(0,1)))return false;

        $arr['addtime'] = time();
        $arr['ip'] = $this->client_ip;


        $user = M('His_member')->where("uid=".$arr['user_id'])->find();

        #交易后余额
        $new_val =$arr['type_id']==0?$user['money_balance']+$arr['amount']:$user['money_balance']-$arr['amount'];

        $arr['money_balance'] = $new_val;
        $arr['money_lock'] = $user['money_lock'];

        #记录
        M('His_transaction_record')->data($arr)->add();

        #更新
        M('His_member')->where("uid=".$arr['user_id'])->save(array('money_balance'=>$new_val));

        return true;
    }

    /**
     * 冻结，解冻，消除，用户锁定金额部分
     * @param array $arr
     * @return bool
     * Author: wsl
     */
    public function userMoneyLock(
        $arr = array(
            'operator_id' => 0,#操作人id
            'hospital_id' => 0,#所属医院id
            'user_id' => 0,#交易人id
            'type_id' => 0,#交易类型，0冻结，1解冻，2消除
            'amount' => 0,#本次交易金额必传
            'pkg_id' => 0,#相关订单id
            'memo' => '',#交易说明
        )
    )
    {

        #extract($arr);
        if(!$arr['user_id']||!$arr['amount'])return false;

        if(!in_array($arr['type_id'],array(0,1,2)))return false;

        $arr['addtime'] = time();
        $arr['ip'] = $this->client_ip;

        $user = M('His_member')->where("uid=".$arr['user_id'])->find();

        if($arr['type_id']==0){

            $money = $user['money_balance']-$arr['amount'];
            $lock = $user['money_lock']+$arr['amount'];
            $arr['memo'] .=" 冻结金额";
        }elseif($arr['type_id']==1){

            $money = $user['money_balance']+$arr['amount'];
            $lock = $user['money_lock']-$arr['amount'];
            $arr['memo'] .=" 解冻金额";
        }else{

            $money = $user['money_balance'];
            $lock = $user['money_lock']-$arr['amount'];
            $arr['memo'] .=" 消除冻结金额";
        }

        if($money<0)return false;

        $arr['money_balance'] = $money;
        $arr['money_lock'] = $lock;

        #记录
        M('His_transaction_record')->data($arr)->add();

        #更新
        M('His_member')->where("uid=".$arr['user_id'])->save(array('money_balance'=>$money,'money_lock'=>$lock));

        return true;
    }
}


