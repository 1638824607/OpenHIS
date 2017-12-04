<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq && doreen
// +----------------------------------------------------------------------
namespace His\Model;

use Common\Model\BaseModel;

/**
 *用户操作相关的model
 * HisMemberModel
 * Author: gmq && doreen
 */
class HisMemberModel extends BaseModel
{
    //自动验证
    protected $_validate=array(
        array('true_name', 'require', '姓名不能为空！', self::EXISTS_VALIDATE),
        array('phone', '/^((\(\d{2,3}\))|(\d{3}\-))?((13\d{9})|(15\d{9})|(18\d{9}))$/', '手机号格式不正确', self::EXISTS_VALIDATE),
        array('mailbox', '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', '邮箱格式不正确', self::VALUE_VALIDATE),
        array('hospital_name', 'require', '医院名称不能为空！', self::EXISTS_VALIDATE),

        array('user_name','','用户已经存在！',self::EXISTS_VALIDATE,'unique',3),
        array('user_name','require','用户名不能为空！',self::EXISTS_VALIDATE),
        array('password', '6,32', '密码长度6-32位！', self::EXISTS_VALIDATE,'length'),
    );


    /**
     * 个人资料修改
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function updateData($map, $data)
    {
        // 对data数据进行验证
        $doctor = M('his_doctor');
        if( !$doctor->validate($this->_validate)->create($data) ) {
            //验证不通过返回错误
            return false;
        } else {
            if ($user = $doctor->where($map)->find()) {
                $updateData = [
                    'true_name' => $data['true_name'],
                    'picture' => $data['picture'],
                    'sex' => $data['sex'],
                    'background' => $data['background'],
                    'mailbox' => $data['mailbox'],
                    'strong' => $data['strong'] ? $data['strong'] : '',
                    'honor' => $data['honor'] ? $data['honor'] : '',
                    'introduction' => $data['introduction'] ? $data['introduction'] : '',
                    'update_time' => time(),
                ];
                $result = $doctor->where($map)->save($updateData);
                if($result) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 添加个人资料
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addData($data)
    {
        // 对data数据进行验证
        $doctor = M('his_doctor');
        if ( !$doctor->validate($this->_validate)->create($data) ) {
            return false;
        } else {
            //验证通过
            $arr = array(
                'true_name' => $data['true_name'],
                'picture' => $data['picture'],
                'sex' => $data['sex'],
                'age'=>$data['age'],
                'background' => $data['background'],
                'mailbox' => $data['mailbox'],
                'strong' => $data['strong'] ? $data['strong'] : '',
                'honor' => $data['honor'] ? $data['honor'] : '',
                'introduction' => $data['introduction'] ? $data['introduction'] : '',
                'create_time' => time(),
                'uid' => $data['uid'],
            );
            $result = $doctor->add($arr);
            if ($result) {
                return $result;
            } else {
                return false;
            }
        }
    }

    /**
     * 用户信息
     * @param int $uid
     * @return bool
     * Author: doreen
     */
    public function getPersonalInfoById($uid = 0)
    {
        if ($uid) {
            $personalInfo = M('his_doctor')->alias('d')
                ->join("LEFT JOIN __HIS_MEMBER__ m ON m.uid = d.uid")
                ->where('d.uid = %d', array('d.uid' => $uid))->find();
            return $personalInfo ? $personalInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 用户关联医院科室信息
     * @param int $uid
     * @return bool
     * Author: doreen
     */
    public function getRelatedHospitalByUid($uid = 0)
    {
        if ($uid) {
            $join = "LEFT JOIN __HIS_HOSPITAL__ h ON h.hid = r.hospital_id LEFT JOIN __HIS_DEPARTMENT__ d ON d.did = r.department_id";
            $field = "h.hospital_name,d.department_name,r.title_level,r.hospital_id";
            $relatedHospitals = M('his_hospital_doctor_relation')
                ->alias('r')
                ->join($join)
                ->where('r.physicianid = %d', array('r.physicianid' => $uid))
                ->field($field)
                ->order('r.create_time desc')
                ->select();
            return $relatedHospitals ? $relatedHospitals : false;
        } else {
            return false;
        }
    }

    /**
     * 我的诊所信息
     * @param array $condition
     * @param string $field
     * @return bool
     * Author: doreen
     */
    public function getMyHospitalInfo($condition = [], $field = 'h.*')
    {
        $where = array();
        $where = array_merge($where, $condition);
        $hospitalInfo = M('his_hospital')->alias('h')
            ->join("LEFT JOIN __HIS_MEMBER__ m ON m.uid = h.hid")
            ->where($where)
            ->field($field)
            ->find();
        return $hospitalInfo ? $hospitalInfo : false;
    }

    /**
     * 诊所医生数量
     * @param int $hid
     * @return mixed
     * Author: doreen
     */
    public function doctorCount($hid = 0)
    {
        if ($hid) {
            $doctorCount = $this->where('p_id = %d', array('p_id' => $hid))->count();
            return $doctorCount;
        }
    }

    /**
     *  修改我的诊所信息
     * @param $map
     * @param $data
     * @return bool
     * Author: gmq
     */
    public function updateHospital($map, $data)
    {
        // 对data数据进行验证
        $hospital = M('his_hospital');
        if( !$hospital->validate($this->_validate)->create($data) ) {
            //验证不通过返回错误
            return false;
        } else {
            if ($user = $hospital->where($map)->find()) {
                $updateData = [
                    'address' => $data['address'],
                    'major_field' => $data['major_field'],
                    'introduction' => $data['introduction'],
                    'update_time' => time(),
                ];
                $result = $hospital->where($map)->save($updateData);
                if($result) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 添加用户
     * @param $data
     * @return bool
     * Author: gmq
     */
     public function addUser($data){

         if(session('user_info')['p_id']==0){//诊所
             $data['p_id']=session('user_info')['uid'];
         }
         if(session('user_info')['p_id'] !=0){//管理员
             $data['p_id'] = session('user_info')['p_id'];
         }
         $member['user_name'] = $data['user_name'];
         $member['type'] = $data['type'];
         $member['department_id'] = $data['department_id'];
         $member['create_time'] = time();
         $member['rank'] = $data['rank'];
         $member['p_id'] = $data['p_id'];

         $this->startTrans();
         if(!$this->create($member)){
             return false;
         }
         $data['password'] = encrypt_password(substr($data['user_name'],5));//密码为手机号后六位
         $member['password'] = $data['password'];
         $m_res = $this->add($member);//把用戶添加到member表裏；
         $d['uid'] = $m_res;
         $d['group_id'] = $data['type'];
         $gro_res = M('His_auth_group_access')->add($d);//添加用戶時，分配角色
         $his_doc['uid'] = $m_res;
         $his_doc['true_name']  = $data['true_name'];
         $his_doc['create_time'] = time();
         $his_doc['age'] = $data['age'];
         $his_doc_res = M('HisDoctor')->add($his_doc);

        if($m_res && $gro_res && $his_doc_res){
            $this -> commit();
            return true;
        }else{
            $this ->rollback();
            return false;
        }


     }

    /**
     * 获得用户信息
     * @param $uid
     * @return bool
     * Author: gmq
     */
    public function getUserInfo($uid){
        $where=['hm.uid'=>$uid];
        $join = "LEFT JOIN __HIS_DOCTOR__ hd ON hm.uid = hd.uid";
        $result = $this
            ->alias('hm')
            ->join($join)
            ->field()
            ->where($where)
            ->find();
        return $result ? $result : false;
    }

    /**
     * 管理员修改用户信息
     * @param $uid
     * @param $data
     * @return bool
     * Author: gmq
     */
    public function saveUserRelate($uid,$data){
         $this->startTrans();
         $member['department_id'] = $data['department_id'];
         $member['rank'] = $data['rank'];
         $member['type'] = $data['type'];
         $member['update_time'] = time();
         $wh = ['uid'=>$uid];
         $mem_res = $this->where($wh)->save($member);
         $doctor['picture'] = $data['picture'];
         $doctor['strong'] = $data['strong'];//擅长领域
         $doctor['introduction'] = $data['introduction'];//简介
         $doctor['true_name'] = $data['true_name'];
         $doctor['sex'] = $data['sex'];
         $doctor['age'] = $data['age'];
         $doctor['update_time'] = time();
         $doc_res = M('HisDoctor')->where($wh)->save($doctor);
         $role['group_id'] = $data['type'];
         $role_rec = M('HisAuthGroupAccess')->where($wh)->save($role);
         if(($mem_res!==false) && ($doc_res!==false) && ($role_rec!==false)){
            $this->commit();
            return true;
         }else{
             $this->rollback();
             return false;
         }

    }


    /**
     * 用户列表
     * @param $uid
     * @param $status
     * @param array $search
     * @param int $page
     * @param int $pageoffset
     * @param int $pagesize
     * @return array
     * Author: gmq
     */
    public function getUserList($uid,$status,$search=array()){


        $where = array(
            'p_id'=>$uid,
            'hm.status'=>$status
        );

        $where = array_merge($where, $search);
        $join1 = "LEFT JOIN __HIS_DOCTOR__ hd ON hm.uid = hd.uid";
        $join2 = "LEFT JOIN __HIS_DEPARTMENT__ hde ON hm.department_id = hde.did";
        $join3 = "LEFT JOIN __HIS_AUTH_GROUP__ ag ON hm.type = ag.id";
        $count = $this->alias('hm')->join($join1)->join($join2)->join($join3)->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str  = $pager->showHis();//获得页码字符串

        $result = $this
            ->alias('hm')
            ->join($join1)
            ->join($join2)
            ->join($join3)
            ->field('hm.uid,hm.create_time,hm.user_name,hd.true_name,hd.sex,hd.age,hde.department_name,ag.title')
            ->where($where)
            ->order('hm.create_time desc')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' =>$pager->getPage(), 'list' => $result, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     * 禁用用户
     * @param $uid
     * @return bool
     * Author: gmq
     */
    public function removeUser($uid){
        $where = ['uid'=>$uid];
        $data = ['status'=>2];
        $r = $this->where($where)->save($data);
        if(!$r){
            return false;
        }
        return true;
    }

    /**
     * 开启用户
     * @param $uid
     * @return bool
     * Author: gmq
     */
    public function startUser($uid){
        $where = ['uid'=>$uid];
        $data = ['status'=>1];
        $r = $this->where($where)->save($data);
        if(!$r){
            return false;
        }
        return true;
    }

    /**
     * 计算所禁用人数
     * @param $uid
     * @return mixed
     * Author: gmq
     */
    public function getRemoveCount($uid){
        $where = ['p_id'=>$uid,'status'=>2];
        return $this->where($where)->count();
     }

    /**
     * 获得医生级别列表
     * @param string $key
     * @return array|bool|mixed
     * Author: gmq
     */
    public function getRankTitle($key='')
    {
        $array = array(
            0=>'-请选择级别-',
            1=>'主治医师',
            2=>'副主任医师',
            3=>'主任医师',
            4=>'医士',
            5=>'医师',
            6=>'助理医师',
            7=>'实习医师',
            8=>'主管护师',
            9=>'护师',
            10=>'护士',
            11=>'医师助理',
            12=>'研究生',
            13=>'随访员',
            14=>'其他',
        );
        if($key == '')
            return $array;
        elseif(key_exists($key, $array))
            return $array[$key];
        else
            return false;
    }


    /**
     * 我的科室信息
     * @param int $did
     * @return bool|mixed
     * Author: gmq
     */
    public function getMyDepartmentInfo($did = 0)
    {
        if ($did) {
            $departmentInfo = M('his_department')
                ->where('did = %d', array('did' => $did))->find();
            return $departmentInfo ? $departmentInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 获得当前医院医生的信息
     * @param int $hid
     * @param array $map
     * @param string $field
     * @return mixed
     * Author: gmq
     */
    public function getDoctorInfo($hid = 0, $map = [], $field = '*')
    {
        $where = array(
            'm.p_id' => $hid,
        );
        $where = array_merge($where, $map);
        $join = "JOIN __HIS_DOCTOR__ d ON d.uid = m.uid";
        $doctorInfo = M('his_member')
            ->alias('m')
            ->join($join)
            ->where($where)
            ->field($field)
            ->select();
        return $doctorInfo;
    }

    /**
     * 角色查询
     * @param $uid
     * @return string
     * Author: zxy
     */
    public function role_judgement($uid){
        if(!empty($uid)){
            $member_p_id = M('his_member')->field('p_id')->where(array("uid"=>$uid))->find();
            if($member_p_id['p_id'] == 0){
                $name = M("his_hospital")->field('owner_name')->where(array('hid'=>$uid))->find()['owner_name'];
            }else{
                $name = M("his_doctor")->field('true_name')->where(array('uid'=>$uid))->find()['true_name'];
            }
            return $name ? $name : '';
        }else{
            return '';
        }
    }

}