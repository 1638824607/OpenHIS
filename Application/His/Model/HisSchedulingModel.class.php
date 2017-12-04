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

class HisSchedulingModel extends BaseModel
{
    protected $_hospitalInfo;//医院信息
    protected $_userInfo;//用户信息
    protected $company_id;//医院ID
    protected $_doctor_id;//用户ID
    public function __construct()
    {
        parent::__construct();
        $this->_hospitalInfo = session('hospital_info');
        $this->_userInfo = session('user_info');
        $this->company_id = $this->_hospitalInfo['uid'];
        $this->_doctor_id = $this->_userInfo['uid'];
    }
    //自动验证
    protected $_validate = array(
        array('physicianid', 'require', '科室不能为空', self::EXISTS_VALIDATE),
        array('department_id', 'require', '医生不能为空！', self::EXISTS_VALIDATE),
        array('company_id', 'require', '诊所不能为空！', self::EXISTS_VALIDATE),
        array('start_time_this_week', 'require', '本周开始时间不能为空！', self::EXISTS_VALIDATE),
        array('end_time_this_week', 'require', '本周结束时间不能为空！', self::EXISTS_VALIDATE),
    );
    //自动完成
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
    );

    /**
     * @param array $condition 查询条件
     * @param string $field    查询字段
     * @param int $page
     * @param int $pageoffset
     * @param int $pagesize
     * @param int $alone_status
     * @Name     getSchedulingInfoByStartTimeThisWeek
     * @explain  根据本周开始时间获取排班信息
     * @author   zuochuanye
     * @Date     2017/11/27
     * @return array
     */
    public function getSchedulingInfoByStartTimeThisWeek($condition = [], $field = '', $alone_status=1)
    {
        $where = array(
            'company_id' => $this->company_id
        );
        if($alone_status == 2) $where['physicianid'] = $this->_doctor_id;
        if($condition) $where = array_merge($where, $condition);
        $count = self::getSchedulingNumberByStartTimeThisWeek($condition);
        $pager       = new_page($count,10,1);
        $pager_str   = $pager->showes();
        $list = $this
            ->field($field)
            ->where($where)
            ->order('create_time DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        foreach ($list as $l => $t) {
            $list[$l]['subsection'] = M('his_scheduling_subsection')->where(array('scheduling_id' => $t['scheduling_id']))->select();
            foreach ($list[$l]['subsection'] as $k => $v) {
                $list[$l]['subsection'][$k]['week'] = M('his_scheduling_week')->where(array('scheduling_subsection_id' => $v['scheduling_subsection_id']))->select();
                foreach ($list[$l]['subsection'][$k]['week'] as $key => $value) {
                    if (!empty($value['registeredfee_id'])) {
                        $registeredfee_info = M('his_registeredfee')->field('reg_id,registeredfee_name')->where(array('reg_id' => $value['registeredfee_id']))->find();
                        $list[$l]['subsection'][$k]['week'][$key]['registeredfee_name'] = $registeredfee_info['registeredfee_name'];
                        $list[$l]['subsection'][$k]['week'][$key]['reg_id'] = $registeredfee_info['reg_id'];
                    }
                }
            }
        }
        return array('page' => $pager->getPage() , 'list' => $list, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     * @param string $condition 查询条件
     * @Name        getSchedulingNumberByStartTimeThisWeek
     * @explain     通过开始时间获取数据的数量
     * @author      zuochuanye
     * @Date        2017/10/24
     * @return mixed
     */
    public function getSchedulingNumberByStartTimeThisWeek($condition = '')
    {
        $where = array(
            'company_id' => $this->company_id
        );
        if($condition) $where = array_merge($where, $condition);
        return $this->where($where)->count('scheduling_id');
    }

    /**
     * @param $data     需要添加的数据
     * @Name            addScheduling
     * @explain         添加排班
     * @author          zuochuanye
     * @Date            2017/10/24
     * @return bool|mixed
     */
    public function addScheduling($data, $week_data)
    {
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        if (!$data = $this->create($data)) {
            return false;
        } else {
            $scheduling_id = $this->add($data);
            //时段信息，上午：1，下午：2，晚上：3
            $subsection = array(1, 2, 3);
            foreach ($subsection as $s => $i) {
                //时段中需要添加的信息
                $subsection_insert_info = array(
                    'subsection_type' => $i,
                    'scheduling_id' => $scheduling_id
                );
                $scheduling_subsection_id = M('his_scheduling_subsection')->add($subsection_insert_info);
                foreach ($week_data as $w => $e) {
                    //日期中需要添加的数据
                    $week_inssert_info = array(
                        'date' => $e['date'],
                        'week' => $e['week'],
                        'scheduling_subsection_id' => $scheduling_subsection_id
                    );
                    $scheduling_week_id = M('his_scheduling_week')->add($week_inssert_info);
                }
            }
            if ($scheduling_week_id) {
                $this->commit();
                return $scheduling_id;
            } else {
                $this->rollback();
                return false;
            }

        }
    }

    /**
     * @param array $condition 查询条件
     * @param string $field 查询字段
     * @Name     getSchedulingInfoByScheduling_id
     * @explain     通过Scheduling_id获取信息
     * @author   zuochuanye
     * @Date        2017/10/24
     * @return bool
     */
    public function getSchedulingInfoByScheduling_id($condition = [], $field = '')
    {
        $where = array(
            'company_id' => $this->company_id
        );
        if($condition) $where = array_merge($where, $condition);
        $info = $this
            ->alias('s')
            ->field($field)
            ->where($where)
            ->find();
        $info['subsection'] = M('his_scheduling_subsection')->where(array('scheduling_id' => $info['scheduling_id']))->select();
        foreach ($info['subsection'] as $k => $v) {
            $info['subsection'][$k]['week'] = M('his_scheduling_week')->where(array('scheduling_subsection_id' => $v['scheduling_subsection_id']))->select();
        }
        return $info ? $info : false;
    }

    /**
     * @param string $where 条件
     * @param string $field 需要查询的字段
     * @param int $num 查找的条数
     * @Name     getRegistrationList
     * @explain  获取挂号费用列表
     * @author   zuochuanye
     * @Date     2017/10/25
     * @return array
     */
    public function getSchedulingList($where = array(), $field = '*')
    {
       $count = $this
            ->alias('s')
            ->join('__HIS_MEMBER__ m ON m.uid = s.physicianid')
            ->join("__HIS_DOCTOR__ de ON de.uid = m.uid")
            ->join('__HIS_DEPARTMENT__ d ON d.did = s.department_id')
            ->join('__HIS_SCHEDULING_SUBSECTION__ su ON su.scheduling_id = s.scheduling_id')
            ->join('__HIS_SCHEDULING_WEEK__ sw ON sw.scheduling_subsection_id = su.scheduling_subsection_id')
            ->join('__HIS_REGISTEREDFEE__ r ON r.reg_id = sw.registeredfee_id')
            ->where($where)
            ->count();
        $pager = new_page($count, 10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('s')
            ->field($field)
            ->join('__HIS_MEMBER__ m ON m.uid = s.physicianid')
            ->join("__HIS_DOCTOR__ de ON de.uid = m.uid")
            ->join('__HIS_DEPARTMENT__ d ON d.did = s.department_id')
            ->join('__HIS_SCHEDULING_SUBSECTION__ su ON su.scheduling_id = s.scheduling_id')
            ->join('__HIS_SCHEDULING_WEEK__ sw ON sw.scheduling_subsection_id = su.scheduling_subsection_id')
            ->join('__HIS_REGISTEREDFEE__ r ON r.reg_id = sw.registeredfee_id')
            ->where($where)
            ->order('sw.date DESC,su.subsection_type')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    /**
     * @param array $condition
     * @param string $field
     * @Name     getSchedulingInfo
     * @explain  获取排班信息
     * @author   zuochuanye
     * @Date     2017/10/25
     * @return bool
     */
    public function getSchedulingInfo($condition = [], $field = '')
    {
        $list = $this
            ->alias('s')
            ->field($field)
            ->join('__HIS_MEMBER__ m ON m.uid = s.physicianid')
            ->join('__HIS_DEPARTMENT__ d ON d.did = s.department_id')
            ->join('__HIS_SCHEDULING_SUBSECTION__ su ON su.scheduling_id = s.scheduling_id')
            ->join('__HIS_SCHEDULING_WEEK__ sw ON sw.scheduling_subsection_id = su.scheduling_subsection_id')
            ->where($condition)
            ->find();
        return $list ? $list : false;
    }
}