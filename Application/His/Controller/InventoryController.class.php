<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;
class InventoryController extends HisBaseController
{
    protected $_batches_of_inventory;
    protected $_inventory;
    protected $_medicines;
    protected $_purchase;

    protected $company_id; //公司ID
    protected $uid;//采购员ID

    public function __construct()
    {
        parent::__construct();
        $this->_batches_of_inventory = D('his_batches_of_inventory');
        $this->_inventory = D('HisInventory');
        $this->_medicines = D('HisMedicines');
        $this->_purchase = D('HisPurchase');

        $this->company_id = $this->hospitalInfo['uid'];
        $this->uid = $this->userInfo['uid'];
    }

    /**
     * @Name     purchase
     * @explain  采购入库
     * @author   zuochuanye
     * @Date     2017/11/28
     */
    public function purchase()
    {
        $batches_of_inventory_id = I("get.batches_of_inventory_id", '', 'intval') ? I("get.batches_of_inventory_id", '', 'intval') : '';
        if (!empty($batches_of_inventory_id)) {
            $condition = array(
                'hmr.hospital_id' => $this->company_id,
                'p.batches_of_inventory_id' => $batches_of_inventory_id
            );
            $again_info = $this->_purchase->once_again_put_in_storage($condition);
            $this->assign('again_info', $again_info);
        }
        //供应商
        $supplier = M('his_supplier')->field('sid,supplier_name')->where(array('hospital_id' => $this->company_id))->select();
        $batches_of_inventory_number = self::getbatches_of_inventory_number();
        $vouching['user_name'] =  D("HisMember")->role_judgement($this->uid);
        $medicines_class = D('his_dictionary')->getLevelLists(array('parent_id' => 11));
        $this->assign('medicines_class', $medicines_class);
        $this->assign('vouching_name', $vouching['user_name']);
        $this->assign('supplier', $supplier);
        $this->assign('batches_of_inventory_number', $batches_of_inventory_number);
        $this->display();
    }


    /**
     * @Name     submit_purchasing_information
     * @explain  提交批次库存信息
     * @author   zuochuanye
     * @Date     2017/10/26
     */
    public function submit_purchasing_information()
    {
        if (IS_AJAX) {
            //直接入库 status=1;提价审核status = 0;
            $status = I('get.status', '', 'intval') ? I('get.status', '', 'intval') : '';
            $hmr_id = I('post.hmr_id');
            $purchase_num = I('post.purchase_num');
            $purchase_unit = I('post.purchase_unit');
            $purchase_trade_price = I('post.purchase_trade_price');
            $purchase_prescription_price = I('post.purchase_prescription_price');
            $batches_of_inventory_info = array(
                'company_id' => $this->company_id,
                'supplier_id' => I('post.supplier_id', '', 'intval'),
                'batches_of_inventory_number' => self::getbatches_of_inventory_number(),
                'batches_of_inventory_total_money' => self::getbatches_of_inventory_total_money($purchase_trade_price, $purchase_num),
                'purchasing_agent_id' => $this->uid,
                'batches_of_inventory_date' => I('post.batches_of_inventory_date'),
                'batches_of_inventory_status' => $status == 2 ? 2 : 1,
            );
            if ($status == 2) {
                $batches_of_inventory_info['batches_of_inventory_verifier'] = $this->uid;
                $batches_of_inventory_info['batches_of_inventory_verifier_date'] = time();
                $batches_of_inventory_info['update_time'] = time();
            }
            $medicines_info = array();
            foreach ($hmr_id as $k => $v) {
                $medicines_info[$k]['hmr_id'] = $v;
                $medicines_info[$k]['purchase_num'] = $purchase_num[$k];
                $medicines_info[$k]['purchase_unit'] = $purchase_unit[$k];
                $medicines_info[$k]['purchase_trade_price'] = $purchase_trade_price[$k];
                $medicines_info[$k]['purchase_prescription_price'] = $purchase_prescription_price[$k];
                $medicines_info[$k]['purchase_trade_total_amount'] = $purchase_trade_price[$k] * $purchase_num[$k];
                $medicines_info[$k]['purchase_prescription_total_amount'] = $purchase_prescription_price[$k] * $purchase_num[$k];
                $medicines_info[$k]['create_time'] = time();
            }
            $insert_return = $this->_batches_of_inventory->insert_info($batches_of_inventory_info, $medicines_info);
            if (!$insert_return) {
                $this->ajaxError('失败');
            } else {
                if ($status) {
                    $this->_inventory->inventory_add($insert_return);
                }
                $this->ajaxSuccess('成功',U('/BatchesOfInventory/get_list'));
            }
        }
    }

    /**
     * @Name     getMedicinesList
     * @explain  获取药品列表
     * @author   zuochuanye
     * @Date     2017/11/04
     */
    public function getMedicinesList()
    {
        if (IS_AJAX) {
            $medicines_class_id = I('post.medicines_class_id', '', 'intval');
            $medicinesName = I('post.medicinesName', '', 'htmlspecialchars');
            $where = [];
            if (!empty($medicinesName)) $where['_string'] = ' (medicines_name like "%' . $medicinesName . '%")  OR ( keywords like "%' . $medicinesName . '%") ';
            if (!empty($medicines_class_id)) {
                $dictionary_info = D('his_dictionary')->findDictionaryById($medicines_class_id);
                if ($dictionary_info) {
                    $where['medicines_class'] = array('like', '%' . $dictionary_info['dictionary_name'] . '%');
                }
            }
            $medicines_info = $this->_medicines->getMedicinesLists($this->company_id, $where);
            $medicines_info ? $this->ajaxSuccess('成功', $medicines_info) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name     submitMedicines
     * @explain  添加选中药品
     * @author   zuochuanye
     * @Date     2017/11/07
     */
    public function submitMedicines()
    {
        if (IS_AJAX) {
            $hmr_id_arr = I("post.hmr_id_arr", '');
            $medicinesInfo = [];
            foreach ($hmr_id_arr as $k => $v) {
                $medicinesInfo[] = $this->_medicines->getAddMedicinesInfo($this->company_id, array('hmr_id' => $v));
            }
            $medicinesInfo ? $this->ajaxSuccess('成功', $medicinesInfo) : $this->ajaxError('失败');

        }
    }

    /**
     * @param $money
     * @param $num
     * @Name     getbatches_of_inventory_total_money
     * @explain  获取采购总金额
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return bool|number
     */
    public function getbatches_of_inventory_total_money($money, $num)
    {
        if ($money && $num) {
            $total = array();
            foreach ($money as $k => $v) {
                $total[] = $money[$k] * $num[$k];
            }
            return array_sum($total);
        } else {
            return false;
        }
    }

    /**
     * @Name     getbatches_of_inventory_number
     * @explain  获取编号
     * @author   zuochuanye
     * @Date        2017/10/26
     * @return string
     */
    public function getbatches_of_inventory_number()
    {
        $year = date('Y');
        $month = str_pad(date('m', time()), 2, 0, STR_PAD_LEFT);
        $days = str_pad(date('d', time()), 2, 0, STR_PAD_LEFT);
        $company_id = str_pad($this->company_id, 5, 0, STR_PAD_LEFT);
        $batches_of_inventory_id = $this->_batches_of_inventory->gets_the_largest_id_of_the_HisBatchesOfInventory($this->company_id);
        $batches_of_inventory_id = $batches_of_inventory_id ? $batches_of_inventory_id : 0;
        $batches_of_inventory_id = str_pad($batches_of_inventory_id + 1, 5, 0, STR_PAD_LEFT);
        return $year . $month . $days . $company_id . $batches_of_inventory_id;
    }

    /**
     * @Name     inventory_list
     * @explain  库存页面展示
     * @author   zuochuanye
     * @Date     2017/11/08
     */
    public function inventory_list()
    {
        $this->display("inventoryList");
    }

    /**
     * @Name     getInventoryListInfo
     * @explain  获取药品库存列表
     * @author   zuochuanye
     * @Date     2017/11/08
     */
    public function getInventoryListInfo()
    {
        if (IS_AJAX) {
            $search = I("post.search", '', 'htmlspecialchars');
            $condition = [];
            if (!empty($search)) $condition['me.medicines_name'] = array('like', '%' . $search . '%');
            $field = 'early_warning,conversion,inventory_id,inventory_num,inventory_prescription_price,inventory_trade_price,inventory_unit,medicines_class,medicines_name,producter,inventory_prescription_total_amount,inventory_trade_total_amount';
            $info = $this->_inventory->get_list($condition, $field);
            $info ? $this->ajaxSuccess('成功', $info) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name     early_warning
     * @explain  设置预警
     * @author   zuochuanye
     * @Date     2017/11/28
     */
    public function set_early_warning()
    {
        if(IS_AJAX){
            $early_warning = I('post.early_warning','');
            if(!empty($early_warning)){
                $condition['company_id'] = $this->company_id;
                $update_info['early_warning'] =  $early_warning;
                $info = $this->_inventory->adjust_price($condition, $update_info);
                $info ? $this->ajaxSuccess("预警设置成功") : $this->ajaxError("预警设置失败");
            }else{
                $this->ajaxError("预警数量不能为空");
            }
        }
    }
    /**
     * @Name     adjust_price
     * @explain  调价
     * @author   zuochuanye
     * @Date     2017/11/08
     */
    public function adjust_price()
    {
        if (IS_AJAX) {
            $inventory_prescription_price = I('post.inventory_prescription_price', '');
            $inventory_num = I('post.inventory_num', '');
            $inventory_id = I('post.inventory_id', '');
            $inventory_trade_price = I('post.inventory_trade_price', '');
            $update_info = array(
                "inventory_prescription_price" => $inventory_prescription_price,
                "inventory_num" => $inventory_num,
                "inventory_trade_total_amount" => $inventory_trade_price * $inventory_num,
                "inventory_prescription_total_amount" => $inventory_prescription_price * $inventory_num,
                "update_time" => time()
            );
            $condition = array("inventory_id" => $inventory_id);
            $info = $this->_inventory->adjust_price($condition, $update_info);
            $info ? $this->ajaxSuccess('成功') : $this->ajaxError("失败");
        }
    }

    /**
     * @Name     getBatchesOfInventoryListStatusEqTwo
     * @explain  获取已经审核的批次库存
     * @author   zuochuanye
     * @Date     2017/11/08
     */
    public function getBatchesOfInventoryListStatusEqTwo()
    {
        if (IS_AJAX) {
            $search = I('post.search', '', 'htmlspecialchars');
            $condition = [];
            if (!empty($search)) $condition['m.medicines_name'] = array('like', '%' . $search . '%');
            $field = 'b.batches_of_inventory_number,m.medicines_name,m.medicines_class,m.producter,m.conversion,p.purchase_unit,p.purchase_num,p.purchase_prescription_price,p.purchase_prescription_total_amount,p.purchase_trade_price,p.purchase_trade_total_amount';
            $info = $this->_purchase->getBatchesOfInventoryListStatusEqTwo($condition, $field);
            $info ? $this->ajaxSuccess('成功', $info) : $this->ajaxError('失败');
        }
    }
}