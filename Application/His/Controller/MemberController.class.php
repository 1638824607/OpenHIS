<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq * doreen
// +----------------------------------------------------------------------
namespace His\Controller;

/**
 *用户相关操作
 * MemberController
 * Author: gmq && doreen
 */
class MemberController extends HisBaseController
{
    protected $member_model;

    public function _initialize()
    {
        parent::_initialize();
        $member_model = D('HisMember');
        $this->member_model = $member_model;
    }

    /**
     * 我的诊所信息
     * Author: doreen
     */
    public function myHospitalInfo()
    {
        if (IS_AJAX) { //ajax提交保存修改信息
            $data = I();
            $condition = [
                'h.hid' => $this->hospitalInfo['uid'],
            ];
            $hospital = M('his_hospital');
            $hospitalInfo = $this->member_model->getMyHospitalInfo($condition);
            if ($hospitalInfo) {
                $map['hid'] = $this->hospitalInfo['uid'];
                $res = $this->member_model->updateHospital($map,$data);
                if ($res) {
                    $this->ajaxSuccess('修改成功');
                } elseif ($hospital->getError()) {
                    $this->ajaxError($hospital->getError());
                } else {
                    $this->ajaxError('修改失败');
                }
            } else {
                $this->ajaxError('该诊所不存在');
            }
        } else { //我的诊所页面显示
            $hid = $this->hospitalInfo['uid'];
            $condition = [
                'h.hid' => $hid,
            ];
            $hospitalInfo = $this->member_model->getMyHospitalInfo($condition); //所属诊所信息
            $doctorCount = $this->member_model->doctorCount($hid); //所属诊所医生数量
            $currentDepartment = D('his_department')->currentDepartment($hid); //所属当前诊所的科室
            $this->assign('hospitalInfo', $hospitalInfo);
            $this->assign('doctorCount', $doctorCount);
            $this->assign('currentDepartment', $currentDepartment);
            $this->display();
        }
    }

    /**
     * 添加用户（医生，护士，..）
     * Author: gmq
     */
    public function addUser()
    {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->member_model->addUser($data);
            if ($this->member_model->getError()) {
                $this->ajaxError($this->member_model->getError());
            }
            $res ? $this->ajaxSuccess('添加成功') : $this->ajaxError('添加失败');
        }

    }

    /**
     * 重置密码
     * Author: gmq
     */
    public function resetPassword(){
        $uid = I('post.uid','');
        if($uid){
            $wh = ['uid'=>$uid];
            $users = M('HisMember')->where($wh)->find();
            $data['password'] = encrypt_password(substr($users['user_name'],5));
            $r = M('HisMember')->where($wh)->save($data);

            if($r!==false){
                $this->ajaxSuccess('重置成功');
            }else{
                $this->ajaxError('重置失败');
            }


        }
    }

    /**
     * 修改（医生，护士，..）信息
     * Author: gmq
     */
    public function editUser(){
        $uid = I('get.uid','0','intval');//用户id
        if(IS_POST){
            $uid = I('post.uid','0','intval');
            $data = I('post.');
            $r = $this->member_model->saveUserRelate($uid,$data);
            if($r){
                $this->ajaxSuccess('修改成功');
            }else{
                $this->ajaxError('提交失败');
            }
        }
        $userInfo = $this->member_model->getUserInfo($uid);
        if($userInfo){
             $this->ajaxReturn($userInfo);
        }else{
            $this->error('此用户不存在');
        }

    }

    /**
     * 医生列表管理
     * Author: gmq
     */
    public function userList(){
        $uid = $this->userInfo['uid'];
        $p_id = $this->userInfo['p_id'];
        $type = $this->userInfo['type'];
        $searchContent['true_name'] = I('post.search','','htmlspecialchars');
        $search = [];
        if(!empty($searchContent['true_name'])) {
            $search['true_name'] = array('like','%'.$searchContent['true_name'].'%');
        }
        if($p_id==0){//诊所
            $uid = $uid;
        }
        if($p_id !=0 && $type==1){//拥有管理权限的医生
            $uid = $p_id;
        }
        if(IS_AJAX){//分页使用
            $action  = I('post.action','');
            if($action=='userList'){
                $list = $this->member_model->getUserList($uid,1,$search);
                $this->ajaxSuccess($list);
            }
            if($action=="roleList"){
                $admin_auth_group_model = D('HisAuthGroup');
                $search = array();
                $role = $admin_auth_group_model->getGroupList($search);
                $this->ajaxSuccess($role);
            }
        }
        $remove_count = $this->member_model->getRemoveCount($uid);//获得禁用的人数
        $departmentList = D('HisDepartment')->getDepartmentList($uid);//获得当前诊所的部门信息
        $getRankList = $this->member_model->getRankTitle();//获得级别的列表
        $this->assign('departmentList',$departmentList['list']);//获的部门列表
        $this->assign('rankList',$getRankList);//获得级别列表
        $this->assign('remove_count',$remove_count);
        //职务管理页面数据
        $admin_auth_group_model = D('HisAuthGroup');
        $search = array();
        $role = $admin_auth_group_model->getGroupList($search);
        $this->assign('roleList',$role);
        $this->display();
    }

    /**
     * 禁用医生列表
     * Author: gmq
     */
    public function RemoveUserList(){
        $uid = $this->userInfo['uid'];
        $p_id = $this->userInfo['p_id'];
        $type = $this->userInfo['type'];
        if($p_id==0){//诊所
            $uid = $uid;
        }
        if($p_id !=0 && $type==1){//拥有管理权限的医生
            $uid = $p_id;
        }
        $searchContent['true_name'] = I('post.search','','htmlspecialchars');
        $search = [];
        if(!empty($searchContent['true_name'])) {
            $search['true_name'] = array('like','%'.$searchContent['true_name'].'%');
        }
        $list = $this->member_model->getUserList($uid,2,$search);
        $this->ajaxReturn($list);
    }

    /**
     * 禁用医生列表
     * Author: gmq
     */
    public function removeUser(){
        $uid = I('post.uid',"",'intval');
        if($uid==$this->userInfo['uid']){
            $this->ajaxError('自己不能移除自己');
        }
        $r = $this->member_model->removeUser($uid);
        if(!$r){
            $this->ajaxError('移除失败');
        }
        $this->ajaxSuccess('移除成功');
    }

    /**
     * 取消禁用
     * Author: gmq
     */
    public function startUser(){
        $uid = I('post.uid',"",'intval');
        $r = $this->member_model->startUser($uid);
        if(!$r){
            $this->ajaxError('取消禁用失败');
        }
        $this->ajaxSuccess('取消禁用成功');
    }

    /**
     * 用户修改图片
     * Author: gmq
     */
    public function uploadDocPic(){
        $add_arr = array();
        $upload_data = post_upload(C('UPLOAD_DOCTOR'), 'imagefile', '', false, true);
        $upload_data['file']['savepath'] = ltrim($upload_data['file']['savepath'],'.');
        die(json_encode($upload_data));
    }
}
?>