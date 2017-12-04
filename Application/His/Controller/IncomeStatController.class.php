<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;

class IncomeStatController extends HisBaseController
{
    protected $_pkg;

    protected $_company_id;//医院id
    protected $_operator_id;//登录人ID

    public function __construct()
    {
        parent::__construct();
        C('TITLE', '诊所收支统计');
        C('KEYEORDS', "");
        C('DESCRIPTION', "");
        $this->_pkg = D('HisCarePkg');
        $this->_company_id = $this->hospitalInfo['uid'];
        $this->_operator_id = $this->userInfo['uid'];
    }

    /**
     * @Name     index
     * @explain  诊所收支统计显示
     * @author   zuochuanye
     * @Date     2017/11/27
     */
    public function index()
    {
        $this->display();
    }

    /**
     * @Name     getIncomeList
     * @explain  获取收支统计列表
     * @author   zuochuanye
     * @Date     2017/11/14
     */
    public function getIncomeList()
    {
        if (IS_AJAX) {
            $condition = array('p.hospital_id' => $this->_company_id);
            $type_id = I('post.type_id', '');
            $status = I('post.status', '');
            $starttime = I('post.starttime', '');
            $endtime = I('post.endtime', '');
            if (!empty($type_id)) $condition['p.type_id'] = $type_id-1;
            if (is_numeric($status)) $condition['p.status'] = $status;
            if (!empty($starttime) || !empty($endtime)) {
                if (!empty($starttime) && !empty($endtime)) {
                    $condition["FROM_UNIXTIME(p.addtime,'%Y-%m-%d')"] = array('between', array($starttime, $endtime));
                } else {
                    $this->ajaxError('开始时间和结束时间必须同时选择');
                }
            }
            $fields = 'p.id,p.type_id,p.status,a.`name`,a.sex,a.birthday,p.amount,p.addtime,p.doctor_id';
            $info = $this->_pkg->get_list($condition, $fields);
            foreach ($info['list'] as $k => $v) {
                $info['list'][$k]['user_name'] = D("HisMember")->role_judgement($v['doctor_id']);
            }
            $info ? $this->ajaxSuccess('成功', $info) : $this->ajaxError('获取失败');
        }
    }

    /**
     * @Name     getIncomeInfo
     * @explain  收入信息数据
     * @author   zuochuanye
     * @Date     2017/11/14
     */
    public function getIncomeInfo()
    {
        if (IS_AJAX) {
            $condition = array('p.hospital_id' => $this->_company_id);
            $type_id = I('post.type_id', '');
            $status = I('post.status', '');
            $starttime = I('post.starttime', '');
            $endtime = I('post.endtime', '');
            if (!empty($type_id)) $condition['p.type_id'] = $type_id-1;
            if (is_numeric($status))$condition['p.status'] = $status;
            if (!empty($starttime) || !empty($endtime)) {
                if (!empty($starttime) && !empty($endtime)) {
                    $condition["FROM_UNIXTIME(p.addtime,'%Y-%m-%d')"] = array('between', array($starttime, $endtime));
                } else {
                    $this->ajaxError('开始时间和结束时间必须同时选择');
                }
            }
            $info['generalincome'] = self::getAllStat($condition);
            $info['general_situation'] = self::points($condition);
            $info['channel'] = self::ditch($condition);
            $this->ajaxSuccess('成功', $info);
        }
    }

    /**
     * @Name     getAllStat
     * @explain  获取合计收入
     * @author   zuochuanye
     * @Date     2017/11/14
     */
    private function getAllStat($where = [])
    {
        $condition = array('p.status' => array('in', '1,5'));
        if (!empty($where)) $condition = array_merge($condition, $where);
        $fields = "SUM(l.pay_amount) as generalIncome";
        $generalIncome = $this->_pkg->getGeneralIncome($condition, $fields);
        return $generalIncome['generalincome'] ? $generalIncome['generalincome'] : 0;
    }

    /**
     * @Name     points
     * @explain  收支概况
     * @author   zuochuanye
     * @Date      2017/11/14
     */
    private function points($where = [])
    {
        //收费金额
        $charge_condition = array('p.status' => array('in', '1,5'));
        if (!empty($where)) $charge_condition = array_merge($charge_condition, $where);
        $charge_fields = "SUM(l.pay_amount) as chargeincome";
        $chargeIncome = $this->_pkg->getGeneralIncome($charge_condition, $charge_fields);
        $general_situation['chargeincome'] = $chargeIncome['chargeincome'] ? $chargeIncome['chargeincome'] : 0;
        //退款金额
        $refund_condition = array(
            'p.status' => array('in', '4,7'),
            'r.status' => 1
        );
        if (!empty($where)) $refund_condition = array_merge($refund_condition, $where);
        $refund_fields = "SUM(r.refund_amount) as refundincome";
        $refundIncome = $this->_pkg->getRefundInfo($refund_condition, $refund_fields);
        $general_situation['refundincome'] = $refundIncome['refundincome'] ? $refundIncome['refundincome'] : 0;
        return $general_situation;
    }

    /**
     * @Name     ditch
     * @explain  收入渠道
     * @author   zuochuanye
     * @Date     2017/11/14
     */
    private function ditch($where = [])
    {
        //微信收入
        $wechart_condition = array(
            'p.status' => array('in', '1,5'),
            'l.payment_platform' => 1
        );
        if (!empty($where)) $wechart_condition = array_merge($wechart_condition, $where);
        $wechart_fields = "SUM(l.pay_amount) as wechartincome";
        $wechartincome = $this->_pkg->getGeneralIncome($wechart_condition, $wechart_fields);
        $channel['wechartincome'] = $wechartincome['wechartincome'] ? $wechartincome['wechartincome'] : 0;
        //现金收入
        $cash_condition = array(
            'p.status' => array('in', '1,5'),
            'l.payment_platform' => 0
        );
        if (!empty($where)) $cash_condition = array_merge($cash_condition, $where);
        $cash_fields = "SUM(l.pay_amount) as cashincome";
        $cashincome = $this->_pkg->getGeneralIncome($cash_condition, $cash_fields);
        $channel['cashincome'] = $cashincome['cashincome'] ? $cashincome['cashincome'] : 0;
        //支付宝
        $alipay_condition = array(
            'p.status' => array('in', '1,5'),
            'l.payment_platform' => 2
        );
        if (!empty($where)) $alipay_condition = array_merge($alipay_condition, $where);
        $alipay_fields = "SUM(l.pay_amount) as alipayincome";
        $alipayincome = $this->_pkg->getGeneralIncome($alipay_condition, $alipay_fields);
        $channel['alipayincome'] = $alipayincome['alipayincome'] ? $alipayincome['alipayincome'] : 0;
        //现金退款
        $cashrefund_condition = array(
            'p.status' => array('in', '4,7'),
            'r.payment_platform' => 0,
            'r.status ' => 1
        );
        if (!empty($where)) $cashrefund_condition = array_merge($cashrefund_condition, $where);
        $cashrefund_fields = "SUM(r.refund_amount) as cashrefundincome";
        $cashrefund = $this->_pkg->getRefundInfo($cashrefund_condition, $cashrefund_fields);
        $channel['cashrefundincome'] = $cashrefund['cashrefundincome'] ? $cashrefund['cashrefundincome'] : 0;
        //微信退款
        $wechartrefund_condition = array(
            'p.status' => array('in', '4,7'),
            'r.payment_platform' => 1,
            'r.status ' => 1
        );
        if (!empty($where)) $wechartrefund_condition = array_merge($wechartrefund_condition, $where);

        $wechartrefund_fields = "SUM(r.refund_amount) as wechartrefundincome";
        $wechartrefund = $this->_pkg->getRefundInfo($wechartrefund_condition, $wechartrefund_fields);
        $channel['wechartrefundincome'] = $wechartrefund['wechartrefundincome'] ? $wechartrefund['wechartrefundincome'] : 0;
        //支付宝退款
        $alipayrefund_condition = array(
            'p.status' => array('in', '4,7'),
            'r.payment_platform' => 2,
            'r.status ' => 1
        );
        if (!empty($where)) $alipayrefund_condition = array_merge($alipayrefund_condition, $where);

        $alipayrefund_fields = "SUM(r.refund_amount) as alipayrefundincome";
        $alipayrefund = $this->_pkg->getRefundInfo($alipayrefund_condition, $alipayrefund_fields);
        $channel['alipayrefundincome'] = $alipayrefund['alipayrefundincome'] ? $alipayrefund['alipayrefundincome'] : 0;
        return $channel;
    }

    /**
     * @Name     export
     * @explain  导出
     * @author   zuochuanye
     * @Date     2017/11/15
     */
    public function export()
    {
        $condition = array(
            'p.hospital_id' => $this->_company_id
        );
        $type_id = I('get.type_id', '');
        $status = I('get.status', '');
        $starttime = I('get.starttime', '');
        $endtime = I('get.endtime', '');
        if (!empty($type_id)) $condition['p.type_id'] = $type_id-1;
        if (is_numeric($status)) $condition['p.status'] = $status;
        if (!empty($starttime) || !empty($endtime)) {
            if (!empty($starttime) && !empty($endtime)) {
                $condition["FROM_UNIXTIME(p.addtime,'%Y-%m-%d')"] = array('between', array($starttime, $endtime));
            } else {
                $this->ajaxError('开始时间和结束时间必须同时选择');
            }
        }
        $fields = 'p.id,p.type_id,p.status,a.`name`,a.sex,a.birthday,p.amount,p.addtime,m.user_name';
        $info = $this->_pkg->get_list($condition, $fields);
        $export_info = array(
            array('类型', '状态', '姓名', '年龄', '应收', '现金', '微信', '支付宝', '日期', '收费员')
        );
        $pay_status = C('ORDER_STATUS');
        $type_id_array = array('就诊','挂号');
        foreach ($info['list'] as $k => $v) {
            $export_info[$k + 1] = [
                $type_id_array[$v['type_id']],
                $pay_status[$v['status']],
                $v['name'],
                $v['age'] ? $v['age'] : 0,
                $v['amount'] ? $v['amount'] : '0',
                $v['xianjin'] ? $v['xianjin'] : '0',
                $v['wechart'] ? $v['wechart'] : '0',
                $v['zhifubao'] ? $v['zhifubao'] : '0',
                $v['addtime'],
                $v['user_name']
            ];
        }
        $fileename = date('Y-m-d', time()) . '_诊所收支';
        create_xls($export_info, $fileename);
    }
}