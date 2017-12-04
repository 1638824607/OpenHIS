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
 * 供应商类
 * SupplierController
 * Author: doreen
 */
class SupplierController extends HisBaseController
{
    protected $supplier_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->supplier_model = D('his_supplier');
    }

    /**
     * 供应商列表
     * Author: doreen
     */
    public function index()
    {
        //搜索条件
        $search_content = I('post.search','','htmlspecialchars');
        if ($search_content) {
            $search['_string'] = ' (supplier_name like "%'.$search_content.'%")  OR ( contact_name like "%'.$search_content.'%") ';
        } else {
            $search = [];
        }
        //获取供应商列表
        $hospitalId = $this->hospitalInfo['uid'];
        $supplierLists = $this->supplier_model->getSupplierLists($hospitalId, $search);
        if (IS_AJAX) {
            $this->ajaxSuccess('', $supplierLists);
        } else {
            $this->display();
        }
    }

    /**
     * 供应商添加
     * Author: doreen
     */
    public function addSupplier()
    {
        if(IS_AJAX){
            $data = I();
            $hospitalId = $this->hospitalInfo['uid'];
            //添加供应商时去重
            $condition = array(
                'hospital_id' => $hospitalId,
            );
            $supplierList = $this->supplier_model->getSupplier($condition);
            foreach ($supplierList as $key => $value) {
                if ($value['supplier_name'] == $data['supplier_name']) {
                    $this->ajaxError('供应商名称已存在');
                }
            }
            if (preg_match('/\\d+/', I('post.supplier_name','','htmlspecialchars'))) $this->ajaxError('供应商名称不包含数字');
            if (preg_match('/\\d+/', I('post.contact_name','','htmlspecialchars'))) $this->ajaxError('联系人姓名不包含数字');
            //保存添加供应商信息
            $data ['hospital_id'] = $hospitalId;
            $data['create_time'] = time();
            $result = $this->supplier_model->addSupplier($data);
            if ($result) {
                $this->ajaxSuccess('添加成功');
            } elseif ($this->supplier_model->getError()) {
                $this->ajaxError($this->supplier_model->getError());
            } else {
                $this->ajaxError('添加失败');
            }
        }
    }

    /**
     * 供应商修改
     * Author: doreen
     */
    public function editSupplier()
    {
        if(IS_POST){ //ajax提交保存编辑供应商信息
            $data = I();
            //编辑供应商时去重
            $condition = array(
                'hospital_id' => $this->hospitalInfo['uid'],
                'sid' => array('NEQ', I('post.sid','','intval'))
            );
            $supplierList = $this->supplier_model->getSupplier($condition);
            foreach ($supplierList as $key => $value) {
                if ($value['supplier_name'] == $data['supplier_name']) {
                    $this->ajaxError('供应商名称已存在');
                }
            }
            if (preg_match('/\\d+/', I('post.supplier_name','','htmlspecialchars'))) $this->ajaxError('供应商名称不包含数字');
            if (preg_match('/\\d+/', I('post.contact_name','','htmlspecialchars'))) $this->ajaxError('联系人姓名不包含数字');
            //保存编辑的供应商
            $sid = I('post.sid','','intval');
            $data['update_time'] = time();
            $map = [
                'sid' => $sid,
            ];
            $result = $this->supplier_model->editSupplier($map, $data);
            if ($result) {
                $this->ajaxSuccess('修改成功');
            } elseif ($this->supplier_model->getError()) {
                $this->ajaxError($this->supplier_model->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        }else{ //显示编辑页面
            $sid = I('get.sid','','intval');
            $supplierInfo = $this->supplier_model->getSupplierInfoById($sid);
            $this->ajaxSuccess('', $supplierInfo);
        }
    }

    /**
     * 供应商删除
     * Author: doreen
     */
    public function deleteSupplier()
    {
        if(IS_POST){
            $sid = I('post.sid','','intval');
            if($sid){
                $res = $this->supplier_model->deleteSupplier($sid);
                $res ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
            }
        }
    }

}