<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;

class BatchesOfInventoryController extends HisBaseController
{
    protected $_purchase;
    protected $_batches_of_inventory;
    protected $_inventory;
    protected $company_id;//公司ID
    protected $uid;       //用户ID
    public function __construct()
    {
        parent::__construct();
        $this->_purchase = D('HisPurchase');
        $this->_batches_of_inventory = D('his_batches_of_inventory');
        $this->_inventory = D('HisInventory');

        $this->company_id = $this->hospitalInfo['uid'];
        $this->uid = $this->userInfo['uid'];
    }

    /**
     * @Name     get_list
     * @explain  获取列表信息
     * @author   zuochuanye
     * @Date     2017/10/26
     */
    public function get_list()
    {
        $supplier = M('his_supplier')->field('sid,supplier_name')->where(array('hospital_id' => $this->company_id))->select();
        $this->assign('supplier', $supplier);
        $this->display("getList");
    }

    /**
     * @Name     getBatchesOfInventoryList
     * @explain  获取批次库存列表
     * @author   zuochuanye
     * @Date     2017/11/07
     */
    public function getBatchesOfInventoryList()
    {
        if (IS_AJAX) {
            $search = I('post.search', '', 'htmlspecialchars') ? I('post.search', '', 'htmlspecialchars') : '';
            if (!empty($search)) {
                $condition['b.batches_of_inventory_number'] = array('like', '%' . $search . '%');
            }
            $batches_of_inventory_status = I('post.batches_of_inventory_status', '', 'intval') ? I('post.batches_of_inventory_status', '', 'intval') : '';
            if (!empty($batches_of_inventory_status)) {
                $condition['b.batches_of_inventory_status'] = $batches_of_inventory_status;
            }
            $supplier_id = I('post.supplier_id', '', 'intval') ? I('post.supplier_id', '', 'intval') : '';
            if (!empty($supplier_id)) {
                $condition['supplier_id'] = $supplier_id;
            }
            $start_time = I('post.start_time');
            $end_time = I('post.end_time');
            if (!empty($start_time) && !empty($end_time)) {
                $condition['b.batches_of_inventory_date'] = array(array('gt', $start_time), array('lt', $end_time));
            }
            //获取的信息
            $fields = 'b.purchasing_agent_id,b.batches_of_inventory_verifier,b.batches_of_inventory_id,b.batches_of_inventory_number,b.batches_of_inventory_total_money,b.batches_of_inventory_date,b.batches_of_inventory_status,b.batches_of_inventory_verifier_date,s.supplier_name';
            $batches_of_inventory_info = $this->_batches_of_inventory->get_list($condition,$fields);
            foreach ($batches_of_inventory_info['list'] as $k => $v) {
                $batches_of_inventory_info['list'][$k]['user_name'] = D("HisMember")->role_judgement($v['purchasing_agent_id']);
                $batches_of_inventory_info['list'][$k]['name'] = D("HisMember")->role_judgement($v['batches_of_inventory_verifier']);
            }
            $batches_of_inventory_info ? $this->ajaxSuccess('成功', $batches_of_inventory_info) : $this->ajaxError('失败');
        }
    }
    /**
     * @Name     audit
     * @explain  审核
     * @author   zuochuanye
     * @Date     2017/10/26
     */
    public function audit()
    {
        if (IS_AJAX) {
            $batches_of_inventory_id = I('post.batches_of_inventory_id', '', 'intval') ? I('post.batches_of_inventory_id', '', 'intval') : false;
            $purchase_id = I('post.purchase_id');
            $purchase_num = I('post.purchase_num');
            $purchase_unit = I('post.purchase_unit');
            $purchase_trade_price = I('post.purchase_trade_price');
            $purchase_prescription_price = I('post.purchase_prescription_price');
            if ($batches_of_inventory_id) {
                // 批次库存表数据
                $batches_of_inventory_info['batches_of_inventory_verifier'] = $this->uid;
                $batches_of_inventory_info['batches_of_inventory_status'] = 2;
                $batches_of_inventory_info['batches_of_inventory_verifier_date'] =  time();
                $batches_of_inventory_info['update_time'] = time();
                //采购信息数据
                $medicines_info = array();
                foreach ($purchase_id as $k => $v) {
                    $medicines_info[$k]['purchase_id'] = $v;
                    $medicines_info[$k]['purchase_num'] = $purchase_num[$k];
                    $medicines_info[$k]['purchase_unit'] = $purchase_unit[$k];
                    $medicines_info[$k]['purchase_trade_price'] = $purchase_trade_price[$k];
                    $medicines_info[$k]['purchase_prescription_price'] = $purchase_prescription_price[$k];
                    $medicines_info[$k]['purchase_trade_total_amount'] = $purchase_trade_price[$k] * $purchase_num[$k];
                    $medicines_info[$k]['purchase_prescription_total_amount'] = $purchase_prescription_price[$k] * $purchase_num[$k];
                }
                $insert_return = $this->_batches_of_inventory->edit_info($batches_of_inventory_info, $medicines_info, $batches_of_inventory_id);
                if (!$insert_return) {
                    $this->ajaxError('失败');
                } else {
                    $inventory_return = $this->_inventory->inventory_add($insert_return);
                    if ($inventory_return) {
                        $this->ajaxSuccess('成功', U('/BatchesOfInventory/get_list'));
                    }
                }
            } else {
                $this->ajaxError('无法进行审核');
            }

        }
    }

    /**
     * @Name     purchase_list
     * @explain  采购信息列表
     * @author   zuochuanye
     * @Date     2017/10/26
     */
    public function purchase_list()
    {
        $batches_of_inventory_id = I('get.batches_of_inventory_id', '', 'intval');
        $field = 'b.batches_of_inventory_id,b.batches_of_inventory_number,b.batches_of_inventory_date,s.supplier_name,b.purchasing_agent_id';
        $condition = array(
            'b.batches_of_inventory_id' => $batches_of_inventory_id,
            'b.company_id' => $this->company_id
        );
        $batches_of_inventory_info = $this->_batches_of_inventory->getBatchesOfInventoryInfo($condition, $field);
        $batches_of_inventory_info['user_name'] = D("HisMember")->role_judgement($batches_of_inventory_info['purchasing_agent_id']);
        $this->assign('batches_of_inventory_info', $batches_of_inventory_info);
        $this->display('purchaseList');
    }

    /**
     * @Name     get_purchase_list
     * @explain  获取采购信息列表
     * @author   zuochuanye
     * @Date     2017/11/07
     */
    public function get_purchase_list()
    {
        if (IS_AJAX) {
            $batches_of_inventory_id = I("post.batches_of_inventory_id", '', 'intval') ? I("post.batches_of_inventory_id", '', 'intval') : '';
            if (!empty($batches_of_inventory_id)) {
                $field = 'me.medicines_name,conversion,unit,producter,purchase_num,purchase_trade_price,purchase_prescription_price,purchase_trade_total_amount,purchase_prescription_total_amount,purchase_id';
                $purchase_info = $this->_purchase->get_list(array('p.batches_of_inventory_id' => $batches_of_inventory_id), $field);
                $purchase_info ? $this->ajaxSuccess('成功', $purchase_info) : $this->ajaxError('失败');
            } else {
                $this->ajaxError("失败");
            }
        }
    }

    /**
     * @Name     delete_batches_of_inventory
     * @explain  删除batches_of_inventory相关信息
     * @author   zuochuanye
     * @Date     2017/10/26
     */
    public function delete_batches_of_inventory()
    {
        if (IS_AJAX) {
            $batches_of_inventory_id = I('post.batches_of_inventory_id', '', 'intval') ? I('post.batches_of_inventory_id', '', 'intval') : false;
            if ($batches_of_inventory_id) {
                $return = $this->_batches_of_inventory->delete_batches_of_inventory_info(array('batches_of_inventory_id' => $batches_of_inventory_id));
                $return ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
            } else {
                $this->ajaxError('无法删除');
            }
        }


    }

    /**
     * @Name     delete_purchase
     * @explain  删除采购信息
     * @author   zuochuanye
     * @Date     2017/10/26
     */
    public function delete_purchase()
    {
        if (IS_AJAX) {
            $purchase_id = I('post.purchase_id', '', 'intval') ? I('post.purchase_id', '', 'intval') : false;
            if ($purchase_id) {
                $return = $this->_purchase->delete_purchase_info(array('purchase_id' => $purchase_id));
                $return ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
            } else {
                $this->ajaxError('无法删除');
            }
        }

    }

}