<?php
class alipay{
    private $alipay_config = array(
        'partner' => '',//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
        'seller_id' => '',//收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
        'key' => '',// MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
        'notify_url' => '',// 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        'return_url' => '',// 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        'sign_type' => '',//签名方式 strtoupper('MD5')
        'input_charset' => 'utf-8',//字符编码格式 目前支持 gbk 或 utf-8 strtolower('utf-8')
        'cacert' => '\\cacert.pem',//请保证cacert.pem文件在当前文件夹目录中 getcwd()
        'transport' => 'http',//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'payment_type' => '1',// 支付类型 ，无需修改
        'service' => 'create_direct_pay_by_user',//默认为pc端地址，根据需求更改
        'anti_phishing_key' => '',// 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
        'exter_invoke_ip' => '',//客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
    );
    public function __construct($partner,$key,$notify_url){
        $this->alipay_config['partner'] = $partner;
        $this->alipay_config['seller_id'] = $partner;
        $this->alipay_config['key'] = $key;
        $this->alipay_config['notify_url'] = $notify_url;
        $this->alipay_config['sign_type'] = strtoupper('MD5');
        $this->alipay_config['cacert'] = __DIR__.$this->alipay_config['cacert'];
    }
    public function set_alipay_config($key,$val){
        $this->alipay_config[$key] = $val;
    }
    public function get_alipay_config(){
        return $this->alipay_config;
    }
    public function pay($total_fee,$out_trade_no,$subject,$body = ''){
        require_once("lib/alipay_submit.class.php");
        $parameter = array(
            "service"       => $this->alipay_config['service'],
            "partner"       => $this->alipay_config['partner'],
            "seller_id"  => $this->alipay_config['seller_id'],
            "payment_type"	=> $this->alipay_config['payment_type'],
            "notify_url"	=> $this->alipay_config['notify_url'],
            "return_url"	=> $this->alipay_config['return_url'],
        
            "anti_phishing_key"=>$this->alipay_config['anti_phishing_key'],
            "exter_invoke_ip"=>$this->alipay_config['exter_invoke_ip'],
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "_input_charset"	=> trim(strtolower($this->alipay_config['input_charset']))
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            //如"参数名"=>"参数值"
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        return $html_text;
    }
    public function get_notify_data(){
        require_once("lib/alipay_notify.class.php");
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        $ret = array();
        if($verify_result) {//验证成功
            //商户订单号
            $ret['out_trade_no'] = $_POST['out_trade_no'];
            //支付宝交易号
            $ret['trade_no'] = $_POST['trade_no'];
            //交易金额
            $ret['total_amount'] = $_POST['total_amount'];
            //卖家id
            $ret['seller_id'] = $_POST['seller_id'];
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                $ret['trade_status'] = 'finished';
            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $ret['trade_status'] = 'success';
            }
            return $ret;
        }
        //验证失败
        return false;
    }
}