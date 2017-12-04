<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;
class SchedulingController extends HisBaseController
{
    protected $_scheduling;
    protected $_registeredfee;

    protected $company_id;//医院ID
    protected $_doctor_id;//当前用户ID
    public function __construct()
    {
        parent::__construct();
        C('TITLE', "排班管理");
        C('KEYEORDS', "");
        C('DESCRIPTION', "");
        $this->_scheduling = D('his_scheduling');
        $this->_registeredfee = D('his_registeredfee');

        $this->company_id = $this->hospitalInfo['uid'];
        $this->_doctor_id = $this->userInfo['uid'];
    }

    /**
     * @Name     Scheduling_list
     * @explain  排班展示页面
     * @author   zuochuanye
     * @Date     2017/10/23
     */
    public function Scheduling_list()
    {
        $status = I('get.status');
        $now_time = I('get.dates') ? I('get.dates') : time();
        if ($status == 'last_week') {
            $now_time = I('get.dates') ? I('get.dates') : time();
            $date = date('Y-m-d', $now_time - 7 * 24 * 3600);
        } elseif ($status == 'next_week') {
            $date = date('Y-m-d', $now_time + 7 * 24 * 3600);
        } else {
            $date = date('Y-m-d');
        }
        //星期
        $week = I('get.dates') ? date('w', strtotime($date)) : date('w', strtotime($date));
        //本周开始时间
        $now_start = date('Y-m-d', strtotime("$date -" . ($week ? $week - 1 : 6) . ' days'));
        //本周结束时间
        $now_end = date('Y-m-d', strtotime("$now_start +6 days"));
        //需要添加星期表中的数据
        $thisWeekTheDate = [];
        for ($start = 0; $start <= 6; $start++) {
            $thisWeekTheDate[$start]['date'] = date('Y/m/d', strtotime("$now_start +$start days"));
            $thisWeekTheDate[$start]['week'] = date('w', strtotime($thisWeekTheDate[$start]['date']));
        }
        $p_id = $this->userInfo['p_id'];
        if($p_id == 0){
            $doctor_condition = array('p_id' => $this->company_id, 'type' => 2);
        }else{
            $doctor_condition = array('m.uid' => $this->_doctor_id,'type'   => 2);
        }
        $doctor_list = M('his_member')->alias('m')->join("__HIS_DOCTOR__ d ON d.uid = m.uid")->where($doctor_condition)->select();
        foreach ($doctor_list as $d => &$t) {
            $department_info = M('his_department')->field('department_name')->where(array('did' => $t['department_id']))->find();
            $t['department_name'] = $department_info['department_name'];
        }
        if ($doctor_list) {
            $where['start_time_this_week'] = $now_start;
            foreach ($doctor_list as $k => $v){
                $where['physicianid'] = $v['uid'];
                $SchedulingNumber = $this->_scheduling->getSchedulingNumberByStartTimeThisWeek($where);
                if (!$SchedulingNumber) {
                    if (I('get.status') != 'last_week') {
                            //排班表中需要添加的数据
                            $scheduling_insert_info = array(
                                'physicianid' => $v['uid'],
                                'department_id' => $v['department_id'],
                                'company_id' => $this->company_id,
                                'start_time_this_week' => $now_start,
                                'end_time_this_week' => $now_end,
                            );
                            $this->_scheduling->addScheduling($scheduling_insert_info, $thisWeekTheDate);
                    }
                }
            }
            $alone_status = $p_id == 0 ? 1 : 2;
            $SchedulingInfo = $this->_scheduling->getSchedulingInfoByStartTimeThisWeek(array('start_time_this_week'=>$now_start),'',$alone_status);
            foreach ($SchedulingInfo['list'] as $key => &$value) {
                $doctor_info =  D("HisMember")->role_judgement($value['physicianid']);
                $value['username'] = $doctor_info ? $doctor_info : '';
                $department_info = M('his_department')->field('department_name')->where(array('did' => $value['department_id']))->find();
                $value['department_name'] = $department_info['department_name'] ? $department_info['department_name'] : '';
            }
            $this->assign('SchedulingInfo', $SchedulingInfo);
        }
        $this->assign('last_date', strtotime($date));
        $this->assign('alone_status', $alone_status);
        $this->assign('next_date', strtotime($date));
        $this->assign('now_start',$now_start);
        $this->display('schedulingList');
    }

    /**
     * @Name     export
     * @explain  导出
     * @author   zuochuanye
     * @Date     2017/11/27
     */
    public function export()
    {
        $start_time_this_week = I('get.start_time_this_week');
        $SchedulingInfo = $this->_scheduling->getSchedulingInfoByStartTimeThisWeek(array('start_time_this_week' => $start_time_this_week),'',$this->page, $this->pageoffset, $this->pagesize);
        foreach ($SchedulingInfo['list'] as $key => &$value) {
            $doctor_info =  D("HisMember")->role_judgement($value['physicianid']);
            $value['username'] = $doctor_info ? $doctor_info : '';
            $department_info = M('his_department')->field('department_name')->where(array('did' => $value['department_id']))->find();
            $value['department_name'] = $department_info['department_name'] ? $department_info['department_name'] : '';
        }
        $filename =date('Y-m-d',time()).'_排班信息';
        self::export_xls($SchedulingInfo['list'],$filename);
    }

    /**
     * @param array $list
     * @param string $filename
     * @Name     export_xls
     * @explain  导出信息
     * @author   zuochuanye
     * @Date     2017/11/27
     */
    public function export_xls($list = [], $filename = 'simple.xls')
    {
        if ($list) {
            ini_set('max_execution_time', '0');
            Vendor('PHPExcel.PHPExcel');
            $filename = str_replace('.xls', '', $filename) . '.xls';
            $phpexcel = new \PHPExcel();
            $PHPExcel_Style_Alignment = new \PHPExcel_Style_Alignment($phpexcel);
            $PHPExcel_Style_Border = new \PHPExcel_Style_Border($phpexcel);
            $phpexcel->getProperties()
                ->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
            //Excel表格式
            $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
            /*全局设置*/
            for ($l = 0; $l < count($letter); $l++) {
                $phpexcel->getActiveSheet()->getColumnDimension($letter[$l])->setWidth(18);
                $phpexcel->getActiveSheet()->getStyle($letter[$l])->getAlignment()->setHorizontal($PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            /*表头数组设置*/
            $tableheader = array('姓名', '科室', '日期/时间');
            foreach ($list[0]['subsection'][0]['week'] as $w => $k) {
                $str = $k['date'];
                switch ($k['week']) {
                    case 1:
                        $str .= ' 周一';
                        break;
                    case 2:
                        $str .= ' 周二';
                        break;
                    case 3:
                        $str .= ' 周三';
                        break;
                    case 4:
                        $str .= ' 周四';
                        break;
                    case 5:
                        $str .= ' 周五';
                        break;
                    case 6:
                        $str .= ' 周六';
                        break;
                    default:
                        $str .= ' 周日';
                        break;
                }
                array_push($tableheader, $str);
            }
            $phpexcel->getActiveSheet()->setTitle('Sheet1');
            $phpexcel->setActiveSheetIndex(0);
            for ($i = 0; $i < count($tableheader); $i++) {
                $phpexcel->getActiveSheet()->setCellValue("$letter[$i]1", "$tableheader[$i]");
                $phpexcel->getActiveSheet()->getStyle("$letter[$i]1")->getFont()->setBold(true);
            }
            /*数据重组*/
            $subsection_type_arr = [];
            for ($j = 0; $j < count($list); $j++) {
                $phpexcel->getActiveSheet()->mergeCells("$letter[0]" . (3 * $j + 2) . ":$letter[0]" . (3 * $j + 4));
                $phpexcel->getActiveSheet()->getStyle("$letter[0]" . (3 * $j + 2))->getAlignment()->setVertical($PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $phpexcel->getActiveSheet()->mergeCells("$letter[1]" . (3 * $j + 2) . ":$letter[1]" . (3 * $j + 4));
                $phpexcel->getActiveSheet()->getStyle("$letter[1]" . (3 * $j + 2))->getAlignment()->setVertical($PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $phpexcel->getActiveSheet()->setCellValue("$letter[0]" . (3 * $j + 2), $list[$j]['username']);
                $phpexcel->getActiveSheet()->setCellValue("$letter[1]" . (3 * $j + 2), $list[$j]['department_name']);
                for ($s = 0; $s < count($list[$j]['subsection']); $s++) {
                    if ($list[$j]['subsection'][$s]['subsection_type'] == 1) {
                        $subsection_type = '上午';
                    } elseif ($list[$j]['subsection'][$s]['subsection_type'] == 2) {
                        $subsection_type = '中午';
                    } elseif ($list[$j]['subsection'][$s]['subsection_type'] == 3) {
                        $subsection_type = '下午';
                    }
                    $subsection_type_arr['subsection_type'][] = $subsection_type;
                    $subsection_type_arr['week1'][] = $list[$j]['subsection'][$s]['week'][0]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][0]['registeredfee_name'] : '周一费用';
                    $subsection_type_arr['week2'][] = $list[$j]['subsection'][$s]['week'][1]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][1]['registeredfee_name'] : '周二费用';
                    $subsection_type_arr['week3'][] = $list[$j]['subsection'][$s]['week'][2]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][2]['registeredfee_name'] : '周三费用';
                    $subsection_type_arr['week4'][] = $list[$j]['subsection'][$s]['week'][3]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][3]['registeredfee_name'] : '周四费用';
                    $subsection_type_arr['week5'][] = $list[$j]['subsection'][$s]['week'][4]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][4]['registeredfee_name'] : '周五费用';
                    $subsection_type_arr['week6'][] = $list[$j]['subsection'][$s]['week'][5]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][5]['registeredfee_name'] : '周六费用';
                    $subsection_type_arr['week7'][] = $list[$j]['subsection'][$s]['week'][6]['registeredfee_name'] ? $list[$j]['subsection'][$s]['week'][6]['registeredfee_name'] : '周日费用';
                }

            }
            for ($su = 0; $su < count($subsection_type_arr['subsection_type']); $su++) {
                $phpexcel->getActiveSheet()->setCellValue("$letter[2]" . ($su + 2), $subsection_type_arr['subsection_type'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[3]" . ($su + 2), $subsection_type_arr['week1'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[4]" . ($su + 2), $subsection_type_arr['week2'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[5]" . ($su + 2), $subsection_type_arr['week3'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[6]" . ($su + 2), $subsection_type_arr['week4'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[7]" . ($su + 2), $subsection_type_arr['week5'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[8]" . ($su + 2), $subsection_type_arr['week6'][$su]);
                $phpexcel->getActiveSheet()->setCellValue("$letter[9]" . ($su + 2), $subsection_type_arr['week7'][$su]);
            }
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=$filename");
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $write = new \PHPExcel_Writer_Excel5($phpexcel);
            $write->save('php://output');
            exit;
        }
    }

    /**
     * @Name     editScheduling
     * @explain  修改
     * @author   zuochuanye
     * @Date    2017/10/24
     */
    public function Scheduling_edit()
    {
        if (IS_AJAX) {
            $scheduling_id = I('post.scheduling_id', '', 'intval');
            $registeredfee = $this->_registeredfee->getRegisteredfeeAllList($this->company_id, 'registeredfee_name,reg_id');
            $info = $this->_scheduling->getSchedulingInfoByScheduling_id(array('scheduling_id' => $scheduling_id));
            $info['username'] = D("HisMember")->role_judgement($info['physicianid']);
            $data = array(
                'registeredfee' => $registeredfee,
                'info' => $info
            );
            $info ? $this->ajaxSuccess('成功', $data) : $this->ajaxError('获取失败');
        }
    }

    /**
     * @Name     Scheduling_change
     * @explain  更改排班信息
     * @author   zuochuanye
     * @Date     2017/10/27
     */
    public function Scheduling_change()
    {
        if (IS_AJAX) {
            $scheduling_week_id = I('post.scheduling_week_id', '', 'intval');
            $reg_id = I('post.reg_id', '', 'intval');
            if ($scheduling_week_id && $reg_id) {
                $scheduling_week_edit_return = M('his_scheduling_week')->where(array('scheduling_week_id' => $scheduling_week_id))->save(array('registeredfee_id' => $reg_id));
                !is_bool($scheduling_week_edit_return) && $scheduling_week_edit_return ? $this->ajaxSuccess('修改成功') : $this->ajaxError('修改失败');
            } else {
                $this->ajaxError('修改失败');
            }
        }
    }
}