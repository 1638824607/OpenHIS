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
use Common\Model\WxmpModel;
use Common\Model\WxopenidModel;
use Common\Model\WxqrloginModel;
use Common\Model\HismemberModel;
use Org\Wx\Wechat;

/**
 * 登录 注销 微信绑定 微信登录
 * LoginController
 * Author: wsl
 */
class LoginController extends PublicBaseController
{

    protected $hospital_info;
    private $tab_pre;

    public function _initialize(){
        $this->tab_pre = C('DB_PREFIX');
    }


    /**
     * 显示登录窗口
     * Author: wsl
     */
    public function index()
    {

        #已登录
        if(session('user_info')){
            $this->redirect('/');
            exit;
        }

        C('title', '登录');

        $this->init_conf();

        #如果设置了微信，那么，可以用二维码登录
        if ($this->hospital_info['appid']) {
            $qr_id = session('qr_id');

            if (!$qr_id) {
                $wxmp = new WxmpModel();
                $qr_id = $wxmp->getLoginQr();
            } else {
                $logger = new WxqrloginModel();
                $qr = $logger->getOne($qr_id);
                if (!$qr || $qr['status'] != 0) {
                    $qr_id = $logger->addqrlog();
                }
            }

            session('qr_id', $qr_id);

            $enid = $this->encrypt($qr_id . '@@' . $this->hospital_info['uid']);

            $url = _URL_ . 'login/go?id=' . $enid;

            $this->assign('qr_img', '/qr?id=' . $enid);
            $this->assign('enid', $enid);
            $this->assign('qr_img_content', $url);
        }
        C('TOKEN_ON',true);

        $this->display();
    }

    /**
     * 初始化微信配置
     * Author: wsl
     */
    private function init_conf()
    {
        $this->hospital_info = session('hospital_info');
        if (!$this->hospital_info) $this->hospital_info['uid'] = C('DEFAULT_HOSPITAL_ID');

        isset($_GET['hid']) || $_GET['hid'] = $this->hospital_info['uid'];

        #保存医院id
        if (isset($_GET['hid']) && intval($_GET['hid']) > 0) {
            #判断是否有效
            $db = M();
            $user = $db->query("SELECT b.userid AS uid,b.* FROM ".$this->tab_pre."his_wxmp b  WHERE b.userid=" . intval($_GET['hid']));

            if (count($user) > 0) {
                $user = $user[0];
            } else {
                exit('1医院标识无效，请正确输入网址:' . $_GET['hid']);
            }
            if ($user['status'] == 2) $this->error('已禁用');

            session('hospital_info', $user);
        } else {
            exit('1无医院标识，请正确输入网址');
        }

        $this->hospital_info = session('hospital_info');
    }

    /**
     * 绑定微信
     * Author: wsl
     */
    public function bindwx()
    {
        $msg = '微信绑定成功';
        $id = I('get.id');

        if (!$id) {
            $msg = 'ERR:id not found';
            goto page_end;
        }

        $id = $this->decrypt($id);

        if (!$id) {
            $msg = 'ERR:id decrypt fail';
            goto page_end;
        }

        $this->init_conf();

        $openid = $this->getOpenid($this->hospital_info['uid']);

        #绑定
        $data = array(
            'appid' => $this->hospital_info['appid'],
            'openid' => $openid,
            'userid' => $id
        );

        $wxopenid = M('His_wxopenid');

        $rs = $wxopenid->where("openid='$openid'")->find();
        if (!$rs) {
            $add = M('His_wxopenid')->add($data);

            if (!$add) {
                $msg = '保存失败';
                goto page_end;
            }
        } else {
            if ($rs['userid'] != $id) {
                $msg = 'ERR：已绑定：' . $rs['userid'];
                goto page_end;
            }
        }

        page_end:

        $hid = $this->hospital_info['uid'];

        #配置信息
        $this->conf = M('His_wxmp')->where("userid='$hid'")->find();
        if(!$this->conf)exit('获取配置信息出错:'.$this->hid);

        $wxmp = new Wechat($this->conf);

        #jsapi 自动关闭页面
        $rc = $wxmp->getJsSign();
        if(!$rc)$this->error('Js Api Error');
        $rc['debug'] = false;
        $rc['jsApiList'] = array('checkJsApi','closeWindow');

        $this->assign('arr_js', json_encode($rc));
        $this->assign('msg', $msg);
        $this->display();
    }

    /**
     * 微信上扫码入口
     * Author: wsl
     */
    public function go()
    {
        $msg = '登录成功';

        $id = I('get.id');

        if (!$id){

            $msg = 'ERR:id not found';
            goto page_end;
        }

        $id = $this->decrypt($id);

        if (!$id){

            $msg = 'ERR:id decrypt fail';
            goto page_end;
        }
        $arr = explode('@@', $id);

        $id = $arr[0];

        if (!$this->hospital_info) {
            $db = M();
            $user = $db->query("SELECT b.userid AS uid,b.* FROM ".$this->tab_pre."his_wxmp b  WHERE " . (is_numeric($arr[1]) ? " b.userid=" . $arr[1] : " b.appid='$arr[1]'"));

            if (count($user) > 0) {
                $user = $user[0];
            } else {
                $msg = '医院标识无效，请正确输入网址';
                goto page_end;
            }
            session('hospital_info', $user);
            $this->hospital_info = $user;
        }

        $wxqr = new WxqrloginModel();

        $qr = $wxqr->getOne($id);
        if (!$qr){
            $msg = 'ERR:id invalid';
            goto page_end;
        }

        if ($qr['status'] != 0){
            $msg = 'ERR:qr status invalid';
            goto page_end;
        }#状态不对也不可以扫

        $openid = $this->getOpenid($this->hospital_info['uid']);


        $wxqr = new WxqrloginModel();

        $wxconn = new WxopenidModel();
        $conn = $wxconn->getOne($openid);
        if (!$conn) {
            $msg = '此微信未绑定';
            goto page_end;
        }

        #更新数据库的enuid
        $wxqr->updateOne(array('enuid' => $this->encrypt($conn['userid']), 'status' => 2), $id);

        page_end:

        $hid = $this->hospital_info['uid'];

        #配置信息
        $this->conf = M('His_wxmp')->where("userid='$hid'")->find();
        if(!$this->conf)exit('获取配置信息出错:'.$this->hid);

        $wxmp = new Wechat($this->conf);

        #jsapi 自动关闭页面
        $rc = $wxmp->getJsSign();
        if(!$rc)$this->error('Js Api Error');
        $rc['debug'] = false;
        $rc['jsApiList'] = array('checkJsApi','closeWindow');

        $this->assign('arr_js', json_encode($rc));

        $this->assign('msg', $msg);
        $this->display();
    }

    /**
     * PC上ajax 判断二维码扫描状态
     * Author: wsl
     */
    public function check_scan()
    {
        $enid = I('post.enid');
        if (!$enid) $this->resJSON(1, 'enid 不能为空');
        $id = $this->decrypt($enid);
        if (!$id) $this->resJSON(2, 'enid解析错误');

        $wxqr = new WxqrloginModel();
        $qr = $wxqr->getOne($id);

        if (!$qr) $this->resJSON(3, 'enid 无效');

        $this->resJSON(0, 'ok', array('status' => $qr['status'], 'enuid' => $qr['enuid'], 'enid' => $enid));
    }

    /**
     * 统一登录入口，用加密的用户id登录
     * Author: wsl
     */
    public function enlogin()
    {
        #删除二维码登录数据
        $enid = I('get.enid');
        $id = 0;
        if ($enid) $id = $this->decrypt($enid);
        if ($id) {
            $wxqr = new WxqrloginModel();
            $wxqr->delOne($id);
            session('qr_id', null);
        }

        $enuid = I('get.enuid');
        if (!$enuid) exit('enuid not found');

        $uid = $this->decrypt($enuid);
        if (!$uid) exit('enuid parse err');

        $d_member = new HismemberModel();
        $user = $d_member->getOne($uid);
        if (!$user) exit('enuid invalid');

        if ($user['status'] == 2) exit('用户已禁用');

        if($user['p_id']!=$this->hospital_info['uid']){
            $hid =$user['p_id']==0? $user['uid']:$user['p_id'];
            #更新sess
            $user2 = M()->query("SELECT b.userid AS uid,b.* FROM ".$this->tab_pre."his_wxmp b  WHERE b.userid=" . $hid);
            if (count($user2) > 0) {
                $user2 = $user2[0];
            } else {
                exit('1医院标识无效，请正确输入网址:' . $_GET['hid']);
            }
            session('hospital_info', $user2);
        }

        $user_relate = $d_member->getUserInfo($uid);//获得用户的更多信息
        session('user_relate', $user_relate);
        session('user_info', $user);

        $this->redirect('/');
    }

    /**
     * 用户使用用户名和密码登录
     * Author: wsl
     */
    public function userlogin()
    {
        $u = I('post.u');
        $p = I('post.p');
        $verify_code = I('post.verify_code');


        $verify = new \Think\Verify();
        if(!$verify->check($verify_code))$this->resJSON(5, '验证码错误！');

        $User = M('HisMember');
        if (!$User->autoCheckToken($_POST))$this->resJSON(6,'表单安全验证失败');


        if (!$u || !$p) $this->resJSON(1, '参数缺失！');

        $d_member = new HismemberModel();
        $user = $d_member->getOne($u, 'user_name');
        if (!$user) $this->resJSON(2, '用户不存在！');
        if (!decrypt_password($p, $user['password'])) {
            $this->resJSON(4, '用户密码错误');
        }
        $this->resJSON(0, 'ok', array('enuid' => $this->encrypt($user['uid'])));
    }

    /**
     * 退出登录，注销登录
     * Author: wsl
     */
    public function logout()
    {
        #保存医院信息
        $hospital_info = session('hospital_info');
        session_unset();
        session_destroy();
        $this->redirect('/Login/index/hid/' . $hospital_info['uid']);
    }
    /**
     * 生成验证码
     * Author: gmq
     */
    public function createVerify(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }

}
