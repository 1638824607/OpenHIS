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
 * 处方附加费设置类
 * PrescriptionExtrachargesController
 * Author: doreen
 */
class PrescriptionExtrachargesController extends HisBaseController
{

    protected $extracharges_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->extracharges_model = D('his_prescription_extracharges');
    }

    /**
     * 处方附加费列表
     * Author: doreen
     */
    public function index()
    {
        //搜索条件
        $search_content = I('post.search','','htmlspecialchars');
        if ($search_content) {
            $search['extracharges_name'] = array('like','%'.$search_content.'%');;
        } else {
            $search = [];
        }
        $hospitalId = $this->hospitalInfo['uid'];
        //获取处方附加费列表
        $chargesLists = $this->extracharges_model->getExtraChargesList($hospitalId, $search);
        if (IS_AJAX) {
            $this->ajaxSuccess('', $chargesLists);
        } else {
            $this->display();
        }
    }

    /**
     * 处方附加费添加
     * Author: doreen
     */
    public function addExtraCharges()
    {
        if(IS_AJAX){
            $mid = $this->userInfo['uid'];
            $hospitalId = $this->hospitalInfo['uid'];
            $chargesName = I('post.extracharges_name','','htmlspecialchars');
            //添加附加费去重
            $condition = array(
                'hid' => $this->hospitalInfo['uid'],
            );
            $chargesList = $this->extracharges_model->getExtracharges($condition);
            foreach ($chargesList as $key => $value) {
                if ($value['extracharges_name'] == $chargesName) {
                    $this->ajaxError('费用名称已存在');
                }
            }
            $chargesFee  = number_format(I('post.fee'),2);
            if(!is_numeric($chargesFee)) $this->ajaxError('费用金额为整数或小数');
            //保存添加的附加费
            $data = array(
                'mid'                  =>  $mid,
                'hid'                  =>  $hospitalId,
                'extracharges_name'    =>  $chargesName,
                'fee'                  =>  $chargesFee,
                'type'                 =>  I('post.type','','intval'),
                'create_time'          =>  time(),
            );
            $result = $this->extracharges_model->addExtraCharges($data);
            if ($result) {
                $this->ajaxSuccess('添加成功');
            } elseif ($this->extracharges_model->getError()) {
                $this->ajaxError($this->extracharges_model->getError());
            } else {
                $this->ajaxError('添加失败');
            }
        }else{
            $this->display();
        }
    }

    /**
     * 处方附加费修改
     * Author: doreen
     */
    public function editExtraCharges()
    {
        if(IS_POST){ //ajax保存编辑的附加费
            $preId = I('post.pre_id','','intval');
            $chargesName = I('post.extracharges_name','','htmlspecialchars');
            //编辑时去重
            $condition = array(
                'hid' => $this->hospitalInfo['uid'],
                'pre_id' => array('NEQ', $preId),
            );
            $chargesList = $this->extracharges_model->getExtracharges($condition);
            foreach ($chargesList as $key => $value) {
                if ($value['extracharges_name'] == $chargesName) {
                    $this->ajaxError('费用名称已存在');
                }
            }
            $chargesFee  = number_format(I('post.fee'),2);
            if(!is_numeric($chargesFee)) $this->ajaxError('费用金额为整数或小数');
            //保存编辑的附加费
            $data = array(
                'extracharges_name'    =>  $chargesName,
                'fee'                  =>  $chargesFee,
                'type'                 =>  I('post.type','','intval'),
                'update_time'          =>  time(),
            );
            $map = [
                'pre_id' => $preId,
            ];
            $result = $this->extracharges_model->editExtraCharges($map, $data);
            if ($result) {
                $this->ajaxSuccess('修改成功');
            } elseif ($this->extracharges_model->getError()) {
                $this->ajaxError($this->extracharges_model->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        }else{ //编辑页面显示
            $preId = I('get.pre_id','','intval');
            $chargesInfo = $this->extracharges_model->getChargesInfoById($preId);
            $this->ajaxSuccess('',$chargesInfo);
        }
    }

    /**
     * 处方附加费删除
     * Author: doreen
     */
    public function deleteExtraCharges()
    {
        if(IS_AJAX){
            $preId = I('post.pre_id','','intval');
            $res = $this->extracharges_model->deleteExtraCharges($preId);
            $res ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
        }
    }

}