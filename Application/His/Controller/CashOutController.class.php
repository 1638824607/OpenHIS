<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

namespace His\Controller;
use Common\Controller\PublicBaseController;
use Org\Wx\Wechat;

/**
 * 提现功能
 * CashOutController
 * Author: wsl
 */
class CashOutController extends PublicBaseController
{
    protected $conf;

    public function _initialize()
    {
        C('TITLE',"提现功能");
        C('KEYEORDS',"");
        C('DESCRIPTION',"");
        parent::_initialize();

    }

    /**
     * 首页
     * @Author   wsl
     * @DateTime 2017-10-24
     */
    public function index()
    {
        $cash_id = I('get.cash_id',1);
        if(!$cash_id)exit('cash_id not found');

        $cash = M('His_cash_out')->where("id='$cash_id'")->find();

        if(!$cash)exit('cash_id invalid');

        #获取配置
        $wh = ($cash['appid']?"appid='$cash[appid]":($cash['hospital_id']?"userid='$cash[hospital_id]'":""));
        if(!$wh)exit('Err:config can not get');

        $this->conf = M('His_wxmp')->where($wh)->find();
        if(!$this->conf)exit('config not setting ?');

        #生成企业付款记录
        $mpay = array(
            'hospital_id'=>$cash['hospital_id'],
            'cash_out_id'=>$cash_id,
            'addtime'=>time(),
            'status'=>0,
            'partner_trade_no'=>date('YmdHis').'20'.$cash['hospital_id'].'20'.$cash['user_id'].'20'.rand(1000,9999),
            'memo'=>'用户提现',

        );

        $mchpay_id = M('His_mchpay')->data($mpay)->add();

        if(!$mchpay_id)exit('insert mchpay log fail');

        $rc = $this->pay($cash['openid'],$mpay['partner_trade_no'],$cash['amount'],$mpay['memo']);

        print_r($rc);
    }


    /**
     * 企业支付
     * @param string $openid    用户openID
     * @param string $trade_no  单号
     * @param string $money     金额
     * @param string $desc      描述
     * @return string   XML 结构的字符串
     */
    public function pay($openid,$trade_no,$money,$desc){

        $wx = new Wechat();

        $data = array(
            'mch_appid' => $this->conf['appid'],
            'mchid'     => $this->conf['mchid'],
            'nonce_str' => $this->generate_password(16),
            //'device_info' => '1000',
            'partner_trade_no' => $trade_no, //商户订单号，需要唯一
            'openid'    => $openid,
            'check_name'=> 'NO_CHECK', //OPTION_CHECK不强制校验真实姓名, FORCE_CHECK：强制 NO_CHECK：
            //'re_user_name' => 'jorsh', //收款人用户姓名
            'amount'    => $money * 100, //付款金额单位为分
            'desc'      => $desc,
            'spbill_create_ip' => C('SPBILL_CREATE_IP')
        );

        //生成签名
        $data['sign'] = $wx->makeSign($data,$this->conf['mchkey']);
        //构造XML数据
        $xmldata = $wx->makeXml($data);

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        //发送post请求
        $res = $this->curl_post_ssl($url, $xmldata);


        if(!$res){
            return array('status'=>1, 'msg'=>"Can't connect the server" );
        }


        if(function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);

        $content = (array)simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);

        //付款结果分析

        if(strval($content['return_code']) == 'FAIL'){
            return array('status'=>1, 'msg'=>strval($content['return_msg']));
        }
        if(strval($content['result_code']) == 'FAIL'){
            return array('status'=>1, 'msg'=>strval($content['err_code']),':'.strval($content['err_code_des']));
        }
        $resdata = array(
            'return_code'      => strval($content['return_code']),
            'result_code'      => strval($content['result_code']),
            'nonce_str'        => strval($content['nonce_str']),
            'partner_trade_no' => strval($content['partner_trade_no']),
            'payment_no'       => strval($content['payment_no']),
            'payment_time'     => strval($content['payment_time']),
        );
        return $resdata;
    }

   public function generate_password( $length = 8 ) {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

    /**
     * 最简单的XML转数组
     * @param string $xmlstring XML字符串
     * @return array XML数组
     */
    function simplest_xml_to_array($xmlstring) {
        return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
    }
    /**
     * 企业付款发起请求
     * 此函数来自:https://pay.weixin.qq.com/wiki/doc/api/download/cert.zip
     */
    public function curl_post_ssl($url, $xmldata, $second=30,$aHeader=array()){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);


        #ssl_cert_path
        $ssl_cert_path = APP_PATH.'pem/'.($this->conf['ssl_cert_path']?$this->conf['ssl_cert_path']:$this->conf['appid']).'/';


        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,$ssl_cert_path.'apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,$ssl_cert_path.'apiclient_key.pem');

        //第二种方式，两个文件合成一个.pem文件
        //curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xmldata);
        $data = curl_exec($ch);

        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }

    /**
     * 调用tp系统日志
     * @param $msg
     * Author: wsl
     */
    protected function log($msg)
    {
        if(is_string($msg)){
            \Think\Log::write($msg);
        }else{
            \Think\Log::write(json_encode($msg));
        }
    }

}
?>