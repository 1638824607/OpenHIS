<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy && doreen
// +----------------------------------------------------------------------

namespace His\Model;
use Common\Model\BaseModel;

/**
 * 患者相关操作
 * PatientModel
 * Author: zcy && doreen
 */
class PatientModel extends BaseModel {

    //自动验证
    protected $_validate=array(
        array('name', 'require', '姓名不能为空', self::EXISTS_VALIDATE),
        array('mobile', 'require', '手机号码不能为空！', self::EXISTS_VALIDATE),
        array('emergency_contact_mobile', '/^((\(\d{2,3}\))|(\d{3}\-))?((13\d{9})|(15\d{9})|(18\d{9}))$/', '手机号码格式不正确！', self::EXISTS_VALIDATE),
        //array('name', '/^[\x{4e00}-\x{9fa5}]+$/u', '姓名只能是汉字！', self::EXISTS_VALIDATE),
        array('emergency_contact_name', '/^[\x{4e00}-\x{9fa5}]+$/u', '姓名只能是汉字！', self::EXISTS_VALIDATE),
    );

    //自动完成
    protected $_auto=array(
        array('create_time','time',1,'function'),
    );

    /**
     * @param array $condition 条件
     * @Name     get_the_number_of_patient
     * @explain  根据条件获取患者表的数量
     * @author   zuochuanye
     * @Date        2017/10/25
     * @return bool
     */
    public function get_the_number_of_patient($condition=[]){
        $count = $this
                ->where($condition)
                ->count('patient_id');
        return $count ? $count : false;
    }

    /**
     * @param array $condition  条件
     * @param string $field      查找字段
     * @Name     get_the_patient_info_of_patient
     * @explain    获取patient信息
     * @author   zuochuanye
     * @Date    2017/10/25
     * @return bool|mixed
     */
    public function get_the_patient_info_of_patient($condition=[],$field=''){
        $info = $this->field($field)->where($condition)->find();
        return $info ? $info : false;
    }

    /**
     * @param    $data 需要插入的数据
     * @Name     patient_add
     * @explain  添加患者
     * @author   zuochuanye
     * @Date    2017/10/25
     * @return bool|mixed
     */
    public function patient_add($data){
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        if ( !$data = $this->create($data) ) {
            return false;
        }else{
            $patient_id = $this->add($data);
            if($patient_id){
                $this->commit();
                return $patient_id;
            }else{
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * 患者列表
     * @param int $hid
     * @param array $search
     * @param string $field
     * @param int $isDel
     * @return array
     * Author: doreen
     */
    public function getPatientLists($hid = 0, $search = array(), $field='*', $isDel = 0)
    {
        $where = array(
            'hospital_id' => $hid,
            'is_del' => $isDel,
        );
        $where = array_merge($where, $search);
        $count = $this->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $result =  $this
            ->where($where)
            ->order("create_time desc,update_time desc")
            ->limit($pager->firstRow.','.$pager->listRows)
            ->field($field)
            ->select();
        return array('page' => $pager->getPage() , 'list' => $result, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     * 根据patient_id取出档案信息
     * @param int $pid
     * @return bool|mixed
     * Author: doreen
     */
    public function findPatientFileInfoById($pid = 0)
    {
        if ($pid) {
            $file = M('his_patient_file');
            $fileInfo = $file->where('patient_id = %d', array('patient_id' => $pid))->find();
            return $fileInfo ? $fileInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 修改患者信息
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function editPatient($map = [], $data = [])
    {
        $this->startTrans();
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        // 对data数据进行验证
        if (!$data = $this->create($data)) {
            //验证不通过返回错误
            return false;
        } else {
            if($patient = $this->where($map)->find()){
                $result = $this->where($map)->save($data);
                if($result){
                    $this->commit();
                    return true;
                } else {
                    $this->rollback();
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 添加患者档案
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addPatientInfo($data = [])
    {
        $this->startTrans();
        $file = M('his_patient_file');
        $insertId = $file->add($data);
        if($insertId){
            $this->commit();
            return $insertId;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * 修改档案信息
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function editPatientInfo($map = [], $data = [])
    {
        $this->startTrans();
        $file = M('his_patient_file');
        if($fileInfo = $file->where($map)->find()){
            $result = $file->where($map)->save($data);
            if($result){
                $this->commit();
                return true;
            } else {
                $this->rollback();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取病例信息列表
     * @param $patientId
     * @param $hospitalId
     * @return mixed
     * Author: doreen
     */
    public function getCareHistoryLists($patientId, $hospitalId)
    {
        $where = array(
            'c.hospital_id' => $hospitalId,
            'c.patient_id' => $patientId,
        );
        $join = "LEFT JOIN __HIS_HOSPITAL__ h ON h.hid = c.hospital_id LEFT JOIN __HIS_DOCTOR__ d ON d.uid = c.doctor_id LEFT JOIN
                __HIS_DEPARTMENT__ de ON de.did = c.department_id LEFT JOIN __HIS_PATIENT_FILE__ p ON p.patient_id = c.patient_id";
        $field = "h.hospital_name, h.owner_name, d.true_name, de.department_name, p.family_info, c.*";
        $careHistory = M('his_care_history');
        $result = $careHistory
            ->alias('c')
            ->join($join)
            ->where($where)
            ->order("addtime desc")
            ->field($field)
            ->select();
        return $result;
    }

    /**
     * 通过id获取病例信息
     * @param int $id
     * @return bool
     * Author: doreen
     */
    public function findCareHistoryById($id = 0)
    {
        if ($id) {
            $careHistory = M('his_care_history');
            $join = "LEFT JOIN __HIS_PATIENT_FILE__ f ON f.patient_id = c.patient_id";
            $field = "c.*, f.family_info";
            $careHistoryInfo = $careHistory
                    ->alias('c')
                    ->join($join)
                    ->where('c.id = %d', array('c.id' => $id))
                    ->field($field)
                    ->find();
            return $careHistoryInfo ? $careHistoryInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 获取病例下处方信息列表
     * @param int $chid
     * @return mixed
     * Author: doreen
     */
    public function getCareOrderLists($chid = 0)
    {
        $where = array(
            'care_history_id' => $chid,
        );
        $careOrder = M('his_care_order');
        $result = $careOrder
            ->where($where)
            ->order("addtime desc")
            ->select();
        return $result;
    }

    /**
     * 获取病例下处方药品列表
     * @param int $fid
     * @return mixed
     * Author: doreen
     */
    public function getCareOrderSubLists($fid = 0)
    {
        $where = array(
            'fid' => $fid,
        );
        $careOrderSub = M('his_care_order_sub');
        $result = $careOrderSub->where($where)->select();
        return $result;
    }

    /**
     * 获取所有病例和处方
     * @param $patientId
     * @param $hospitalId
     * @return mixed
     * Author: doreen
     */
    public function getCareAll($patientId, $hospitalId)
    {
        $where = array(
            'h.patient_id' => array('in', $patientId),
            'h.hospital_id'=> $hospitalId,
        );
        $careOrder = M('his_care_history');
        $join = "LEFT JOIN __HIS_CARE_ORDER__ o ON o.care_history_id = h.id";
        $field = "h.id as history_id, o.id as order_id";
        $result = $careOrder
            ->alias('h')
            ->join($join)
            ->where($where)
            ->field($field)
            ->select();
        return $result;
    }

    /**
     * 删除患者
     * @param array $pid
     * @param array $historyMap
     * @param array $orderMap
     * @param array $orderSubMap
     * @return bool
     * Author: doreen
     */
    public function deletePatient($pid = [], $historyMap = [], $orderMap = [], $orderSubMap = [])
    {
        $this->startTrans();
        $file = M('his_patient_file');
        $careOrderSub = M('his_care_order_sub');
        $careHistory = M('his_care_history');
        $careOrder = M('his_care_order');
        $where = [
            'patient_id' => array('in', $pid),
        ];
        if ($this->where($where)->find()) {
            $deletePatient = $this->where($where)->delete();
            if ($file->where($where)->find()) {
                $deleteFile = $file->where($where)->delete();  //删除档案
                if ($careHistory->where($historyMap)->select()) { //删除病例
                    $deleteCareHistory = $careHistory->where($historyMap)->delete();
                } else {
                    $deleteCareHistory = true;
                }
                if ($careOrder->where($orderMap)->select()) {
                    $deleteCareOrder = $careOrder->where($orderMap)->delete(); //删除处方
                } else {
                    $deleteCareOrder = true;
                }
                if ($careOrderSub->where($orderSubMap)->select()) { //删除处方明细
                    $deleteCareOrderSub = $careOrderSub->where($orderSubMap)->delete();
                } else {
                    $deleteCareOrderSub = true;
                }
            } else {
                $deleteFile = true;
                $deleteCareHistory = true;
                $deleteCareOrder = true;
                $deleteCareOrderSub = true;
            }
            if ($deletePatient && $deleteFile && $deleteCareHistory && $deleteCareOrder && $deleteCareOrderSub) {
                $this->commit();
                return true;
            } else {
                $this->rollback();
                return false;
            }
        } else {
            return false;
        }
    }

}