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
 * 年月度报表统计
 * ReportStatisticsController
 * Author: doreen
 */
class ReportStatisticsController extends HisBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 月度报表统计
     * Author: doreen
     */
    public function monthlyReport()
    {
        $hid = $this->hospitalInfo['uid'];
        $action = !empty(I('action')) ? I('action') : '';
        $year = date('Y', time());
        //搜索条件开始结束时间，开始时间默认当年01月01号，结束时间默认当前时间
        $startTime = !empty(I('startTime')) ? strtotime(I('startTime')) : strtotime($year.'-01-01');
        $endTime = !empty(I('endTime')) ? strtotime(I('endTime')) : time();
        $monArr = getMonth($startTime, $endTime); //月数数组
        $amountArr = []; //每月实收数组
        $reportList = []; //报表列表数据
        foreach ($monArr as $key => $value) {
            $monthStartTime = strtotime($value.'/01');
            $monthEndTime = strtotime('+1 month', $monthStartTime);
            $search['addtime'] = array(array('egt', $monthStartTime), array('lt', $monthEndTime));
            $searchContent['p.addtime'] = array(array('egt', $monthStartTime), array('lt', $monthEndTime));
            $carePkg = D('his_care_pkg');
            $monthlyReport = $carePkg->getMonthlyReport($hid, $search); //总收入
            $monthlyReportList = $carePkg->getMonthlyReportList($hid, $searchContent); //各支付方式收款
            $amountArr[$key] = $monthlyReport['result']['amount'] ? $monthlyReport['result']['amount'] : 0;
            $reportList['list'][$key]['month'] = $value;
            $reportList['list'][$key]['visitedNum'] = $monthlyReport['count'];
            $reportList['list'][$key]['amount'] = $monthlyReport['result']['amount'] ? $monthlyReport['result']['amount'] : 0;
            $reportList['list'][$key]['cashPay'] = 0;
            $reportList['list'][$key]['wechatPay'] = 0;
            $reportList['list'][$key]['aliPay'] = 0;
            $reportList['list'][$key]['registeredFee'] = 0;
            $reportList['list'][$key]['visitedFee'] = 0;
            foreach ($monthlyReportList as $k => $v) {
                $reportList['list'][$key]['cashPay'] = $v['payment_platform'] == 0 ? $v['pay_amount'] : $reportList['list'][$key]['cashPay'];
                $reportList['list'][$key]['wechatPay'] = $v['payment_platform'] == 1 ? $v['pay_amount'] : $reportList['list'][$key]['wechatPay'];
                $reportList['list'][$key]['aliPay'] = $v['payment_platform'] == 2 ? $v['pay_amount'] : $reportList['list'][$key]['aliPay'];
            }
            foreach ($monthlyReport['fee'] as $k => $v) {
                $reportList['list'][$key]['registeredFee'] = $v['type_id'] == 1 ? $v['amount'] : $reportList['list'][$key]['registeredFee'];
                $reportList['list'][$key]['visitedFee'] = $v['type_id'] == 0 ? $v['amount'] : $reportList['list'][$key]['visitedFee'];
            }
        }
        //导出excel
        if ($action == 'export') {
            $this->exportMonthlyReport($reportList['list']);
        }
        //返回数据，显示页面
        if (IS_AJAX) {
            $count = count($reportList['list']);
            $pager = new_page($count, 10, 1);
            $pager_str   = $pager->showHis();
            $reportList['list'] = array_slice($reportList['list'], $pager->firstRow, $pager->listRows);
            $reportList['count'] = $count;
            $reportList['page'] = $pager->getPage();
            $reportList['pager_str'] = $pager_str;
            $data = [
                'month' => $monArr,
                'amount' => $amountArr,
                'reportList' => $reportList,
                'startTime' => date('Y-m-d', $startTime),
                'endTime' => date('Y-m-d', $endTime),
            ];
            $this->ajaxSuccess('请求成功',$data);
        }
        $this->display();
    }

    /**
     * 年度报表统计
     * Author: doreen
     */
    public function yearReport()
    {
        $hid = $this->hospitalInfo['uid'];
        $action = !empty(I('action')) ? I('action') : '';
        $year = date('Y', time());
        //搜索条件开始结束时间，开始时间默认当年01月01号，结束时间默认当前时间
        $startTime = !empty(I('startTime')) ? strtotime(I('startTime')) : strtotime($year . '-01-01');
        $endTime = !empty(I('endTime')) ? strtotime(I('endTime')) : time();
        if (date('Y', $startTime) != date('Y', $endTime)) $this->ajaxError('不可以跨年');
        $monArr = getMonth($startTime, $endTime); //月数数组
        //年度总收入
        $startYear = strtotime(date('Y', $startTime) . '-01-01');
        $endYear = strtotime('+1 year', $startYear);
        $searchContent['addtime'] = array(array('egt', $startYear), array('lt', $endYear));
        $carePkg = D('his_care_pkg');
        $yearAmount = $carePkg->getMonthlyReport($hid, $searchContent, 2)['result']['amount'];
        $total = $this->getTotal($hid,$startTime,$endTime,$yearAmount);
        $amountArr = []; //各月实收金额数组
        $reportList = []; //报表列表数据
        foreach ($monArr as $key => $value) {
            $monthStartTime = strtotime($value . '/01');
            $monthEndTime = strtotime('+1 month', $monthStartTime);
            $search['addtime'] = array(array('egt', $monthStartTime), array('lt', $monthEndTime));
            $searchPay['create_time'] = array(array('egt', $monthStartTime), array('lt', $monthEndTime));
            $monthlyReport = $carePkg->getMonthlyReport($hid, $search, 2); //总收入
            $monthlyPay = $carePkg->getPayAmount($hid, $searchPay);
            $amountArr[$key] = $monthlyReport['result']['amount'] ? $monthlyReport['result']['amount'] : 0;
            $reportList['list'][$key]['month'] = $value;
            $reportList['list'][$key]['visitedNum'] = $monthlyReport['count'];
            $reportList['list'][$key]['amount'] = $monthlyReport['result']['amount'] ? $monthlyReport['result']['amount'] : 0;
            $reportList['list'][$key]['payAmount'] = $monthlyPay['pay_amount'] ? $monthlyPay['pay_amount'] : 0;
            $reportList['list'][$key]['proportion'] = $yearAmount ? round(($reportList['list'][$key]['amount'] / $yearAmount) * 100).'%' : '0%';
        }
        array_unshift($reportList['list'], $total);
        //导出excel
        if ($action == 'export') {
            $this->exportYearReport($reportList['list']);
        }
        //返回数据，显示页面
        if (IS_AJAX) {
            $count = count($reportList['list']);
            $pager = new_page($count, 13, 1);
            $pager_str = $pager->showHis();
            $reportList['list'] = array_slice($reportList['list'], $pager->firstRow, 13);
            $reportList['count'] = $count;
            $reportList['page'] = $pager->getPage();
            $reportList['pager_str'] = $pager_str;
            $data = [
                'month' => $monArr,
                'amount' => $amountArr,
                'reportList' => $reportList,
                'startTime' => date('Y-m-d', $startTime),
                'endTime' => date('Y-m-d', $endTime),
            ];
            $this->ajaxSuccess('请求成功', $data);
        }
        $this->display();
    }

    /**
     * 月度报表导出
     * @param $reportList
     * Author: doreen
     */
    public function exportMonthlyReport($reportList)
    {
        $title = [
            'month'         => '月度',
            'visitedNum'    => '就诊人次',
            'amount'        => '实收金额',
            'cashPay'       => '现金支付',
            'wechatPay'     => '微信支付',
            'aliPay'        => '支付宝支付',
            'registeredFee' => '挂号费',
            'visitedFee'    => '就诊费',
        ];
        array_unshift($reportList, $title);
        $fileName =date('Y-m-d',time()).'_月度统计报表';
        create_xls($reportList,$fileName);
    }

    /**
     * 年月度报表导出
     * @param $reportList
     * Author: doreen
     */
    public function exportYearReport($reportList)
    {
        $title = [
            'month'         => '月份',
            'visitedNum'    => '就诊人次',
            'amount'        => '实收金额',
            'payAmount'     => '支出金额',
            'proportion'    => '所占年度比例',
        ];
        array_unshift($reportList, $title);
        $fileName =date('Y-m-d',time()).'_年度统计报表';
        create_xls($reportList,$fileName);
    }

    /**
     * 合计记录
     * @param $hid
     * @param $startTime
     * @param $endTime
     * @param $yearAmount
     * @return array
     * Author: doreen
     */
    public function getTotal($hid, $startTime, $endTime, $yearAmount)
    {
        //合计
        $carePkg = D('his_care_pkg');
        $startMonth = strtotime(date('Y-m', $startTime) . '-01');
        $endMonth = strtotime(date('Y-m',strtotime('+1 month', $endTime)).'-01');
        $monthAmountSearch['addtime'] =  array(array('egt', $startMonth), array('lt', $endMonth));
        $monthPayAmountSearch['create_time'] = array(array('egt', $startMonth), array('lt', $endMonth));
        $monthAmount = $carePkg->getMonthlyReport($hid, $monthAmountSearch, 2); //总收入
        $monthPayAmount = $carePkg->getPayAmount($hid, $monthPayAmountSearch);
        $total = [
            'month' => '合计',
            'visitedNum' => $monthAmount['count'],
            'amount' => $monthAmount['result']['amount'] ? $monthAmount['result']['amount'] : 0,
            'payAmount' => $monthPayAmount['pay_amount'] ? $monthPayAmount['pay_amount'] : 0,
            'proportion' => $yearAmount ? round(($monthAmount['result']['amount'] / $yearAmount) * 100).'%' : 0,
        ];
        return $total;
    }

}