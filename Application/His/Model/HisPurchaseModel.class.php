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

class HisPurchaseModel extends BaseModel
{
    protected $company_id;
    protected $_hospitalInfo;//医院信息
    public function __construct()
    {
        parent::__construct();
        $this->_hospitalInfo = session('hospital_info');
        //医院id
        $this->company_id = $this->_hospitalInfo['uid'];
    }
    /**
     * @param array $condition
     * @param string $field
     * @param int $num
     * @Name     get_list
     * @explain  获取采购信息列表
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return array
     */
    public function get_list($condition = [],$field="*")
    {
        $where = array();
        if($condition)$where = array_merge($where, $condition);
        $count = $this
            ->alias('p')
            ->join("__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = p.hmr_id")
            ->join("__HIS_MEDICINES__ me ON me.medicines_id = hmr.medicines_id")
            ->where($where)
            ->count();
        $pager = new_page($count, 10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('p')
            ->field($field)
            ->join("__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = p.hmr_id")
            ->join("__HIS_MEDICINES__ me ON me.medicines_id = hmr.medicines_id")
            ->where($where)
            ->order('p.purchase_id')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    /**
     * @param array $condition
     * @Name     delete_purchase_info
     * @explain  删除采购信息
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return bool
     */
    public function delete_purchase_info($condition = [])
    {
        $this->startTrans();
        $where = array();
        if($condition)$where = array_merge($where, $condition);
        $delete_info = $this->where($where)->delete();
        if ($delete_info) {
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * @param array $condition 条件
     * @param string $field    查询信息
     * @Name     once_again_put_in_storage
     * @explain  再次入库所需信息
     * @author   zuochuanye
     * @Date     2017/11/07
     * @return bool
     */
    public function once_again_put_in_storage($condition = [], $field = '*')
    {
        $info = $this
            ->alias('p')
            ->join("__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = p.hmr_id")
            ->join("__HIS_MEDICINES__ me ON me.medicines_id = hmr.medicines_id")
            ->field($field)
            ->where($condition)
            ->select();
        return $info ? $info : false;
    }

    /**
     * @param array $condition
     * @param string $field
     * @Name     getBatchesOfInventoryListStatusEqTwo
     * @explain  获取已经审核列表
     * @author   zuochuanye
     * @Date     2017/11/28
     * @return array
     */
    public function getBatchesOfInventoryListStatusEqTwo($condition = [], $field="*"){
        $where = array(
            'b.batches_of_inventory_status' =>  2,
            'b.company_id'  =>  $this->company_id
        );
        if($condition)$where = array_merge($where, $condition);

        $count = $this
            ->alias('p')
            ->join('__HIS_BATCHES_OF_INVENTORY__ b ON b.batches_of_inventory_id = p.batches_of_inventory_id')
            ->join('__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = p.hmr_id')
            ->join('__HIS_MEDICINES__ m ON hmr.medicines_id = m.medicines_id')
            ->where($where)
            ->count();
        $pager = new_page($count, 10,2);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('p')
            ->field($field)
            ->join('__HIS_BATCHES_OF_INVENTORY__ b ON b.batches_of_inventory_id = p.batches_of_inventory_id')
            ->join('__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = p.hmr_id')
            ->join('__HIS_MEDICINES__ m ON hmr.medicines_id = m.medicines_id')
            ->where($where)
            ->order('p.purchase_id')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }
}