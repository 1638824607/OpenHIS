<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

namespace Common\Model;
use Org\Util\Wechat;
use Org\Wx\Wechat as Wechat2;
use Common\Model\WxqrloginModel;
use Org\Nx\Curl;

/**
 * 微信公众平台基础信息
 * WxmpModel
 * Author: wsl
 */
class WxmpModel extends BaseModel
{
    protected $tableName = 'his_wxmp';

    protected $appid;

    protected $conf=null;

    /**
     * 传入appid
     * @param $id 传入appid
     * Author: wsl
     */
    public function init($id){
        $this->setAppid($id);
        $this->conf = $this->getOne();
    }

    /**
     * 获取一条数据,微信配置
     * @return mixed
     * Author: wsl
     */
    public function getOne()
    {
            $where = array(
                'appid' => $this->appid
            );

        return $this->where($where)->find();
    }

    /**
     * 更新一条数据
     * @param $data
     * @param $id
     * @param string $f
     * @return bool
     * Author: wsl
     */
    public function updateOne($data,$id,$f='id'){
        $where = array(
            $f => $id,
        );
        return $this->where($where)->save($data);
    }

    /**
     * 设置appid
     * @param $appid
     * Author: wsl
     */
    public function setAppid($appid){
        $this->appid=$appid;
    }

    /**
     * 获取微信access_token
     * @param int $update
     * @return bool|mixed
     * Author: wsl
     */
    public function getAccessToken($update=0)
    {

        $wx = new Wechat2($this->conf);

        return $wx->checkAuth();
    }

    #获取getJsTicket
    public function getJsTicket(){
        #判断过期
        if($this->conf['jsapi_ticket_expires'] > time()){
            return $this->conf['jsapi_ticket'];
        }

        $wx = new Wechat($this->conf);
        $at = $this->getAccessToken();

        $wx->setAccessToken($at);


        $rc = $wx->getJsTicket();

        #保存入库
        if($rc){
            $this->conf['jsapi_ticket']=$rc;
            $this->conf['jsapi_ticket_expires']=time()+7000;
            $this->updateOne(array('jsapi_ticket'=>$rc,'jsapi_ticket_expires'=>$this->conf['jsapi_ticket_expires']),$this->conf['appid'],'appid');
        }

        return $rc;
    }


    public function getJsSign($jst){
        $wx = new Wechat($this->conf);
        $wx->setJsapiTicket($jst);
        $rc = $wx->getJsSign();
        return $rc;
    }



    public function getLoginQr($qrid=0){

        if(!$qrid){
            #插入
            $logger = new WxqrloginModel();
            $qrid = $logger->addqrlog();
        }

        return $qrid;

        if(!$qrid)return 0;

        #创建临时二维码
        $at = $this->getAccessToken();

        $wx = new Wechat($this->conf);

        $wx->setAccessToken($at);

        $rc = $wx->getQRCode($qrid);

        if(isset($rc['url']))$rc['base_code']=$qrid;

        return $rc;
    }

    public function updateLoginQr($id,$openid)
    {
        $logger = new WxqrloginModel();
        return $logger->updateOne(array('openid'=>$openid,'status'=>2),$id);

    }

    public function getOpenid(){
        $wx = new Wechat($this->conf);
        $r = $wx->getOauthAccessToken();
        if(!isset($r['openid'])){
            echo $wx->errMsg;
        }
        return $r;
    }


    public function getAuth($access_token,$openid){
        $wx = new Wechat($this->conf);
        $r = $wx->getOauthUserinfo($access_token,$openid);

        return $r;

        if(!isset($r['openid'])){
            echo $wx->errMsg;
        }



        return $r;
    }


    public function getForeverMedia($media_id,$hospital_id,$pkg_id,$ext='amr')
    {

        $up_dir = str_replace('ThinkPHP','Upload',THINK_PATH).'question/'.$hospital_id.'/'.$pkg_id.'/';//存放在当前目录的upload文件夹下
        $up_url = C('UPLOAD_QUESTION').$hospital_id.'/'.$pkg_id.'/';

        if(!file_exists($up_dir))mkdir($up_dir,0777,true);


        #$wx = new Wechat($this->conf);

        $at = $this->getAccessToken();

        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$at&media_id=$media_id";
        $curl = new Curl($url);
        #$rec = $this->dcurl($url);
        $rec = $curl->get();



        if(substr($rec,0,1)=='{'){
            #access_token超时判断
            $rc = json_decode($rec,true);
            if($rc['errcode']!=40001){

                \Think\Log::write('wsl001,media_id:'.$media_id.',at:'.$at.',errmsg:'.$rc['errmsg'],'DEBUG');
                return false;
            }
            unset($curl);
            $at = $this->getAccessToken(1);
            $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$at&media_id=$media_id";
            $curl = new Curl($url);
            #$rec = $this->dcurl($url);
            $rec = $curl->get();

            if(substr($rec,0,1)=='{'){
                \Think\Log::write('wsl002,media_id:'.$media_id.',at:'.$at.',errmsg:'.$rc['errmsg'],'DEBUG');
                return false;
            }

        }

        $filename = md5(rand(100,999).time());
        $save_path = $up_dir.$filename;
        if(file_put_contents($save_path,$rec)){

            #TODO amr2mp3 这里添加转码功能，注意返回文件名，扩展名
            $this->amr2mp3($save_path);

            return $up_url.$filename.'.mp3';
        }else{
            \Think\Log::write('保存文件失败:'.$save_path,'DEBUG');
            return false;
            #return array(1,'保存文件失败');
        }


/*
        $wx->setAccessToken($at);



        list($status,$rc) = $wx->getForeverMedia($media_id,$up_dir,$ext);
        if($status!=0){
            \Think\Log::write('$rc:'.$rc,'DEBUG');
            return false;
        }*/
        #return $up_url.$filename;
    }


    protected function amr2mp3($in)
    {
        #$amr = './' . $vo['voice'];
        #$mp3 = $amr . '.mp3';

        $out = $in.'.mp3';

        if (file_exists($out) == true) {
            // exit('无需转换');
        } else {
            $command = "/usr/bin/ffmpeg -i $in $out";
            exec($command, $error);
        }
    }

    protected function dcurl($url, $par = '') {
        if(function_exists('curl_init')) {
            $cur = curl_init($url);
            if($par) {
                curl_setopt($cur, CURLOPT_POST, 1);
                curl_setopt($cur, CURLOPT_POSTFIELDS, $par);
            }
           # curl_setopt($cur, CURLOPT_REFERER, DT_PATH);
            curl_setopt($cur, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($cur, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($cur, CURLOPT_HEADER, 0);
            curl_setopt($cur, CURLOPT_TIMEOUT, 30);
            curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($cur, CURLOPT_RETURNTRANSFER, 1);
            $rec = curl_exec($cur);
            curl_close($cur);
            return $rec;
        }
        return file_get($par ? $url.'?'.$par : $url);
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
        $post['notify_url'] = C('MAIN_SERVER_DOMAIN').'Pay/wx_notify';//_URL_.'Login/wx_notify_url';
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

    /**高明强  创建二维码ticket
     * @param $scene_id
     * @param string $type
     * @return array
     */
    public function getQRCodeTic($scene_id,$type='1'){
        $wx = new Wechat($this->conf);

        $at=$this->getAccessToken();
        $wx->setAccessToken($at);

        $ticket = $wx->getQRCode($scene_id,$type);
        $data['ticket'] = $ticket['ticket'];
        $data['url'] = $ticket['url'];
        $data['gcode'] = $wx->getQRUrl($data['ticket']);
        return $data;

    }
    /**
     * 高明强  判断用户是否关注此公众号
     *
     */
//      public function isSubscribe($openid){
//          $wx = new Wechat($this->conf);
//          $is_attention = $wx->getUserInfo($openid);
//          if($is_attention['subscribe']==0){
//              return false;
//          }else{
//              return true;
//          }
//
//      }
     /*
      * 高明强
      * 获得扫码是事件消息
      */
      public function getWxMeassage(){

          $wx = new Wechat($this->conf);
          $at=$this->getAccessToken();
          $wx->setAccessToken($at);
          $postdata = $wx->getRev()->getRevData();
          return $postdata;

      }
      /*
       * 高明强
       * 发送消息
       */
      public function  sendMeassage($data){
          $wx = new Wechat($this->conf);
//          return $wx->makeText($FromUserName,$ToUserName,$contentStr);

          $at=$this->getAccessToken();
          $wx->setAccessToken($at);

          $wx->sendCustomMessage($data);
      }
      public function  sendText(){

      }
}