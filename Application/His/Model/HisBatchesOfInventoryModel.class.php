<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Model;

use Common\Model\BaseModel;

class HisBatchesOfInventoryModel extends BaseModel
{
    protected $_hospitalInfo;//医院信息
    protected $company_id;  //医院id
    public function __construct()
    {
        parent::__construct();
        $this->_hospitalInfo = session('hospital_info');
        $this->company_id = $this->_hospitalInfo['uid'];
    }
    //自动验证
    protected $_validate = array(
        array('supplier_id', 'require', '供应商不能为空！', self::EXISTS_VALIDATE),
        array('batches_of_inventory_date', 'require', '制单日期不能为空！', self::EXISTS_VALIDATE),
    );
    //自动完成
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 2, 'function')
    );

    /**
     * @param array $condition
     * @param string $field
     * @param int $num
     * @Name     get_list
     * @explain  获取列表信息
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return array
     */
    public function get_list($condition = [], $field = "*")
    {
        $where = array(
            'b.company_id' => $this->company_id
        );
        if($condition) $where = array_merge($where, $condition);
        $count = $this
            ->alias('b')
            ->join('__HIS_SUPPLIER__ AS s ON s.sid = b.supplier_id')
            ->where($where)
            ->count();
        $pager = new_page($count, 10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('b')
            ->field($field)
            ->join('__HIS_SUPPLIER__ AS s ON s.sid = b.supplier_id')
            ->where($where)
            ->order('b.batches_of_inventory_id DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);

    }

    /**
     * @param array $condition 删除条件
     * @Name     delete_batches_of_inventory_info
     * @explain   删除批次动态相关信息
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return bool|mixed
     */
    public function delete_batches_of_inventory_info($condition = [])
    {
        $this->startTrans();
        $where = array();
        $where = array_merge($where, $condition);
        $delete_info = $this->where($where)->delete();
        if ($delete_info) {
            $delete_purchase_info = M("his_purchase")->where($where)->delete();
        } else {
            $delete_purchase_info = false;
        }
        if ($delete_purchase_info) {
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * @param string $company_id 诊所ID
     * @Name     gets_the_largest_id_of_the_HisBatchesOfInventory
     * @explain  获取批次库存表中的最大ID
     * @author   zuochuanye
     * @Date
     * @return bool
     */
    public function gets_the_largest_id_of_the_HisBatchesOfInventory($company_id = '')
    {
        $where = [
            'company_id' => $company_id
        ];
        $maxNumber = $this->where($where)->max('batches_of_inventory_id');
        return $maxNumber ? $maxNumber : false;
    }

    /**
     * @param $data   批次数据表数据
     * @param $purchase_data  采购信息表数据
     * @Name     insert_info
     * @explain  添加数据
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return bool|mixed
     */
    public function insert_info($data = [], $purchase_data = [])
    {
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        foreach ($purchase_data as $s => $t) {
            foreach ($t as $y => $i) {
                $purchase_data[$s][$y] = trim($i);
            }
        }
        if (!$data = $this->create($data)) {
            //验证不通过返回错误
            return false;
        } else {
            $batches_of_inventory_id = $this->add($data);
            foreach ($purchase_data as $key => $value) {
                $purchase_data[$key]['batches_of_inventory_id'] = $batches_of_inventory_id;
            }
            foreach ($purchase_data as $p => $q) {
                $purchase_id = M('his_purchase')->add($q);
                $log_info = array(
                    'company_id' => $data['company_id'],
                    'purchase_id' => $purchase_id,
                    'batches_of_inventory_number' => $data['batches_of_inventory_number'],
                    'hmr_id' => $q['hmr_id'],
                    'modifier_id' => $data['purchasing_agent_id'],
                    'new_quantity' => $q['purchase_num'],
                    'new_trade_price' => $q['purchase_trade_price'],
                    'new_prescription_price' => $q['purchase_prescription_price'],
                    'old_quantity' => $q['purchase_num'],
                    'old_trade_price' => $q['purchase_trade_price'],
                    'old_prescription_price' => $q['purchase_prescription_price'],
                    'operation_module' => 1,
                    'create_time' => time()
                );
                $log_id = M('his_storage_log')->add($log_info);
            }
            if ($log_id) {
                $this->commit();
                return $batches_of_inventory_id;
            } else {
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * @param array $data 批次数据表数据
     * @param array $purchase_data 采购信息表数据
     * @param string $batches_of_inventory_id
     * @Name     edit_info
     * @explain  修改数据
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return bool|string
     */
    public function edit_info($data = [], $purchase_data = [], $batches_of_inventory_id = '')
    {
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        foreach ($purchase_data as $s => $t) {
            foreach ($t as $y => $i) {
                $purchase_data[$s][$y] = trim($i);
            }
        }
        if (!$data = $this->create($data)) {
            //验证不通过返回错误
            return false;
        } else {
            $this->where(array('batches_of_inventory_id' => $batches_of_inventory_id))->save($data);
            $batches_of_inventory_info = $this->field('batches_of_inventory_number,company_id,purchasing_agent_id')->where(array('batches_of_inventory_id' => $batches_of_inventory_id))->find();
            foreach ($purchase_data as $p => $q) {
                $purchase_info = M('his_purchase')->field('purchase_num,hmr_id,purchase_num,purchase_trade_price,purchase_prescription_price')->where(array('purchase_id' => $q['purchase_id']))->find();
                $purchase_id_info = $q['purchase_id'];
                unset($q['purchase_id']);
                $purchase_id = M('his_purchase')->where(array('purchase_id' => $purchase_id_info))->save($q);
                $log_info = array(
                    'company_id' => $batches_of_inventory_info['company_id'],
                    'purchase_id' => $purchase_id,
                    'batches_of_inventory_number' => $batches_of_inventory_info['batches_of_inventory_number'],
                    'hmr_id' => $purchase_info['hmr_id'],
                    'modifier_id' => $batches_of_inventory_info['purchasing_agent_id'],
                    'new_quantity' => $q['purchase_num'],
                    'new_trade_price' => $q['purchase_trade_price'],
                    'new_prescription_price' => $q['purchase_prescription_price'],
                    'old_quantity' => $purchase_info['purchase_num'],
                    'old_trade_price' => $purchase_info['purchase_trade_price'],
                    'old_prescription_price' => $purchase_info['purchase_prescription_price'],
                    'operation_module' => 2,
                    'create_time' => time()
                );
                $log_id = M('his_storage_log')->add($log_info);
            }
            if ($log_id) {
                $this->commit();
                return $batches_of_inventory_id;
            } else {
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * @param array $condition
     * @param string $field
     * @Name     getBatchesOfInventoryInfo
     * @explain  根据ID获取表中信息
     * @author   zuochuanye
     * @Date     2017/11/07
     * @return bool
     */
    public function getBatchesOfInventoryInfo($condition = [], $field = '*')
    {
        $info = $this
            ->alias('b')
            ->field($field)
            ->join('__HIS_MEMBER__ AS m ON m.uid = b.purchasing_agent_id')
            ->join('__HIS_SUPPLIER__ AS s ON s.sid = b.supplier_id')
            ->where($condition)
            ->find();
        return $info ? $info : false;
    }
}