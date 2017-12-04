<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq && doreen
// +----------------------------------------------------------------------
namespace His\Controller;

/**
 * 首页相关操作控制器
 * IndexController
 * Author: gmq && doreen
 */
class IndexController extends HisBaseController
{
    protected $member_model;

    public function _initialize()
    {
        C('TITLE',"明医诊所云");
        C('KEYEORDS',"");
        C('DESCRIPTION',"");
        parent::_initialize();
        $this->member_model = D('HisMember');
    }

    /**
     * 首页
     * Author: doreen
     */
    public function index()
    {
        if (IS_AJAX) {
            //取出当前用户绑定微信信息
            $wxOpenid = D('HisWxopenid');
            $wxUser = $wxOpenid->where('userid = %d', array('userid' => $this->userInfo['uid']))->find();
            $member = D('HisMember');
            //当前登录用户信息
            $personalInfo = $member->where('uid = %d', array('uid' => $this->userInfo['uid']))->find();
            //当前登录用户关联的医院信息
            if ($personalInfo['p_id'] == 0) {
                $condition = [
                    'h.hid' => $this->userInfo['uid'],
                ];
                $hospitalLists = $member->getMyHospitalInfo($condition);
            } else {
                $condition = [
                    'h.hid' => $personalInfo['p_id'],
                ];
                $hospitalLists = $member->getMyHospitalInfo($condition);
            }
            //没有绑定信息提示绑定
            if (!$wxUser) {
                if (!$hospitalLists) {
                    $this->ajaxSuccess('你绑定的医院不存在');
                }
                $id = $this->encrypt($this->userInfo['uid']);
                $data = [
                    'msg' => '没有绑定，请扫描二维码绑定',
                    'url' => '/qr?type=bindwx&id='.$id.'&size=8',
                    'hospitalLists' => $hospitalLists,
                ];
                $this->ajaxError('',$data);
            }
            if ($personalInfo['p_id'] == 0) { //医院账号直接跳转
                $data['url'] = '/index.php/Index/base_index?hospital_id='.$personalInfo['uid'];
                $this->ajaxSuccess('',$data);
            } else { //已绑定显示医院列表页面
                if (!$hospitalLists) {
                    $this->ajaxSuccess('你绑定的医院不存在');
                } else {
                    $this->ajaxSuccess('请求成功', $hospitalLists);
                }
            }
        }
        $this->display();
    }

    /**
     * 诊所首页
     * Author: gmq
     */
    public function base_index(){

        if(!$this->userInfo['uid'])$this->error('登录后访问');
        $menu = $this->getMenuList($this->userInfo['uid']);//获得一级菜单
        $isHospital = 0;
        if($this->userInfo['p_id']==0){//诊所
            $isHospital = 1;
        }
        $this->assign('isHospital',$isHospital);
        $this->assign('menu',$menu);

        $this->display();
    }

    /**
     * 首页个人资料编辑
     * Author: doreen
     */
    public function editPersonal()
    {
        if (IS_POST) { //ajax提交保存修改信息
            $data = I();
            $doctor = M('his_doctor');
            $personalInfo = $this->member_model->getPersonalInfoById($data['uid']);
            $act = I('post.act') ? I('post.act') : I('get.act');
            if ($act == 'add') { //上传头像
                $add_arr = array();
                $upload_data = post_upload(C('UPLOAD_DOCTOR'), 'imagefile', '', false, true);
                $upload_data['file']['savepath'] = ltrim($upload_data['file']['savepath'],'.');
                die( json_encode($upload_data));
            }
            if ($personalInfo) {
                $map['uid'] = $data['uid'];
                $res = $this->member_model->updateData($map,$data);
                if ($res) {
                    $this->ajaxSuccess('修改成功');
                } elseif ($doctor->getError()) {
                    $this->ajaxError($doctor->getError());
                } else {
                    $this->ajaxError('修改失败');
                }
            } else {
                $this->ajaxError('无该医生信息');
            }
        } else { //显示编辑页面
            $personalInfo = $this->member_model->getPersonalInfoById($this->userInfo['uid']); //用户信息
            $personalInfo['rank'] = $personalInfo['rank'] == 0 ? '其他' :$this->member_model->getRankTitle($personalInfo['rank']); //医生级别
            $departmentInfo = [];
            $condition = [
                'h.hid' => $personalInfo['p_id'],
            ];
            $hospitalInfo = $this->member_model->getMyHospitalInfo($condition); //医院信息
            if ($personalInfo['department_id'] != 0) {
                $departmentInfo = $this->member_model->getMyDepartmentInfo($personalInfo['department_id']); //科室信息
            }
            $this->assign('personalInfo', $personalInfo);
            $this->assign('hospitalInfo', $hospitalInfo);
            $this->assign('departmentInfo', $departmentInfo);
            $this->display();
        }
    }

    /**
     * 根据用户权限查询一级菜单
     * @param $uid
     * @return mixed
     * Author: gmq
     */
    public function getMenuList($uid){
        $his_auth_group_access_model = D('HisAuthGroupAccess');
        $menus = $his_auth_group_access_model->getUserRules0($uid);
        return $menus;//根据用户的id查找用户的显示菜单
    }

    /**
     * 根据用户权限查询一级菜单的子权限
     * Author: gmq
     */
    public function getMenuByPid(){
        $pid = I('post.pid','','intval');
        $menus =  D('HisAuthGroupAccess')->getMenuByPid($this->userInfo['uid'],$pid);
        return $this->ajaxReturn($menus);
    }

    /**
     * 用户修改密码
     * Author: gmq
     */
    public function editPassword(){
        $uid = $this->userInfo['uid'];
        if(!$uid){
            $this->ajaxError('请先登录');
        }
        if(IS_AJAX){
            $new_password = I("post.new_password",'');
            $old_password = I("post.old_password",'');
            if($new_password && $old_password){
                $old = M("his_member")->where('uid="'.$uid.'"')->find();

                if(!decrypt_password($old_password,$old['password'])){
                    $this->ajaxError('原密码不正确');
                }
                $data['password'] = encrypt_password($new_password);
                $wh = ['uid'=>$uid];
                $r =  M("his_member")->where($wh)->save($data);
               if($r){
                   $this->ajaxSuccess('修改成功');
               }
               $this->ajaxError('修改失败');

            }
        }
        $this->display();

    }
}
?>