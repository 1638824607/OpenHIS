<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------
namespace Common\Model;

#微信二维码登录基础表
class HismemberModel extends BaseModel
{
    protected $tableName = 'his_member';

    #获取一条数据
    public function getOne($id,$f='uid')
    {
        if(empty($id))return '';
            $where = array(
                $f => $id
            );
        return $this->where($where)->find();
    }

    public function delOne($id,$f='id'){
        return $this->where(array($f=>$id))->delete();
    }

    public function updateOne($data,$id,$f='id'){
        $where = array(
            $f => $id,
        );
        return $this->where($where)->save($data);
    }
    /**
     * 登录成功后获得用户信息保存session
     * gmq
     */
    public function getUserInfo($uid){
        $user = $this->find($uid);
        if($user['p_id']==0){//诊所登录
            $user_relate = M('HisHospital')->where('hid='."'".$uid."'")->find();
            $user_relate['hospital_name'] = $user_relate['hospital_name'];
        }else{//医生登录
            $user_relate = M('HisDoctor')->where('uid='."'".$uid."'")->find();
            $user_relate['hospital_name'] = M('HisHospital')->where('hid='."'".$user['p_id']."'")->getField('hospital_name');

        }
        return $user_relate;
    }
}