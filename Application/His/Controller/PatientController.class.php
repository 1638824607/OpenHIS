<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: doreen
// +----------------------------------------------------------------------

namespace His\Controller;

/**
 * 患者库管理类
 * PatientController
 * Author: doreen
 */
class PatientController extends HisBaseController
{
    protected $patient_model;

    public function _initialize()
    {
        parent::_initialize();
        $patient_model = D('Patient');
        $this->patient_model = $patient_model;
    }

    /**
     * 患者列表
     * Author: doreen
     */
    public function index()
    {
        $hid = $this->hospitalInfo['uid'];
        //搜索条件
        $search_content['name'] = I('post.name','','htmlspecialchars');
        $search_content['mobile'] = I('post.mobile','','htmlspecialchars');
        if ($search_content) {
            $search['name'] = array('like','%'.$search_content['name'].'%');
            $search['mobile'] = array('like','%'.$search_content['mobile'].'%');
        } else {
            $search = [];
        }
        //获取患者列表
        $patientLists = $this->patient_model->getPatientLists($hid, $search);
        //获取移除人数
        $removedCount = $this->patient_model->getPatientLists($hid, $search, '', 1);
        if (IS_AJAX) {
            $data['patientLists'] = $patientLists;
            $data['search'] = $search_content;
            $data['removedCount'] = $removedCount['count'];
            $this->ajaxSuccess('',$data);
        } else {
            $this->display();
        }
    }

    /**
     * 编辑患者档案
     * Author: doreen
     */
    public function editPatient()
    {
        if (IS_AJAX) { //保存编辑的患者档案
            $patientId = I('post.pid','','intval');
            $height  = !empty(I('post.height')) ? number_format(I('post.height'),1) : I('post.height');
            $weight  = !empty(I('post.weight')) ? number_format(I('post.weight'),1) : I('post.weight');
            $leftVision  = !empty(I('post.left_version')) ? number_format(I('post.left_version'),1) : I('post.left_version');
            $rightVision  = !empty(I('post.right_version')) ? number_format(I('post.right_version'),1) : I('post.right_version');
            if(!empty(I('post.height')) && !is_numeric($height)) $this->ajaxError('身高为整数或小数');
            if(!empty(I('post.weight')) && !is_numeric($weight)) $this->ajaxError('体重为整数或小数');
            if(!empty(I('post.left_version')) && !is_numeric($leftVision)) $this->ajaxError('左眼视力为整数或小数');
            if(!empty(I('post.right_version')) && !is_numeric($rightVision)) $this->ajaxError('右眼视力为整数或小数');
            $bloodType = json_encode(array(I('post.blood_type','','intval'), I('post.Rh','','intval')));
            $name = str_replace(' ', '', I('post.name','','htmlspecialchars'));
            $emergency_contact_name = str_replace(' ', '', I('post.emergency_contact_name','','htmlspecialchars'));
            //保存患者基本信息
            $patientInfo = array(
                'name'         =>  $name,
                'sex'          =>  I('post.sex','','intval'),
                'birthday'     =>  I('post.birthday'),
                'id_card'      =>  I('post.id_card') ? I('post.id_card') : '',
                'address'      =>  I('post.address','','htmlspecialchars') ? I('post.address','','htmlspecialchars') : '',
                'allergy_info' =>  I('post.allergy_info','','htmlspecialchars') ? I('post.allergy_info','','htmlspecialchars') : ''
            );
            $map = [
                'patient_id' => $patientId,
            ];
            $editPatientRes = $this->patient_model->editPatient($map, $patientInfo);
            if ($editPatientRes) {
                //保存患者档案附加信息
                $fileInfo = $this->patient_model->findPatientFileInfoById($patientId);
                $fileInsertInfo = array(
                    'emergency_contact_name'     => $emergency_contact_name ? $emergency_contact_name : '',
                    'emergency_contact_mobile'   => I('post.emergency_contact_mobile') ? I('post.emergency_contact_mobile') : '',
                    'emergency_contact_relation' => I('post.emergency_contact_relation','','intval'),
                    'left_ear_hearing'           => I('post.left_ear_hearing','','intval'),
                    'right_ear_hearing'          => I('post.right_ear_hearing','','intval'),
                    'left_vision'               => $leftVision,
                    'right_vision'              => $rightVision,
                    'height'                     => $height,
                    'weight'                     => $weight,
                    'blood_type'                 => $bloodType,
                    'personal_info'              => I('post.personal_info','','htmlspecialchars') ? I('post.personal_info','','htmlspecialchars') : '',
                    'family_info'                => I('post.family_info','','htmlspecialchars') ? I('post.family_info','','htmlspecialchars') : '',
                );
                if ($fileInfo) {
                    $fileInsertInfo['update_time'] = time();
                    $editFailInfo = $this->patient_model->editPatientInfo($map,$fileInsertInfo);
                    $editFailInfo ?  $this->ajaxSuccess('修改成功') : $this->ajaxError('修改失败');
                } else {
                    $fileInsertInfo['create_time'] = time();
                    $fileInsertInfo['patient_id'] = $patientId;
                    $addFileInfo = $this->patient_model->addPatientInfo($fileInsertInfo);
                    $addFileInfo ?  $this->ajaxSuccess('修改成功') : $this->ajaxError('修改失败');
                }
            } elseif ($this->patient_model->getError()) {
                $this->ajaxError($this->patient_model->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        } else {
            //患者档案
            $pid = I('get.pid','','intval');
            $map['patient_id'] = $pid;
            $field = "patient_id,name,sex,birthday,mobile,id_card,address,allergy_info";
            $patientInfo = $this->patient_model->get_the_patient_info_of_patient($map, $field);
            $patientInfo['age'] = getAge($patientInfo['birthday'], date('Y-m-d', time()));
            $fileInfo = $this->patient_model->findPatientFileInfoById($pid);
            $bloodType = json_decode($fileInfo['blood_type']);
            $fileInfo['blood_type'] = $bloodType[0];
            $fileInfo['Rh'] = $bloodType[1];
            //历史病例
            $hospitalId = $this->hospitalInfo['uid'];
            $careHistoryLists = $this->patient_model->getCareHistoryLists($pid,$hospitalId);
            $careHistory = $careHistoryLists[0];
            $careOrderLists = $this->patient_model->getCareOrderLists($careHistory['id']);
            $careOrderSubLists = [];
            foreach ($careOrderLists as $key => $value) {
                $careOrderSubLists[] = $this->patient_model->getCareOrderSubLists($value['id']);
            }
            $this->assign('patientInfo', $patientInfo);
            $this->assign('fileInfo', $fileInfo);
            $this->assign('careHistoryLists', $careHistoryLists);
            $this->assign('careHistory', $careHistory);
            $this->assign('careOrderLists', $careOrderLists);
            $this->assign('careOrderSubLists', $careOrderSubLists);
            $this->assign('patientId', $pid);
            $this->display();
        }
    }

    /**
     * 移除患者
     * Author: doreen
     */
    public function removePatient()
    {
        if (IS_POST) {
            $patientId = I('post.patient_id','','intval');
            $isDel = I('post.is_del','','intval') == 0 ? 1 : 0;
            $patientInfo = array(
                'is_del' => $isDel,
            );
            $map = [
                'patient_id' => $patientId,
            ];
            $editPatientRes = $this->patient_model->editPatient($map, $patientInfo);
            $editPatientRes ? $this->ajaxSuccess('移除成功') : $this->ajaxError('移除失败');
        }
    }

    /**
     * 移除患者列表
     * Author: doreen
     */
    public function removedLists()
    {
        $hid = $this->hospitalInfo['uid'];
        //搜索条件
        $search_content['name'] = I('post.name','','htmlspecialchars');
        $search_content['mobile'] = I('post.mobile','','htmlspecialchars');
        if ($search_content) {
            $search['name'] = array('like','%'.$search_content['name'].'%');
            $search['mobile'] = array('like','%'.$search_content['mobile'].'%');
        } else {
            $search = [];
        }
        //获取移除患者列表
        $removeLists = $this->patient_model->getPatientLists($hid, $search, '*', 1);
        if (IS_AJAX) {
            $this->ajaxSuccess('', $removeLists);
        } else {
            $this->display();
        }
    }

    /**
     * 恢复患者
     * Author: doreen
     */
    public function recoveryPatient()
    {
        $patientId = I('post.patientId');
        $patientInfo = array(
            'is_del' => 0,
        );
        $map = [
            'patient_id' => array('in', $patientId),
        ];
        $editPatientRes = $this->patient_model->editPatient($map, $patientInfo);
        $editPatientRes ? $this->ajaxSuccess('恢复成功') : $this->ajaxError('恢复失败');
    }

    /**
     * 删除患者
     * Author: doreen
     */
    public function deletePatient()
    {
        if (IS_AJAX) {
            $patientId = I('post.patient_id');
            $hospitalId = $this->hospitalInfo['uid'];
            //获取需要删除的病例和处方id
            $careAll = $this->patient_model->getCareAll($patientId, $hospitalId);
            $historyId = [];
            $orderId = [];
            foreach ($careAll as $key => $value) {
                if ($value['history_id']) {
                    $historyId[] = $value['history_id'];
                }
                if ($value['order_id']) {
                    $orderId[] = $value['order_id'];
                }
            }
            //删除患者信息及病例、处方信息
            $historyId = array_unique($historyId);
            $historyMap['id'] = array('in', $historyId);
            $orderMap['id'] = array('in', $orderId);
            $orderSubMap['fid'] = array('in', $orderId);
            $deletePatient = $this->patient_model->deletePatient($patientId, $historyMap, $orderMap, $orderSubMap);
            $deletePatient ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
        }
    }

    /**
     * 历史病例
     * Author: doreen
     */
    public function careHistory()
    {
        if (IS_AJAX) {
            $historyId = I('post.history_id','','intval');
            $careHistory = $this->patient_model->findCareHistoryById($historyId);
            $careOrderLists = $this->patient_model->getCareOrderLists($careHistory['id']);
            $careOrderSubLists = [];
            foreach ($careOrderLists as $key => $value) {
                $careOrderSubLists[] = $this->patient_model->getCareOrderSubLists($value['id']);
            }
            $data = [
                'careHistory' => $careHistory,
                'careOrderLists' => $careOrderLists,
                'careOrderSubLists' => $careOrderSubLists,
            ];
            $this->ajaxSuccess('', $data);
        }
    }

    /**
     * 导出患者列表
     * Author: doreen
     */
    public function exportExcel()
    {
        $hid = $this->hospitalInfo['uid'];
        $search_content['name'] = I('name','','htmlspecialchars');
        $search_content['mobile'] = I('mobile','','htmlspecialchars');
        if ($search_content) {
            $search['name'] = array('like','%'.$search_content['name'].'%');
            $search['mobile'] = array('like','%'.$search_content['mobile'].'%');
        } else {
            $search = [];
        }
        $field = "patient_id,name,mobile,sex,birthday,create_time";
        $patientLists = $this->patient_model->getPatientLists($hid, $search, $field);
        foreach ($patientLists['list'] as $key => &$value) {
            $value['patient_id'] = $key + 1;
            $value['sex'] = $value['sex'] == 1 ? '男' : '女';
            $value['age'] = birthday($value['birthday']);
            $value['time'] = date('Y-m-d', $value['create_time']);
            unset($value['birthday']);
            unset($value['create_time']);
        }
        $title = [
            'patient_id' => '患者编号',
            'name'       => '患者姓名',
            'mobile'     => '电话',
            'sex'        => '性别',
            'age'        => '年龄',
            'time'       => '注册时间',
        ];
        array_unshift($patientLists['list'], $title);
        $excel_title = date('Y-m-d').'_患者信息';
        create_xls($patientLists['list'],$excel_title);
    }
}
?>