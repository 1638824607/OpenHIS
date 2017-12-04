<?php
/**
 * Created by PhpStorm.
 * User: wsl
 * Date: 2017/11/6
 * Time: 18:50
 */

namespace Org\Wx;
use \Think\Log;
use Org\Nx\Curl;

class Wechat extends Wxsdk
{
    public $hid;
    public $conf;
    public function __construct($options=array())
    {
        if($options)$this->conf = $options;
        $this->hid = isset($options['userid']) ? $options['userid'] : 0;

        parent::__construct($options);
    }

    /**
     * log overwrite
     * @see Wechat::log()
     */
    protected function log($log){
        if ($this->debug) {
            if (function_exists($this->logcallback)) {
                if (is_array($log)) $log = print_r($log,true);
                return call_user_func($this->logcallback,$log);
            }elseif (class_exists('Log')) {
                Log::write($this->hid.',wechat：'.$log, Log::DEBUG);
            }
        }
        return false;
    }

    /**
     * 重载设置缓存
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename,$value,$expired){
        return S($cachename.'_'.$this->hid,$value,$expired);
    }

    /**
     * 重载获取缓存
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename){
        return S($cachename.'_'.$this->hid);
    }

    /**
     * 重载清除缓存
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename){
        return S($cachename.'_'.$this->hid,null);
    }



    public function makeSign($arr, $key) {
        ksort($arr);
        $str = '';
        foreach($arr as $k=>$v) {
            if($v) $str .= $k.'='.$v.'&';
        }
        $str .= 'key='.$key;
        return strtoupper(md5($str));
    }

    public function makeXml($arr) {
        $str = '<xml>';
        foreach($arr as $k=>$v) {
            if(is_numeric($v)) {
                $str .= '<'.$k.'>'.$v.'</'.$k.'>';
            } else {
                $str .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
            }
        }
        $str .= '</xml>';
        return $str;
    }

    #微信公众平台JSAPI订单
    public function addWxOrder($order,$set)
    {
        $post = array();
        $post['appid'] = $set['appid'];###
        $post['mch_id'] = $set['mchid'];###
        $post['nonce_str'] = md5(md5($set['mchkey'].$order['amount']));###==
        $post['body'] = $order['body'];#==
        $post['out_trade_no'] = $order['order_code'];#==
        $post['total_fee'] = $order['amount']*100;#==
        $post['spbill_create_ip'] = C('SPBILL_CREATE_IP');
        $post['notify_url'] = C('MAIN_SERVER_DOMAIN').'Pay/wx_notify';
        $post['trade_type'] = 'JSAPI';
        #$post['product_id'] = $order['product_id'];#==
        $post['openid'] = $order['openid'];#==
        $post['sign'] = $this->makeSign($post, $set['mchkey']);###

        $curl = new Curl('https://api.mch.weixin.qq.com/pay/unifiedorder');

        $rec = $curl->post($this->makeXml($post));
        if(strpos($rec, 'prepay_id') !== false) {
            if(function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);
            $x = simplexml_load_string($rec, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            $x = simplexml_load_string($rec, 'SimpleXMLElement', LIBXML_NOCDATA);
            return array(1,$x);
        }
        $arr = array();
        $arr['appId'] = $set['appid'];
        $arr['timeStamp'] = ''.time().'';
        $arr['nonceStr'] = $post['nonce_str'];
        $arr['package'] = 'prepay_id='.$x->prepay_id;
        $arr['signType'] = 'MD5';
        $arr['paySign'] = $this->makeSign($arr, $set['mchkey']);

        return array(0,$arr);
    }

    public function wx_exit($type = '')
    {
        exit($type == 'ok' ? '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>' : '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[' . $type . ']]></return_msg></xml>');
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
            $this->errMsg = "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
}