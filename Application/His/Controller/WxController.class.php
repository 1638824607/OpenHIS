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

/**
 * 微信公共功能
 * WxController
 * Author: wsl
 */
class WxController extends BaseController {

    public $hid;
    public $openid;
    public $wxsdk;
    private $conf;
    private $db;
    private $appid;
    private $tab_pre;
    public function _initialize()
    {

        parent::_initialize();
        $this->tab_pre = C('DB_PREFIX');

        isset($_GET['id'])||$_GET['id']=C('DEFAULT_HOSPITAL_ID');
        $this->hid = $_GET['id'];
        $this->db = M();

        $this->conf=S('conf_'.$this->hid);
        if(!$this->conf){
            $user=$this->db->query("SELECT b.userid,b.appid,b.appsecret,b.token,b.encodingaeskey,b.access_token,b.access_token_expires,b.jsapi_ticket,b.jsapi_ticket_expires,b.mchkey,b.mchid 
FROM ".$this->tab_pre."his_wxmp b 
WHERE ".(is_numeric($this->hid)?"b.userid='".intval($this->hid)."'":"b.appid='".$this->hid."'"));
            if(count($user)>0){
                $user=$user[0];
                $user['uid']=$_GET['hid'];
            }else{
                Log::write('1医院标识无效，请正确输入网址', Log::DEBUG);

            }

            $this->conf=$user;
            S('conf_'.$this->hid,$user);#缓存
        }

        if(!$this->conf){
            Log::write('2医院标识无效，请正确输入网址', Log::DEBUG);
            exit;
        }

        $this->hid = $this->conf['userid'];
        $this->appid = $this->conf['appid'];
    }

    /**
     * 对接微信公众平台开发模式入口
     * Author: wsl
     */
    public function api()
    {
        $this->wxsdk= new Wechat($this->conf);
        $this->wxsdk->valid();

        $qr_id = 0;
        $type = $this->wxsdk->getRev()->getRevType();


        $this->openid = $this->wxsdk->getRevFrom();

        $_res = array(
            'type'=>'text',
            'content'=>'默认内容'
        );

        switch($type) {
            case Wechat::MSGTYPE_EVENT:#事件处理

                $evt = $this->wxsdk->getRevEvent();
                if($evt){



                    $key = $evt['key'];
                    switch ($evt['event']){
                        case Wechat::EVENT_SUBSCRIBE:#关注
                            $qr_id = $this->wxsdk->getRevSceneId();

                            break;
                        case Wechat::EVENT_UNSUBSCRIBE:#取消关注

                            break;
                        case Wechat::EVENT_LOCATION:#上报地理位置

                            break;

                        case Wechat::EVENT_SCAN:#扫描带参数二维码
                            $qr_id = $key;
                            break;

                        case Wechat::EVENT_MENU_VIEW :#菜单 - 点击菜单跳转链接

                            break;
                        case Wechat::EVENT_MENU_CLICK :#菜单 - 点击菜单拉取消息

                            break;
                        case Wechat::EVENT_MENU_SCAN_PUSH :#菜单 - 扫码推事件(客户端跳URL)

                            break;
                        case Wechat::EVENT_MENU_SCAN_WAITMSG :#菜单 - 扫码推事件(客户端不跳URL)

                            break;
                        case Wechat::EVENT_MENU_PIC_SYS :#菜单 - 弹出系统拍照发图

                            break;
                        case Wechat::EVENT_MENU_PIC_PHOTO :#菜单 - 弹出拍照或者相册发图

                            break;
                        case Wechat::EVENT_MENU_PIC_WEIXIN :#菜单 - 弹出微信相册发图器

                            break;
                        case Wechat::EVENT_MENU_LOCATION :#菜单 - 弹出地理位置选择器

                            break;


                    }#end switch
                }#end if

                break;

            case Wechat::MSGTYPE_TEXT:

                $txt = $this->wxsdk->getRevContent();


                switch ($txt){
                    case '图文':
                        $_res['type']='news';
                        $_res['content'] = array(
                            array(
                                'Title'=>'百度',
                                'Description'=>'全球最大的中文搜索引擎、最大的中文网站。1999年底,身在美国硅谷的李彦宏看到了中国互联网及中文搜索引擎服务的巨大发展潜力，抱着技术改变世界的梦想，他毅然辞掉硅谷的高薪工作，携搜索引擎专利技术，于 2000年1月1日在中关村创建了百度公司。',
                                'PicUrl'=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=783d11acbf315c6043956ce9b58aac2e/1c950a7b02087bf49212ea50f1d3572c10dfcf89.jpg',
                                'Url'=>'https://www.baidu.com/',
                            )
                        );
                        break;
                    case '多图文':#最多八条?
                        $_res['type']='news';
                        $_res['content'] = array(
                            array(
                                'Title'=>'百度',
                                'Description'=>'',
                                'PicUrl'=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=783d11acbf315c6043956ce9b58aac2e/1c950a7b02087bf49212ea50f1d3572c10dfcf89.jpg',
                                'Url'=>'https://www.baidu.com/',
                            ),
                            array(
                                'Title'=>'百度',
                                'Description'=>'',
                                'PicUrl'=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=783d11acbf315c6043956ce9b58aac2e/1c950a7b02087bf49212ea50f1d3572c10dfcf89.jpg',
                                'Url'=>'https://www.baidu.com/',
                            ),
                            array(
                                'Title'=>'百度',
                                'Description'=>'',
                                'PicUrl'=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=783d11acbf315c6043956ce9b58aac2e/1c950a7b02087bf49212ea50f1d3572c10dfcf89.jpg',
                                'Url'=>'https://www.baidu.com/',
                            ),
                            array(
                                'Title'=>'百度',
                                'Description'=>'',
                                'PicUrl'=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=783d11acbf315c6043956ce9b58aac2e/1c950a7b02087bf49212ea50f1d3572c10dfcf89.jpg',
                                'Url'=>'https://www.baidu.com/',
                            ),

                        );

                        break;

                    case 'zd':

                        $_res['content']="被动消息\n\n\n\t\t<a href=\"http://www.baidu.com\">百度</a>\n\n\n<span style=\"color: red\">红色</span>";

                        $msg = array(
                            "touser" => $this->openid,
                            "msgtype" => "text",
                            "text" => array(
                                "content" => "主动消息"
                            )
                        );
                        #客服接口-发消息
                        $this->wxsdk->sendCustomMessage($msg);

                        break;
                    case 'at':
                        $_res['content']= $this->wxsdk->checkAuth();

                        break;
                    case 'tpl':#模板消息
                        $url = 'http://www.baidu.com';
                        if($this->appid=='wxeaaafd6f56c4bf6e'){
                            $template_id='99jRbQ_k3UXkXclL5rZEZIQWZtBG4REet5ShGKsHcv8';
                            /*
                                                    {{result.DATA}}\n\n领奖金额:{{withdrawMoney.DATA}}\n领奖 时间:{{withdrawTime.DATA}}\n银行信息:{{cardInfo.DATA}}\n到账时间: {{arrivedTime.DATA} }\n{{remark.DATA}}
                                                    */
                            $data = array(
                                'result'=>          array('value'=>'恭喜您中奖！'     ,"color"=>"#173177"),
                                'withdrawMoney'=>   array('value'=>'8888.00'        ,"color"=>"#FF0000"),
                                'withdrawTime'=>    array('value'=>date('Y-m-d H:i:s')),
                                'cardInfo'=>        array('value'=>'中国银行尾号4567' ,"color"=>"#FF0000"),
                                'arrivedTime'=>     array('value'=>date('Y-m-d H:i:s')),
                                'remark'=>          array('value'=>"请您有时间，携带有效证件到我行任意营业厅领取奖品！点击详情查看更多信息"),
                            );
                        }else{
                            $template_id='tMjAE5zoYdlqdbREXqH6IrEpxvhmehEbu7OJBV8BNAk';

                            /*
                            {{first.DATA}}
提交时间：{{keyword1.DATA}}
预约类型：{{keyword2.DATA}}
{{remark.DATA}}
新预约提醒
                            您有一个新的预约待处理
提交时间:2014-07-23 15：30
预约类型:现场咨询
咨询地址：广州海珠区新港东路202号5楼医生咨询室
                            */
                            $data = array(
                                'first'=>          array('value'=>'您有一个新的预约待处理'     ,"color"=>"#173177"),
                                'keyword1'=>    array('value'=>date('Y-m-d H:i:s')),
                                'keyword2'=>   array('value'=>'现场咨询'        ,"color"=>"#FF0000"),
                                'remark'=>          array('value'=>"咨询地址：广州海珠区新港东路202号5楼医生咨询室"),
                            );
                        }

                        $tpl_data = array(
                            'touser'=>$this->openid,
                            'template_id'=>$template_id,
                            'url'=>$url,
                            'data'=>$data
                        );


                        $rc = $this->wxsdk->sendTemplateMessage($tpl_data);
                        if(!$rc){
                            $_res['content']='appid:'.$this->appid.',ERR:'.$this->wxsdk->errMsg.','.json_encode($tpl_data);
                            break;
                        }

                        exit('success');
                        break;

                    case 'link':
                        $_res['content']="\t\t点击下面的链接：\n".'
                        



<a href="http://www.baidu.com" >百度</a>



';

                        break;


                    default:
                        #可以调用数据库

                        $_res['content'] = '关键词：'.$txt;

                }
                break;

            case Wechat::MSGTYPE_IMAGE:#图片
                break;

             case Wechat::MSGTYPE_LOCATION:#坐标
                break;

             case Wechat::MSGTYPE_LINK:#链接
                break;

             case Wechat::MSGTYPE_MUSIC:#音乐
                break;

             case Wechat::MSGTYPE_NEWS:#图文
                break;

             case Wechat::MSGTYPE_VOICE:#音频
                break;

             case Wechat::MSGTYPE_VIDEO:#视频
                break;

             case Wechat::MSGTYPE_SHORTVIDEO:#小视频消息
                break;

            default:
                $_res['content']="未知类型:".$type;
        }#end switch


        if($qr_id>0){



        }

        #开始回复
        switch ($_res['type']){
            case 'text':
                $this->wxsdk->text($_res['content'])->reply();

                break;
            case 'news':
                $this->wxsdk->news($_res['content'])->reply();
                break;
            default:
                $this->wxsdk->text('你好')->reply();

        }


    }#end function

    /**
     * 微信openid获取接口
     * Author: wsl
     */
    public function openid()
    {
        $id = I('get.wx_cache_id');
        if(!$id)exit;
        $qr = M('His_wxopenid_cache')->where("id='$id'")->find();
        if(!$qr)exit('id invalid');

        if(is_numeric($qr['appid'])){
            $wh = "userid='$qr[appid]'";
            $mp = M('His_wxmp')->where($wh)->find();
            if(!$mp)exit('appid invalid');
            $appid=$mp['appid'];
        }else{
            $appid = $qr['appid'];
        }

        $redirect_uri = urlencode(C('MAIN_SERVER_DOMAIN').'Wx/back');

        $id.='@'.$appid;

        #跳转到微信，准备获取openid
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=$id#wechat_redirect";

        header("Location:$url");exit;
    }

    /**
     * 微信上获取openid
     * Author: wsl
     */
    public function back(){

        $state = I('get.state');

        $arr = explode('@',$state);
        $id = $arr[0];
        $appid = $arr[1];

        if(!$id)exit;
        $qr = M('His_wxopenid_cache')->where("id='$id'")->find();
        if(!$qr)exit('id invalid');

        #测试公众号，二维访问，code失效
        if($qr['openid'])goto end;

        if(!$appid)exit('appid not found');
        $wh = is_numeric($appid)?"userid='$appid'":"appid='$appid'";
        $mp = M('His_wxmp')->where($wh)->find();
        if(!$mp)exit('appid invalid');


        $wx = new Wechat($mp);

        $r = $wx->getOauthAccessToken();
        if(!isset($r['openid'])){
            echo $wx->errMsg;
            exit;
        }

        M('His_wxopenid_cache')->where("id='$id'")->save(array('openid'=>$r['openid']));


        end:
        $url= $qr['url'].(strpos($qr['url'],'?')===false ?'?':'&')."wx_cache_id=$id";

        header("Location:$url");exit;
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