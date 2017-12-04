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

class HisInventoryModel extends BaseModel
{
    protected $_hospitalInfo;//医院信息
    protected $company_id;//医院id
    public function __construct()
    {
        parent::__construct();
        $this->_hospitalInfo = session('hospital_info');
        $this->company_id = $this->_hospitalInfo['uid'];
    }
    /**
     * @param array $condition
     * @param string $field
     * @param int $num
     * @Name     get_list
     * @explain  获取列表
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return array
     */
    public function get_list($condition = [], $field = "*")
    {
        $where = array(
            'i.company_id' => $this->company_id
        );
        if($condition) $where = array_merge($where, $condition);
        $count = $this
            ->alias('i')
            ->join('__HIS_HOSPITAL_MEDICINES_RELATION__ AS hmr ON hmr.hmr_id = i.hmr_id')
            ->join('__HIS_MEDICINES__ AS me ON me.medicines_id = hmr.medicines_id')
            ->where($where)
            ->count();
        $pager = new_page($count, 10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('i')
            ->field($field)
            ->join('__HIS_HOSPITAL_MEDICINES_RELATION__ AS hmr ON hmr.hmr_id = i.hmr_id')
            ->join('__HIS_MEDICINES__ AS me ON me.medicines_id = hmr.medicines_id')
            ->where($where)
            ->order('i.update_time DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    /**
     * @param string $id
     * @Name     inventory_add
     * @explain  库存添加
     * @author   zuochuanye
     * @Date     2017/10/26
     * @return bool|mixed
     */
    public function inventory_add($id = '')
    {
        $this->startTrans();
        if ($id) {
            $purchase_info = M('his_purchase')
                ->field('purchase_id,hmr_id,purchase_num,purchase_unit,purchase_trade_price,purchase_prescription_price,purchase_trade_total_amount,purchase_prescription_total_amount')
                ->where(array('batches_of_inventory_id' => $id))
                ->select();
            foreach ($purchase_info as $k => $v) {
                if (!$this->where(array('hmr_id' => $v['hmr_id'], 'company_id' => $this->company_id))->count('hmr_id')) {
                    $inventory_insert = array(
                        'hmr_id' => $v['hmr_id'],
                        'company_id' => $this->company_id,
                        'inventory_num ' => $v['purchase_num'],
                        'inventory_unit' => $v['purchase_unit'],
                        'inventory_trade_price' => $v['purchase_trade_price'],
                        'inventory_prescription_price' => $v['purchase_prescription_price'],
                        'inventory_trade_total_amount' => $v['purchase_trade_total_amount'],
                        'inventory_prescription_total_amount' => $v['purchase_prescription_total_amount'],
                        'update_time' => time()
                    );
                    $inventory_return = $this->add($inventory_insert);
                } else {
                    $inventory_info = $this
                        ->field('inventory_num,inventory_trade_total_amount,inventory_prescription_total_amount')
                        ->where(array('hmr_id' => $v['hmr_id'], 'company_id' => $this->company_id))
                        ->find();
                    $inventory_save = array(
                        'inventory_num ' => $inventory_info['inventory_num'] + $v['purchase_num'],
                        'inventory_unit' => $v['purchase_unit'],
                        'inventory_trade_price' => ($inventory_info['inventory_trade_total_amount'] + $v['purchase_trade_total_amount']) / ($inventory_info['inventory_num'] + $v['purchase_num']),
                        'inventory_prescription_price' => ($inventory_info['inventory_prescription_total_amount'] + $v['purchase_prescription_total_amount']) / ($inventory_info['inventory_num'] + $v['purchase_num']),
                        'inventory_trade_total_amount' => $inventory_info['inventory_trade_total_amount'] + $v['purchase_trade_total_amount'],
                        'inventory_prescription_total_amount' => $inventory_info['inventory_prescription_total_amount'] + $v['purchase_prescription_total_amount'],
                        'update_time' => time()
                    );
                    $inventory_return = $this->where(array('hmr_id' => $v['hmr_id'], 'company_id' => $this->company_id))->save($inventory_save);
                }
            }
            if ($inventory_return) {
                $this->commit();
                return $inventory_return;
            } else {
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * @param string $inventory_id
     * @param $data
     * @Name     adjust_price
     * @explain  根据条件对库存表进行修改
     * @author   zuochuanye
     * @Date     2017/11/08
     * @return bool
     */
    public function adjust_price($condition = [], $data)
    {
        $this->startTrans();
        if ($condition) {
            $info = $this->where($condition)->save($data);
            if ($info !== false) {
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
     * @param array $condition 查询条件
     * @param int $type        查询类型 0:收支概况 1:列表信息
     * @param int $page
     * @param int $pageoffset
     * @param int $pagesize
     * @Name     drugSalesStatistics
     * @explain  药品收支统计查询信息
     * @author   zuochuanye
     * @Date     2017/11/27
     * @return array|bool
     */
    public function drugSalesStatistics($condition = [], $type = 0)
    {
        $where = array(
            'i.company_id' => $this->company_id,
            'hcos.type_id' => 0,
            'cp.status'    => 1
        );
        if($condition) $where = array_merge($where, $condition);
        if($type == 1){
            $count_num = $this
                ->alias('i')
                ->join('__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = i.hmr_id')
                ->join('__HIS_MEDICINES__ me ON me.medicines_id = hmr.medicines_id')
                ->join('__HIS_CARE_ORDER_SUB__ hcos ON hcos.goods_id = i.hmr_id')
                ->join('__HIS_CARE_ORDER__ o ON o.id = hcos.fid')
                ->join('__HIS_CARE_PKG__ cp ON cp.id = hcos.pkg_id')
                ->where($where)
                ->group('me.medicines_name')
                ->select();
            $count = count($count_num);
            $pager = new_page($count,10,1);
            $pager_str   = $pager->showHis();
        }
        $field = "SUM(i.inventory_trade_total_amount) as trade_total_amount,SUM(hcos.amount) as amount,(SUM(hcos.amount)- SUM(i.inventory_trade_total_amount)) as profit";
        if($type == 1) $field.=",(FORMAT(SUM(hcos.amount)/SUM(hcos.num),2)) as price,me.medicines_class,me.medicines_name, me.conversion,me.unit,SUM(hcos.num) as num,(FORMAT(SUM(i.inventory_trade_total_amount)/SUM(hcos.num),2)) as trade_price";
        $info_sql = $this
            ->alias('i')
            ->field($field)
            ->join('__HIS_HOSPITAL_MEDICINES_RELATION__ hmr ON hmr.hmr_id = i.hmr_id')
            ->join('__HIS_MEDICINES__ me ON me.medicines_id = hmr.medicines_id')
            ->join('__HIS_CARE_ORDER_SUB__ hcos ON hcos.goods_id = i.hmr_id')
            ->join('__HIS_CARE_ORDER__ o ON o.id = hcos.fid')
            ->join('__HIS_CARE_PKG__ cp ON cp.id = hcos.pkg_id')
            ->where($where);
        if ($type == 1) {
            $list = $info_sql->group('me.medicines_name')->order("num DESC")->limit($pager->firstRow.','.$pager->listRows)->select();
            return array('page' => $pager->getPage() , 'list' => $list, 'count'=>$count, 'pager_str'=>$pager_str);
        } else {
            $info = $info_sql->select()[0];
            return $info ? $info : false;
        }
    }
}