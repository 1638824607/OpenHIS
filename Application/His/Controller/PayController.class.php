<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

namespace His\Controller;
use Common\Controller\BaseController;
use Org\Wx\Wechat;
use \Think\Log;

date_default_timezone_set("PRC");

/**
 * 统一支付页面
 * PayController
 * Author: wsl
 */
class PayController extends BaseController {

    public $hid;
    public $pkg_id;
    public $pkg;
    public $openid;
    public $wxsdk;
    private $conf;
    private $db;
    private $appid;
    private $ua;
    private $L_type = array('处方费','挂号费','咨询费');
    private $tab_pre;
    public function _initialize()
    {
        parent::_initialize();

        $this->tab_pre = C('DB_PREFIX');
        #浏览器标识
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            $this->ua='wx';
            $this->openid = session('openid_pay');

        }elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'AliApp(AP') !== false ) {
            $this->ua='ali';
        }else{
            $this->ua='';
        }

        C('TITLE',"支付中心");

        $this->db = M();
    }

    /**
     * 根据浏览器标识进行判断
     * Author: wsl
     */
    public function go()
    {
        $this->pkg_id = I('get.id',0);
        if($this->pkg_id ){
            session('pkg_id_pay',$this->pkg_id);
        }else{
            $this->pkg_id = session('pkg_id_pay');
        }

        if($this->ua=='')exit('<h1>目前只支持微信和支付宝</h1>');

        header("Location:/Pay/".$this->ua);
    }

    /**
     * 初始化订单
     * Author: wsl
     */
    public function initPkg()
    {
        #订单信息
        $this->pkg_id = I('get.id',0);
        if($this->pkg_id ){
            session('pkg_id_pay',$this->pkg_id);
        }else{
            $this->pkg_id = session('pkg_id_pay');
        }
        if(!$this->pkg_id)$this->error('缺少参数：pkg_id');

        $this->pkg = M('His_care_pkg')->where("id='$this->pkg_id'")->find();
        if(!$this->pkg)$this->error('pkg_id无效');
        if($this->pkg['status']!=0)$this->error('订单状态不支持');
        $this->hid = $this->pkg['hospital_id'];

        #配置信息
        $this->conf = M('His_wxmp')->where("userid='$this->hid'")->find();

        if(!$this->conf)exit('获取配置信息出错:'.$this->hid);

        $this->appid = $this->conf['appid'];
    }

    /**
     * 退单入口
     * Author: wsl
     */
    public function refund()
    {
        $paylog_id = I('get.paylog_id',0);

        $amount = I('post.amount','all');//all就是是全部
        $adm_uid = I('post.adm_uid',0);//all就是是全部
        $adm_memo = I('post.adm_memo','退款');//all就是是全部

        if(!$paylog_id||!$amount)$this->resJSON(1,'参数缺失:paylog_id or amount');

        #todo 这里需要添加功能权限，无权限不能使用



        $sql = "SELECT a.*,b.hospital_id,b.type_id,b.order_code,b.ol_pay_part,b.amount,b.patient_id FROM ".$this->tab_pre."his_care_paylog a LEFT JOIN ".$this->tab_pre."his_care_pkg b ON a.pkg_id=b.id WHERE a.id='$paylog_id' LIMIT 1";

        $r = $this->db->query($sql);
        if(!$r)$this->resJSON(2,'paylog_id无效',$sql);

        $paylog = $r[0];

        if(!in_array($paylog['payment_platform'],array(1,2))) $this->resJSON(3,'此支付记录非微信或支付宝，无法使用微信退单');

        if($paylog['status']!=1) $this->resJSON(3,'状态不支持');

        if($amount=='all')$amount = $paylog['pay_amount'];

        if($amount<=0)$this->resJSON(4,'退款金额应大于0');

        #配置信息
        $this->conf = M('His_wxmp')->where("userid='$paylog[hospital_id]'")->find();
        if(!$this->conf)$this->resJSON(5,'获取配置信息出错');

        $log = array(
            'pkg_id'=>$paylog['pkg_id'],
            'status'=>1,
            'adm_uid'=>$adm_uid,
            'adm_ip'=>get_client_ip(),
            'adm_memo'=>$adm_memo,
        );


        if($paylog['payment_platform']==1){
            $rs = $this->wx_refund($paylog,$amount);

            $log['payment_platform'] = 1;
            $log['platform_code'] = $rs['refund_id'];
            $log['refund_amount'] = $rs['refund_fee']/100;
        }else{
            $paylog['adm_memo'] =  $adm_memo;

            $result = $this->ali_refund($paylog,$amount);

            $log['payment_platform'] = 2;
            $log['platform_code'] = $result->trade_no;
            $log['refund_amount'] = $result->refund_fee;

        }
        #更新
        M('His_care_pkg')->where("id='$paylog[pkg_id]'")->save(array('status'=>4));

        #记录
        $log_id = M('His_care_refundlog')->add($log);
        if(!$log_id)$this->resJSON(8,'记录失败');


        $this->resJSON(0,'ok',array('refund_id'=>$log_id));
    }

    /**
     * 微信支付
     * Author: wsl
     */
    public function wx()
    {

        $this->initPkg();

        $this->openid=$this->getOpenid($this->conf['appid']);

        $wxmp = new Wechat($this->conf);

        #jsapi 自动关闭页面
        $rc = $wxmp->getJsSign();
        if(!$rc)$this->error('Js Api Error');
        $rc['debug'] = false;
        $rc['jsApiList'] = array('checkJsApi','closeWindow');

        $this->assign('arr_js', json_encode($rc));

       $body = isset($this->L_type[$this->pkg['type_id']])?$this->L_type[$this->pkg['type_id']]:'默认产品名';

        #jsapi 微信支付
        $data=array(
            'amount'=>$this->pkg['ol_pay_part']>0?$this->pkg['ol_pay_part']:0.01,
            'body'=>$body,
            'order_code'=>$this->pkg['order_code'],
            #'product_id'=>'',
            'openid'=>$this->openid
        );

        list($wx_order_add,$arr) = $wxmp->addWxOrder($data,$this->conf);
        if($wx_order_add!=0){
            $this->log($arr->err_code_des);
            $this->log($data);
            $this->log($arr);
            $this->log($this->conf);exit;
        }
        $this->assign('arr_json', json_encode($arr));


        $hospital = M('His_hospital')->where("hid='$this->hid'")->find();
        $this->assign('hospital',$hospital);
        $this->assign('pkg',$this->pkg);

        $this->display();
    }

    /**
     * 微信支付异步回调
     * Author: wsl
     */
    public function wx_notify()
    {
        $wxmp = new Wechat();

        $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        $xml or $wxmp->wx_exit();
        if(function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);
        $x = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $x = (array)$x;

        $this->log($x);

        if(isset($x['transaction_id'])) {

            $post = array();

            foreach($x as $k=>$v) {
                $post[$k] = $v;
            }

            unset($post['sign']);

            #获取pkg信息
            $care_pkg = M('His_care_pkg');
            $order_code = $post['out_trade_no'];

            $pkg = $care_pkg->where("order_code='$order_code'")->find();
            if(!$pkg){
                $this->log('未知order_code:'.$order_code);
                $wxmp->wx_exit();
            }

            #获取医院微信支付密钥
            $his_wxmp = M('His_wxmp');
            $mchkey = $his_wxmp->where("userid='$pkg[hospital_id]'")->getField('mchkey');

            if(!$mchkey){
                $this->log('未处理订单：'.$order_code);
                $wxmp->wx_exit();
            }

            #验证签名
            if($post['result_code'] == 'SUCCESS' && $wxmp->makeSign($post, $mchkey) == $x['sign']) {
                $total_fee = $post['total_fee']/100;

                #如果已经更新
                if($pkg['status']!=0)$wxmp->wx_exit('ok');

                $up_status = ($pkg['amount']>$total_fee)?5:1;

                #更新数据
                $care_pkg->where('id='.$pkg['id'])->save(array('status'=>$up_status));

                #记录
                $paylog = array(
                    'pkg_id'=>$pkg['id'],
                    'platform_code'=>$post['transaction_id'],
                    'payment_platform'=>1,
                    'pay_amount'=>$total_fee,
                    'status'=>1,
                    'addtime'=>time()
                );
                $care_paylog = M('His_care_paylog');

                $care_paylog->data($paylog)->add();

                $wxmp->wx_exit('ok');
            }else{
                $this->log('验证签名失败:'.json_encode($x));
            }
        }

        $wxmp->wx_exit();

    }

    /**
     * 微信退单
     * @param $paylog
     * @param $amount
     * @return array
     * Author: wsl
     */
    protected function wx_refund($paylog,$amount)
    {
        $data = array(
            'appid'=>$this->conf['appid'],
            'mch_id'=>$this->conf['mchid'],
            'nonce_str'=>md5(md5($this->conf['mchkey'].time().rand(100,999))),
            'out_refund_no'=>date('YmdHis').'88'.$paylog['hospital_id'].'88'.$paylog['patient_id'].'88'.rand(1000,9999),#商户退款单号,
            'refund_fee'=>$amount*100,#单位：分
            'total_fee'=>$paylog['pay_amount']*100#单位：分
        );


        if($paylog['platform_code']){
            $data['transaction_id'] = $paylog['platform_code'];
        }else{
            $data['out_trade_no'] = $paylog['order_code'];
        }

        $wxmp = new Wechat($this->conf);
        #签名
        $data['sign'] = $wxmp->makeSign($data,$this->conf['mchkey']);

        $xml = $wxmp->makeXml($data);

        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $rc = $wxmp->curl_post_ssl($url,$xml);

        if(!$rc)$this->resJSON(6,$wxmp->errMsg);

        if(function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);

        $x = simplexml_load_string($rc, 'SimpleXMLElement', LIBXML_NOCDATA);

        $rs = (array)$x;

        if(!$rs['refund_id'])$this->resJSON(7,$rs['err_code_des']);

        return $rs;
    }

    /**
     * 支付宝支付
     * Author: wsl
     */
    public function ali()
    {
        $this->initPkg();
        require_once THINK_PATH.'Library/Vendor/aliwap/wappay/service/AlipayTradeService.php';
        require_once THINK_PATH.'Library/Vendor/aliwap/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';


        $config = array (
            //应用ID,您的APPID。
            'app_id' => $this->conf['app_id'],

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => $this->conf['merchant_private_key'],

            //异步通知地址
            'notify_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_notify",

            //同步跳转
            'return_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_pay_done",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => $this->conf['alipay_public_key'],


        );

            //商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $this->pkg['order_code'];

            //订单名称，必填
            $subject =  isset($this->L_type[$this->pkg['type_id']])?$this->L_type[$this->pkg['type_id']]:'默认产品名';

            //付款金额，必填
            $total_amount = $this->pkg['ol_pay_part']>0?$this->pkg['ol_pay_part']:0.01;

            //商品描述，可空
            $body = $this->pkg['id'];

            //超时时间
            $timeout_express="1m";


            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);

            $payResponse = new \AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);


        $this->display();
    }

    /**
     * 支付宝异步回调
     * Author: wsl
     */
    public function ali_notify()
    {

        if(!$_POST)exit('fail');

        require_once THINK_PATH.'Library/Vendor/aliwap/wappay/service/AlipayTradeService.php';

        $arr = $_POST;

        //商户订单号
        $order_code = $_POST['out_trade_no'];
        if(!$order_code)exit('fail');

        $care_pkg = M('His_care_pkg');

        $pkg = $care_pkg->where("order_code='$order_code'")->find();
        if(!$pkg){
            $this->log('获取pkg出错：'.$order_code);
            exit('fail');
        }
        if($pkg['status']!=0){
            $this->log('状态不支持：'.$pkg['status'].',pkg_id:'.$pkg['id']);
            exit('success');
        }

        #验证
        #获取医院支付密钥
        $his_wxmp = M('His_wxmp');
        $his = $his_wxmp->field('app_id,merchant_private_key,alipay_public_key')->where("userid='$pkg[hospital_id]'")->find();

        $config = array (
            //应用ID,您的APPID。
            'app_id' => $his['app_id'],

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => $his['merchant_private_key'],

            //异步通知地址
            'notify_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_notify",

            //同步跳转
            'return_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_pay_done",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => $his['alipay_public_key'],
        );


        $alipaySevice = new \AlipayTradeService($config);
        #$this->log(__FUNCTION__.':'.__LINE__);
        $result = $alipaySevice->check($arr);
        #$this->log(__FUNCTION__.':'.__LINE__);
        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功

            #$this->log(__FUNCTION__.':'.__LINE__);

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            #$out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            $total_fee = $_POST['total_amount'];

           /* #if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            #}
            #else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            #}
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——*/

            $up_status = ($pkg['amount']>$total_fee)?5:1;

            #更新数据
            $care_pkg->where('id='.$pkg['id'])->save(array('status'=>$up_status));

            #记录
            $paylog = array(
                'pkg_id'=>$pkg['id'],
                'platform_code'=>$_POST['trade_no'],
                'payment_platform'=>2,
                'pay_amount'=>$total_fee,
                'status'=>1,
                'addtime'=>time()
            );
            $care_paylog = M('His_care_paylog');

           $add_log =  $care_paylog->data($paylog)->add();

           if(!$add_log){

               $this->log('添加支付宝记录出错');
               $this->log($paylog);

           }

            echo "success";		//请不要修改或删除

        }else {
            //验证失败
            echo "fail";	//请不要修改或删除
            $this->log('支付宝，签名验证失败');
            $this->log($this->conf);
            $this->log($_POST);
        }
    }

    /**
     * 支付宝支付 同步跳转页面
     * Author: wsl
     */
    public function ali_pay_done()
    {
        $amount = $_GET['total_amount'];

        if($amount>0){
            $msg = '恭喜！支付成功';
        }else{
            $msg = '支付失败';
        }

        $this->assign('msg', $msg);
        $this->assign('amount',sprintf("%.2f",$amount));
        $this->display();
    }

    /**
     * 支付宝退款
     * @param $paylog
     * @param $amount
     * @return bool|mixed|\SimpleXMLElement|\SimpleXMLElement[]|string|\提交表单HTML文本
     * Author: wsl
     */
    protected function ali_refund($paylog,$amount)
    {

        require_once THINK_PATH.'Library/Vendor/aliwap/wappay/service/AlipayTradeService.php';
        require_once THINK_PATH.'Library/Vendor/aliwap/wappay/buildermodel/AlipayTradeRefundContentBuilder.php';



        $config = array (
            //应用ID,您的APPID。
            'app_id' => $this->conf['app_id'],

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => $this->conf['merchant_private_key'],

            //异步通知地址
            'notify_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_notify",

            //同步跳转
            'return_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_pay_done",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type'=>"RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => $this->conf['alipay_public_key'],


        );


/*
        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        //商户订单号，和支付宝交易号二选一
        #$out_trade_no = trim($_POST['WIDout_trade_no']);

        //支付宝交易号，和商户订单号二选一
        #$trade_no = trim($_POST['WIDtrade_no']);

        //退款金额，不能大于订单总金额
        #$refund_amount=trim($_POST['WIDrefund_amount']);

        //退款的原因说明
        #$refund_reason=trim($_POST['WIDrefund_reason']);*/

        //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
        #$out_request_no=trim($_POST['WIDout_request_no']);
        $out_request_no=date('YmdHis').'87'.$paylog['hospital_id'].'87'.$paylog['patient_id'].'87'.rand(1000,9999);#商户退款单号;

        $RequestBuilder = new \AlipayTradeRefundContentBuilder();

        if($paylog['platform_code']){
            $RequestBuilder->setTradeNo($paylog['platform_code']);
        }else{
            $RequestBuilder->setOutTradeNo($paylog['order_code']);
        }

        $RequestBuilder->setRefundAmount($amount);
        $RequestBuilder->setRefundReason($paylog['adm_memo']);
        $RequestBuilder->setOutRequestNo($out_request_no);

        $Response = new \AlipayTradeService($config);
        $result=$Response->Refund($RequestBuilder);

        if($result->code!='10000')$this->resJSON(6,$result->msg.','.$result->sub_msg);

        return $result;
    }

    /**
     * 调用tp日志
     * @param $msg
     * Author: wsl
     */
    protected function log($msg)
    {
        if(is_string($msg)){
            Log::write($msg);
        }else{
            Log::write(json_encode($msg));
        }
    }
}