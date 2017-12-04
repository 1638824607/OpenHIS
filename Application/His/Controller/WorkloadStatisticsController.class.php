<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy && doreen
// +----------------------------------------------------------------------

namespace His\Controller;
/**
 * 工作量统计类
 * WorkloadStatisticsController
 * Author: zcy && doreen
 */
class WorkloadStatisticsController extends HisBaseController
{
    protected $company_id;
    protected $_pkg;
    public function _initialize()
    {
        parent::_initialize();
        $this->company_id  = $this->hospitalInfo['uid'];
        $this->_pkg = D('HisCarePkg');
    }

    /**
     * @Name     index
     * @explain  工作量统计显示
     * @author   zuochuanye
     * @Date     2017/11/27
     */
    public function index()
    {
        $this->display();
    }

    /**
     * @Name     getClinicFee
     * @explain   门诊费用统计
     * @author   zuochuanye
     * @Date    2017/11/16
     */
    public function getClinicFee()
    {
        if (IS_AJAX) {
            $name = I('search', '', 'htmlspecialchars');
            $startTime = !empty(I('startTime')) ? strtotime(date('Y-m-d 0:0:0', strtotime(I('startTime')))) : 0;
            $endTime = !empty(I('endTime')) ? strtotime(date('Y-m-d 23:59:59', strtotime(I('endTime')))) : time();
            $doctor_info = self::getClinicFeeInfo($name,$startTime,$endTime);
            $count = count($doctor_info);
            $pager = new_page($count, 10,1);
            $doctor_info = array_slice($doctor_info, $pager->firstRow, $pager->listRows);
            $pager_str = $pager->showHis();
            $info = [
                'count' => $count,
                'page' =>  $pager->getPage(),
                'pager_str' => $pager_str,
                'list' => $doctor_info,
            ];
            $this->ajaxSuccess('成功', $info);
        }
    }

    /**
     * @param $name
     * @param $startTime
     * @param $endTime
     * @Name     getClinicFeeInfo
     * @explain  获取门诊费用统计信息
     * @author   zuochuanye
     * @Date     2017/11/28
     * @return mixed
     */
    private function getClinicFeeInfo($name,$startTime,$endTime){
        //医生信息
        $member = D('his_member');
        $map['m.type'] = 2;
        $map['d.true_name'] = array('like', '%' . $name . '%');
        $field = "d.true_name,m.uid";
        $doctor_info = $member->getDoctorInfo($this->company_id, $map, $field);
        $managerField = "h.owner_name as true_name,m.uid";
        $where = array('h.hid'=>$this->company_id,'h.hospital_name'=>array('like','%'.$name.'%'));
        $managerInfo = M('his_hospital')->alias('h')
            ->join("LEFT JOIN __HIS_MEMBER__ m ON m.uid = h.hid")
            ->where($where)
            ->field($managerField)
            ->find();
        if($managerInfo) array_push($doctor_info,$managerInfo);
        foreach ($doctor_info as $k => &$v) {
            $v['user_name'] = $v['true_name'];
            $sub_condition = array(
                'p.doctor_id' => $v['uid'],
                'p.type_id'   => 0,
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            );
            //就诊人数
            $patients_fields = "count(id) as patients";
            $v['patients'] = M("his_care_pkg")->alias('p')->field($patients_fields)->where($sub_condition)->find()['patients'];
            //就诊退款
            $refund_condition = array(
                'p.status' => array('in', '4,7'),
                'r.status ' => 1,
                'p.type_id' => 0,
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            );
            $refund_where = array_merge($sub_condition, $refund_condition);
            $refund_fields = "SUM(r.refund_amount) as refundincome";
            $refundincome = $this->_pkg->getRefundInfo($refund_where, $refund_fields);
            $v['refundincome'] = $refundincome['refundincome'] ? $refundincome['refundincome'] : 0;
            //挂号退款
            $registration_refund_condition = array(
                'p.status' => array('in', '4,7'),
                'r.status ' => 1,
                'p.type_id' => 1,
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            );
            $registration_refund_where = array_merge($sub_condition, $registration_refund_condition);
            $registration_refund_fields = "SUM(r.refund_amount) as registration_refundincome";
            $registration_refundincome = $this->_pkg->getRefundInfo($registration_refund_where, $registration_refund_fields);
            $v['registration_refundincome'] = $registration_refundincome['registration_refundincome'] ? $registration_refundincome['registration_refundincome'] : 0;
            //就诊实收金额
            $income_condition = array(
                'p.status' => array('in', '1,6'),
                'p.type_id' => 0,
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            );
            $income_where = array_merge($sub_condition, $income_condition);
            $income_fields = "SUM(l.pay_amount) as income";
            $income = $this->_pkg->getGeneralIncome($income_where, $income_fields);
            $v['income'] = $income['income'] ? $income['income'] : 0;
            //挂号实收
            $registration_income_condition = array(
                'p.status' => array('in', '1,6'),
                'p.type_id' => 1,
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            );
            $registration_income_where = array_merge($sub_condition, $registration_income_condition);
            $registration_income_fields = "SUM(l.pay_amount) as registration_income";
            $registration_income = $this->_pkg->getGeneralIncome($registration_income_where, $registration_income_fields);
            $v['registration_income'] = $registration_income['registration_income'] ? $registration_income['registration_income'] : 0;
        }
        return $doctor_info;
    }
    /**
     * @Name     getDrugPurchase
     * @explain  获取挂号费统计
     * @author   zuochuanye
     * @Date     2017/11/17
     */
    public function getDrugPurchase()
    {
        if (IS_AJAX) {
            $name = I('search', '', 'htmlspecialchars');
            $startTime = !empty(I('startTime')) ? strtotime(date('Y-m-d 0:0:0', strtotime(I('startTime')))) : 0;
            $endTime = !empty(I('endTime')) ? strtotime(date('Y-m-d 23:59:59', strtotime(I('endTime')))) : time();
            $list = self::getDrugPurchaseInfo($name,$startTime,$endTime);
            $count = count($list['info']);
            $pager = new_page($count, 10,1);
            $list['info'] = array_slice($list['info'], $pager->firstRow, $pager->listRows);
            $pager_str = $pager->showHis();
            $info = [
                'count' => $count,
                'page' => $pager->getPage(),
                'pager_str' => $pager_str,
                'list' => $list,
            ];
            $this->ajaxSuccess('成功', $info);
        }

    }

    /**
     * @param $name
     * @param $startTime
     * @param $endTime
     * @Name     getDrugPurchaseInfo
     * @explain  获取挂号费用信息
     * @author   zuochuanye
     * @Date     2017/11/28
     * @return array
     */
    private function getDrugPurchaseInfo($name,$startTime,$endTime){
        //医生信息
        $member = D('his_member');
        $map['m.type'] = 2;
        $map['d.true_name'] = array('like', '%' . $name . '%');
        $field = "d.true_name,m.uid";
        $doctor_info = $member->getDoctorInfo($this->company_id, $map, $field);
        $managerField = "h.owner_name as true_name,m.uid";
        $where = array('h.hid'=>$this->company_id,'h.hospital_name'=>array('like','%'.$name.'%'));
        $managerInfo = M('his_hospital')->alias('h')
            ->join("LEFT JOIN __HIS_MEMBER__ m ON m.uid = h.hid")
            ->where($where)
            ->field($managerField)
            ->find();
        if($managerInfo) array_push($doctor_info,$managerInfo);
        $registeredfee = M('his_registeredfee')->field('reg_id,registeredfee_name')->where(array('company_id' => $this->company_id))->select();
        $list = array();
        foreach ($doctor_info as $d => $t) {
            foreach ($registeredfee as $k => $v) {
                $list['registeredfee'] = $registeredfee;
                $list['info'][$d]['user_name'] = $t['true_name'];
                $sub_condition = array(
                    'p.doctor_id' => $t['uid'],
                    'r.registeredfee_id' => $v['reg_id']
                );
                //未开处方
                $didNotOpen_condition = array(
                    'p.status' => 0,
                    'p.type_id' =>1,
                    'r.registration_status' => 1,
                    'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
                );
                $didNotOpen_fields = "COUNT(p.id) as didnotopen_count";
                $didNotOpen_condition = array_merge($didNotOpen_condition, $sub_condition);
                $didnotopen_count = $this->_pkg->getregistrationFee($didNotOpen_condition, $didNotOpen_fields);
                $list['info'][$d]['num'][$k]['didnotopen_count'] = $didnotopen_count['didnotopen_count'] ? $didnotopen_count['didnotopen_count'] : 0;
                //以开处方
                $hasBeenOpen_condition = array(
                    'p.status' => array("in", '1,6'),
                    'r.registration_status' => 2,
                    'p.type_id' =>1,
                    'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
                );
                $hasbeenopen_fields = "COUNT(p.id) as hasbeenopen_count";
                $hasBeenOpen_condition = array_merge($hasBeenOpen_condition, $sub_condition);
                $hasbeenopen_count = $this->_pkg->getregistrationFee($hasBeenOpen_condition, $hasbeenopen_fields);
                $list['info'][$d]['num'][$k]['hasbeenopen_count'] = $hasbeenopen_count['hasbeenopen_count'] ? $hasbeenopen_count['hasbeenopen_count'] : 0;
                //退货量
                $refund_condition = array(
                    'p.status' => array("in", '4,7'),
                    'r.registration_status' => 3,
                    'p.type_id' =>1,
                    'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
                );
                $refund_fields = "COUNT(p.id) as refund_count";
                $refund_condition = array_merge($refund_condition, $sub_condition);
                $refund_count = $this->_pkg->getregistrationFee($refund_condition, $refund_fields);

                $list['info'][$d]['num'][$k]['refund_count'] = $refund_count['refund_count'] ? $refund_count['refund_count'] : 0;
                //挂号量
                $registration_condition = array(
                    'p.status' => array("in", '1,6'),
                    'r.registration_status' => array('in', '1,2'),
                    'p.type_id' => 1,
                    'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
                );
                $registration_fields = "COUNT(p.id) as registration_count";
                $registration_condition = array_merge($registration_condition, $sub_condition);
                $registration_count = $this->_pkg->getregistrationFee($registration_condition, $registration_fields);
                $list['info'][$d]['num'][$k]['registration_count'] = $registration_count['registration_count'] ? $registration_count['registration_count'] : 0;
                //实收金额
                $realPrice_condition = array(
                    'p.status' => array("in", '1,6'),
                    'r.registration_status' => array('in', '1,2'),
                    'p.type_id' => 1,
                    'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
                );
                $realprice_fields = "SUM(l.pay_amount) as realprice_amount";
                $realPrice_condition = array_merge($realPrice_condition, $sub_condition);
                $realprice_amount = $this->_pkg->getregistrationFee($realPrice_condition, $realprice_fields);
                $list['info'][$d]['num'][$k]['realprice_amount'] = $realprice_amount['realprice_amount'] ? $realprice_amount['realprice_amount'] : 0;
            }
        }
        return $list;
    }
    /**
     * @Name     ClinicFee_export
     * @explain  门诊费用统计导出
     * @author   zuochuanye
     * @Date     2017/11/18
     */
    public function ClinicFee_export(){
        $name = I('search', '', 'htmlspecialchars');
        $startTime = !empty(I('startTime')) ? strtotime(date('Y-m-d 0:0:0', strtotime(I('startTime')))) : 0;
        $endTime = !empty(I('endTime')) ? strtotime(date('Y-m-d 23:59:59', strtotime(I('endTime')))) : time();
        $doctor_info = self::getClinicFeeInfo($name,$startTime,$endTime);
        //导出信息
        $export_info = array(array('医生','门诊数量','就诊实收','挂号实收','就诊退费','挂号退费'));
        $list = [];
       foreach ($doctor_info as $k => $v){
           $list[$k][] = $v['user_name'] ? $v['user_name'] : "0";
           $list[$k][] = $v['patients'] ? $v['patients'] : '0' ;
           $list[$k][] = $v['refundincome'] ? $v['refundincome'] : '0';
           $list[$k][] = $v['registration_refundincome'] ? $v['registration_refundincome'] : '0';
           $list[$k][] = $v['income'] ? $v['income'] : '0';
           $list[$k][] = $v['registration_income'] ? $v['registration_income'] : '0';
           array_push($export_info,$list[$k]);
       }
        $filename =date('Y-m-d',time()).'_门诊费用统计';
        create_xls($export_info,$filename);
    }

    /**
     * @Name     DrugPurchase_export
     * @explain  挂号费用导出
     * @author   zuochuanye
     * @Date     2017/11/18
     */
    public function DrugPurchase_export(){
        $name = I('search', '', 'htmlspecialchars');
        $startTime = !empty(I('startTime')) ? strtotime(date('Y-m-d 0:0:0', strtotime(I('startTime')))) : 0;
        $endTime = !empty(I('endTime')) ? strtotime(date('Y-m-d 23:59:59', strtotime(I('endTime')))) : time();
        $list = self::getDrugPurchaseInfo($name,$startTime,$endTime);
        //导出信息
        $export_info = [];
        //表头
        $tableheader = ['医生姓名/表类型'];
        //第二层
        $second_tableheader = [''];
        $type = array('未开处方','已开处方','退号量','挂号量','实收金额');
        foreach ($list['registeredfee'] as $k =>$v){
            foreach ($type as $t => $y){
                $second_tableheader[] = $y;
            }
            array_push($tableheader,$v['registeredfee_name'],'','','','');
        }
        array_push($export_info,$tableheader,$second_tableheader);
        //详细数据
        $details = [];
        foreach ($list['info'] as $i => $f){
            $details[$i][] = $f['user_name'];
            foreach ($f['num'] as $m => $n){
                $didnotopen_count = $n['didnotopen_count'] ? $n['didnotopen_count'] : '0';
                $hasbeenopen_count = $n['hasbeenopen_count'] ? $n['hasbeenopen_count'] : '0';
                $refund_count = $n['refund_count'] ? $n['refund_count'] : '0';
                $registration_count = $n['registration_count'] ? $n['registration_count'] : '0';
                $realprice_amount = $n['realprice_amount'] ? $n['realprice_amount'] : '0';
                array_push($details[$i],$didnotopen_count,$hasbeenopen_count,$refund_count,$registration_count,$realprice_amount);
            }
            array_push($export_info,$details[$i]);
        }
        $filename =date('Y-m-d',time()).'_挂号费用统计';
        create_xls($export_info,$filename);
    }

    /**
     * 门诊处方统计
     * Author: doreen
     */
    public function getCareOrderStatistics()
    {
        $name = I('name', '', 'htmlspecialchars');
        $action = I('action', '', 'htmlspecialchars');
        $startTime = !empty(I('startTime')) ? strtotime(date('Y-m-d 0:0:0', strtotime(I('startTime')))) : 0;
        $endTime = !empty(I('endTime')) ? strtotime(date('Y-m-d 23:59:59', strtotime(I('endTime')))) : time();
        //医生信息
        $member = D('his_member');
        $map['m.type'] = 2;
        $map['d.true_name'] = array('like', '%' . $name . '%');
        $field = "d.true_name,m.uid";
        $doctorInfo = $member->getDoctorInfo($this->company_id, $map, $field);
        $managerField = "h.owner_name as true_name,m.uid";
        $where = array('h.hid'=>$this->company_id,'h.hospital_name'=>array('like','%'.$name.'%'));
        $managerInfo = $member->getMyHospitalInfo($where,$managerField);
        if($managerInfo) array_push($doctorInfo,$managerInfo);
        foreach ($doctorInfo as $key => &$value) {
            //处方数
            $careOrder = D('his_care_order');
            $countCondition = [
                'doctor_id' => $value['uid'],
            ];
            $careOrderCount = $careOrder->where($countCondition)->count();
            $value['care_order_num'] = $careOrderCount;
            //实收金额
            $condition = [
                'p.doctor_id' => $value['uid'],
                'p.status' => array('in', '1,6'),
                'p.type_id' => 0,
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            ];
            $amountField = "SUM(l.pay_amount) as amount";
            $amount = $this->_pkg->getGeneralIncome($condition, $amountField)['amount'];
            $value['amount'] = $amount ? $amount : 0;
            if ($amount <= 0) {
                unset($doctorInfo[$key]);
                continue;
            }
            //中药处方金额
            $cMedicineCondition = [
                's.type_id' => 0,
            ];
            $cMedicineCondition = array_merge($condition, $cMedicineCondition);
            $cMedicineField = "SUM(s.amount) as cmedicine_amount";
            $cMedicineAmount = $this->_pkg->getFee($cMedicineCondition, $cMedicineField)['cmedicine_amount'];
            $value['cmedicine_amount'] = $cMedicineAmount ? $cMedicineAmount : 0;
            //附加费金额
            $extraCondition = [
                's.type_id' => 1,
            ];
            $extraCondition = array_merge($condition, $extraCondition);
            $extraField = "SUM(s.amount) as extra_fee";
            $extraFee = $this->_pkg->getFee($extraCondition, $extraField)['extra_fee'];
            $value['extra_fee'] = $extraFee ? $extraFee : 0;
            //检查项目金额
            $inspectionCondition = [
                's.type_id' => 2,
            ];
            $inspectionCondition = array_merge($condition, $inspectionCondition);
            $inspectionField = "SUM(s.amount) as inspection_fee";
            $inspectionFee = $this->_pkg->getFee($inspectionCondition, $inspectionField)['inspection_fee'];
            $value['inspection_fee'] = $inspectionFee ? $inspectionFee : 0;
            //退费金额
            $refundCondition = array(
                'p.doctor_id' => $value['uid'],
                'p.status' => array('in', '4,7'),
                'r.status ' => 1,
                'p.type_id' => 0,
                'r.addtime' => array(array('gt', date('Y-m-d H:i:s', $startTime)), array('elt', date('Y-m-d H:i:s',$endTime))),
            );
            $refundFields = "SUM(r.refund_amount) as refund_income";
            $refundIncome = $this->_pkg->getRefundInfo($refundCondition, $refundFields)['refund_income'];
            $value['refund_income'] = $refundIncome ? $refundIncome : 0;
            unset($value['uid']);
        }
        $count = count($doctorInfo);
        $pager = new_page($count, 10,1);
        $doctorInfo = array_slice($doctorInfo, $pager->firstRow, $pager->listRows);
        //导出excel
        if ($action == 'export') $this->exportCareOrderStatistics($doctorInfo);
        //页面显示数据
        if (IS_AJAX) {
            $pager_str = $pager->showHis();
            $data = [
                'count' => $count,
                'page' => $pager->getPage(),
                'pager_str' => $pager_str,
                'list' => $doctorInfo,
            ];

            $this->ajaxSuccess('请求成功', $data);
        }
    }

    /**
     * 收费员统计
     * Author: doreen
     */
    public function getCollectionStatistics()
    {
        $name = I('name', '', 'htmlspecialchars');
        $action = I('action', '', 'htmlspecialchars');
        $startTime = !empty(I('startTime')) ? strtotime(date('Y-m-d 0:0:0', strtotime(I('startTime')))) : 0;
        $endTime = !empty(I('endTime')) ? strtotime(date('Y-m-d 23:59:59', strtotime(I('endTime')))) : time();
        //收费员信息
        $member = D('his_member');
        $map['d.true_name'] = array('like', '%' . $name . '%');
        $field = "d.true_name,m.uid";
        $doctorInfo = $member->getDoctorInfo($this->company_id, $map, $field);
        $managerField = "h.owner_name as true_name,m.uid";
        $where = array('h.hid'=>$this->company_id,'h.hospital_name'=>array('like','%'.$name.'%'));
        $managerInfo = $member->getMyHospitalInfo($where,$managerField);
        if($managerInfo) array_push($doctorInfo,$managerInfo);
        foreach ($doctorInfo as $key => &$value) {
            //收费次数
            $condition = [
                'doctor_id' => $value['uid'],
                'status' => array('in', '1,6'),
                'type_id' => array('in', '0,1'),
                'addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            ];
            $chargeNum = $this->_pkg->where($condition)->count();
            $value['chargeNum'] = $chargeNum;
            //实收金额
            $amountCondition = [
                'p.doctor_id' => $value['uid'],
                'p.status' => array('in', '1,6'),
                'p.type_id' => array('in', '0,1'),
                'p.addtime' => array(array('gt', $startTime), array('elt', $endTime)),
            ];
            $amountField = "SUM(l.pay_amount) as amount";
            $amount = $this->_pkg->getGeneralIncome($amountCondition, $amountField)['amount'];
            $value['amount'] = $amount ? $amount : 0;
            if ($amount <= 0) {
                unset($doctorInfo[$key]);
                continue;
            }
            //现金收入
            $cashCondition = [
                'l.payment_platform' => 0,
            ];
            $cashCondition = array_merge($cashCondition, $amountCondition);
            $cashField = 'SUM(l.pay_amount) as cash_pay';
            $cashPay = $this->_pkg->getPayPlatformIncome($cashCondition, $cashField)['cash_pay'];
            $value['cashPay'] = $cashPay ? $cashPay : 0;
            //微信收入
            $wechatCondition = [
                'l.payment_platform' => 1,
            ];
            $wechatCondition = array_merge($wechatCondition, $amountCondition);
            $wechatField = 'SUM(l.pay_amount) as wechat_pay';
            $wechatPay = $this->_pkg->getPayPlatformIncome($wechatCondition, $wechatField)['wechat_pay'];
            $value['wechatPay'] = $wechatPay ? $wechatPay : 0;
            //支付宝收入
            $aliCondition = [
                'l.payment_platform' => 2,
            ];
            $aliCondition = array_merge($aliCondition, $amountCondition);
            $aliField = 'SUM(l.pay_amount) as ali_pay';
            $aliPay = $this->_pkg->getPayPlatformIncome($aliCondition, $aliField)['ali_pay'];
            $value['aliPay'] = $aliPay ? $aliPay : 0;
            unset($value['uid']);
        }
        $count = count($doctorInfo);
        $pager = new_page($count, 10,1);
        $doctorInfo = array_slice($doctorInfo, $pager->firstRow, $pager->listRows);
        //导出excel
        if ($action == 'export') $this->exportCollectionStatistics($doctorInfo);
        //页面显示数据
        if (IS_AJAX) {
            $pager_str = $pager->showHis();
            $data = [
                'count' => $count,
                'page' => $pager->getPage(),
                'pager_str' => $pager_str,
                'list' => $doctorInfo,
            ];
            $this->ajaxSuccess('请求成功', $data);
        }
    }

    /**
     * 门诊处方统计导出
     * @param $doctorInfo
     * Author: doreen
     */
    public function exportCareOrderStatistics($doctorInfo)
    {
        $title = [
            'true_name' => '医生姓名',
            'care_order_num' => '处方数量',
            'amount' => '实收金额',
            'cmedicine_amount' => '中药处方',
            'extra_fee' => '附加费用',
            'inspection_fee' => '检查项目',
            'refund_income' => '退费金额',
        ];
        array_unshift($doctorInfo, $title);
        $filename =date('Y-m-d',time()).'_门诊处方统计';
        create_xls($doctorInfo, $filename);
    }

    /**
     * 收费员统计导出
     * @param $doctorInfo
     * Author: doreen
     */
    public function exportCollectionStatistics($doctorInfo)
    {
        $title = [
            'true_name' => '医生姓名',
            'chargeNum' => '收费次数',
            'amount' => '实收金额',
            'cashPay' => '现金收入',
            'wechatPay' => '微信支付',
            'aliPay' => '支付宝支付',
        ];
        array_unshift($doctorInfo, $title);
        $filename =date('Y-m-d',time()).'_收费员统计';
        create_xls($doctorInfo, $filename);
    }
}