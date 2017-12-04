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
 * 支付记录model
 * User: dingxiaoxin
 * Date: 2017/11/13
 */

/**
 * 统计Model
 * HisCarePkgModel
 * Author: zcy && doreen
 */
class HisCarePkgModel extends BaseModel
{
    /**
     * @param array $condition
     * @param int $page
     * @param int $pageoffset
     * @param int $pagesize
     * @param string $field
     * @Name     get_list
     * @explain  获取收支统计列表信息
     * @author   zuochuanye
     * @Date     2017/11/14
     * @return array
     */
    public function get_list($condition = [], $field = "*")
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $count_info = $this
            ->alias('p')
            ->field($field)
            ->join('__PATIENT__ a ON a.patient_id = p.patient_id')
            ->join('__HIS_MEMBER__ m ON m.uid = p.doctor_id')
            ->where($where)
            ->group('p.id')
            ->select();
        $count = count($count_info);
        $pager = new_page($count, 10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('p')
            ->field($field)
            ->join('__PATIENT__ a ON a.patient_id = p.patient_id')
            ->join('__HIS_MEMBER__ m ON m.uid = p.doctor_id')
            ->where($where)
            ->group('p.id')
            ->order('p.addtime DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        $payment_platform = array('xianjin', 'wechart', 'zhifubao');
        foreach ($list as $k => $v) {
            $list[$k]['age'] = birthday($v['birthday']);
            $list[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
            foreach ($payment_platform as $kk => $vv) {
                $where = array(
                    'pkg_id' => $v['id'],
                    'payment_platform' => $kk
                );
                $paylog = M('his_care_paylog')->field('pay_amount')->where($where)->find();
                $list[$k][$vv] = $paylog ? $paylog['pay_amount'] : 0;
            }
        }

        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    /**
     * @param array $condition
     * @param string $fields
     * @Name     getGeneralIncome
     * @explain 获取收入信息
     * @author   zuochuanye
     * @Date 2017/11/15
     * @return bool
     */
    public function getGeneralIncome($condition = [], $fields = '*')
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $info = $this
            ->alias('p')
            ->field($fields)
            ->join("__HIS_CARE_PAYLOG__ l ON l.pkg_id = p.id")
            ->where($where)
            ->find();
        return $info ? $info : false;
    }

    /**
     * @param array $condition
     * @param string $fields
     * @Name     getFee
     * @explain  获取就诊信息
     * @author   zuochuanye
     * @Date     2017/11/17
     * @return bool
     */
    public function getFee($condition = [], $fields = '')
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $info = $this->alias('p')
            ->join("__HIS_CARE_ORDER_SUB__ s ON s.pkg_id = p.id")
            ->where($where)
            ->field($fields)
            ->find();
        return $info ? $info : false;
    }
    /**
     * @param array $condition
     * @param string $fields
     * @Name     getregistratuionFee
     * @explain  获取挂号信息
     * @author   zuochuanye
     * @Date     2017/11/17
     */
    public function getregistrationFee($condition = [], $fields = '*')
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $info = $this->alias('p')
            ->join("__HIS_CARE_PAYLOG__ l ON l.pkg_id = p.id")
            ->join('__HIS_REGISTRATION__ r ON r.pkg_id = p.id')
            ->where($where)
            ->field($fields)
            //->group('p.id')
            ->find();
        return $info ? $info : false;
    }
    /**
     * @param array $condition
     * @param string $fields
     * @param string $join
     * @Name     getRefundInfo
     * @explain  获取退款信息
     * @author   zuochuanye
     * @Date     2017/11/16
     * @return bool
     */
    public function getRefundInfo($condition = [],$fields = "*",$extraJoin='')
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $join = "INNER JOIN __HIS_CARE_REFUNDLOG__ r ON r.pkg_id = p.id";
        $join = $join.$extraJoin;
        $info = $this
            ->alias('p')
            ->field($fields)
            ->join($join)
            ->where($where)
            ->find();
        return $info ? $info : false;
    }

    /**
     * 支付方式统计
     * @param array $condition
     * @param string $field
     * @return mixed
     * Author: doreen
     */
    public function getPayPlatformIncome($condition = [], $field = '*')
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $carePkg = D('his_care_pkg');
        $join = "JOIN __HIS_CARE_PAYLOG__ l ON l.pkg_id = p.id";
        $list = $carePkg->alias('p')
            ->join($join)
            ->where($where)
            ->field($field)
            ->find();
        return $list;
    }

    /**
     * 月度报表
     * @param int $hid
     * @param array $search
     * @param int $type
     * @return array
     * Author: doreen
     */
    public function getMonthlyReport($hid = 0, $search = [], $type = 1)
    {
        $where = array(
            'hospital_id' => $hid,
            'type_id' => array('in', '0,1'),
            'status' => array('in', '1,6'),
        );
        if($search) $where = array_merge($where, $search);
        $countWhere = array(
            'hospital_id' => $hid,
            'type_id' => 0,
            'status' => array('in', '0,1,6'),
        );
        $countWhere = array_merge($countWhere, $search);
        $carePkg = D('his_care_pkg');
        $result = $carePkg->where($where)->field('SUM(amount) as amount')->find(); //实收金额
        $count = $carePkg->where($countWhere)->count(); //就诊人数
        if ($type == 1) {
            $fee = $carePkg->where($where)->field('SUM(amount) as amount,type_id')->group('type_id')->select(); //挂号费、就诊费
            return array('count' => $count, 'result' => $result, 'fee' => $fee);
        }
        return array('count' => $count, 'result' => $result);
    }

    /**
     * 月度报表列表
     * @param int $hid
     * @param array $search
     * @return mixed
     * Author: doreen
     */
    public function getMonthlyReportList($hid = 0, $search = [])
    {
        $where = array(
            'p.hospital_id' => $hid,
            'p.type_id' => array('in', '0,1'),
            'p.status' => array('in', '1,6'),
            'l.status' => 1,
        );
        if($search) $where = array_merge($where, $search);
        $carePkg = D('his_care_pkg');
        $join = "JOIN __HIS_CARE_PAYLOG__ l ON l.pkg_id = p.id";
        $list = $carePkg->alias('p')
            ->join($join)
            ->where($where)
            ->field('SUM(l.pay_amount) as pay_amount,l.payment_platform')
            ->group('l.payment_platform')
            ->select();
        return $list;
    }

    /**
     * 月支出总金额
     * @param int $hid
     * @param array $search
     * @return mixed
     * Author: doreen
     */
    public function getPayAmount($hid = 0, $search = [])
    {
        $where = array(
            'company_id' => $hid,
        );
        if($search) $where = array_merge($where, $search);
        $batchesOfInventory = D('his_batches_of_inventory');
        $payAmount = $batchesOfInventory->where($where)->field('SUM(batches_of_inventory_total_money) as pay_amount')->find();
        return $payAmount;
    }

}