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

class HisRegistrationModel extends BaseModel
{
    protected $_hospitalInfo;//医院信息
    protected $company_id;   //医院i d
    public function __construct()
    {
        parent::__construct();
        $this->_hospitalInfo = session('hospital_info');
        $this->company_id = $this->_hospitalInfo['uid'];
    }
    //自动验证
    protected $_validate = array(
        array('patient_id', 'require', '', self::EXISTS_VALIDATE),
        array('physician_id', 'require', '', self::EXISTS_VALIDATE),
        array('company_id', 'require', '', self::EXISTS_VALIDATE),
        array('registration_amount', 'require', '', self::EXISTS_VALIDATE),
    );
    //自动完成
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
    );

    /**
     * @param array $condition 条件
     * @param string $field 查询字段
     * @param int $num 数量
     * @Name     getRegistrationList
     * @explain  获取挂号列表
     * @author   zuochuanye
     * @Date     2017/10/25
     * @return array
     */
    public function getRegistrationList($condition = [], $field = '')
    {
        $where = array(
            'r.company_id' => $this->company_id
        );
        if($condition) $where = array_merge($where, $condition);
        $count = $this
            ->alias('r')
           ->join("__HIS_DOCTOR__ da ON da.uid = r.physician_id")
            ->join('__HIS_DEPARTMENT__ AS d ON d.did = r.department_id')
            ->join('__PATIENT__ AS p ON p.patient_id = r.patient_id')
            ->join('__HIS_REGISTEREDFEE__ AS re ON re.reg_id = r.registeredfee_id')
            ->where($where)
            ->count();
        $page = new_page($count, 10,1);
        $show = $page->showes();
        $list = $this
            ->alias('r')
            ->field($field)
            ->join("__HIS_DOCTOR__ da ON da.uid = r.physician_id")
            ->join('__HIS_DEPARTMENT__ AS d ON d.did = r.department_id')
            ->join('__PATIENT__ AS p ON p.patient_id = r.patient_id')
            ->join('__HIS_REGISTEREDFEE__ AS re ON re.reg_id = r.registeredfee_id')
            ->where($where)
            ->order('r.create_time DESC')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        return array('page' => $show, 'list' => $list, 'count' => $count);
    }
    /**
     * @param array $condition 条件
     * @param string $field 查找字段
     * @Name     getRegistrationInfo
     * @explain   获取挂号列表的对应信息
     * @author   zuochuanye
     * @Date     2017/10/31
     * @return bool
     */
    public function getRegistrationInfo($condition = [], $field = '')
    {
        $where = array(
            'r.company_id' => $this->company_id
        );
        if($condition) $where = array_merge($where, $condition);
        $info = $this
            ->alias('r')
            ->field($field)
           ->join("__HIS_DOCTOR__ da ON da.uid = r.physician_id")
            ->join('__HIS_DEPARTMENT__ AS d ON d.did = r.department_id')
            ->join('__PATIENT__ AS p ON p.patient_id = r.patient_id')
            ->join('__HIS_REGISTEREDFEE__ AS re ON re.reg_id = r.registeredfee_id')
            ->join('__HIS_SCHEDULING__ AS s ON s.scheduling_id = r.scheduling_id')
            ->join('__HIS_SCHEDULING_SUBSECTION__ AS su ON su.scheduling_subsection_id = r.scheduling_subsection_id')
            ->join('__HIS_SCHEDULING_WEEK__ AS sw ON sw.scheduling_subsection_id = r.scheduling_subsection_id')
            ->where($where)
            ->find();
        return $info ? $info : false;
    }

    /**
     * @param string $company_id 诊所id
     * @Name     gets_the_largest_id_of_the_registration
     * @explain  获取挂号最大的ID
     * @author   zuochuanye
     * @Date     2017/10/25
     * @return bool
     */
    public function gets_the_largest_id_of_the_registration($company_id = '')
    {
        $where = [
            'company_id' => $company_id
        ];
        $maxNumber = $this->where($where)->max('registration_id');
        return $maxNumber ? $maxNumber : false;
    }

    /**
     * @param $data   需要添加数据
     * @Name     registration_add
     * @explain     添加挂号信息
     * @author   zuochuanye
     * @Date    2017/10/25
     * @return bool|mixed
     */
    public function registration_add($data)
    {
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        if (!$data = $this->create($data)) {
            return false;
        } else {
            $registration_id = $this->add($data);
            if ($registration_id) {
                $this->commit();
                return $registration_id;
            } else {
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * @param string $registration_id 挂号ID
     * @Name     registration_quit
     * @explain  退号
     * @author   zuochuanye
     * @Date     2017/10/25
     * @return bool
     */
    public function registration_quit($registration_id = '',$data =array())
    {
        $this->startTrans();
        if ($registration_id) {
            $registration_info = $this->where(array('registration_id' => $registration_id))->select();
            $registration_return = $this->where(array('registration_id' => $registration_id))->save($data);
            if (!is_bool($registration_return) && $registration_return) {
                $this->commit();
                $files_name = C("REGISTRATION_LOG_PATH")."registration_log_success_".date("Y_m_d").".log";
                $this->logs($registration_info,$files_name,'作废操作成功——');
                return true;
            } else {
                $this->rollback();
                $files_name = C("REGISTRATION_LOG_PATH")."registration_log_error_".date("Y_m_d").".log";
                $this->logs($registration_info,$files_name,'作废操作失败——');
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $data   数据
     * @param $files_name  路径
     * @param $title   提示信息
     * @Name     logs
     * @explain  记录log
     * @author   zuochuanye
     * @Date
     */
    protected function logs($data,$files_name,$title)
    {
        $str = is_string($data)?$data:var_export($data,true);
        if(empty($files_name)){
            $filename=TEMP_PATH.'cli_log.txt';
        }else{
            $filename=$files_name;
        }
        $handle=fopen($filename,"a+");
        fwrite($handle,$title.date("Y-m-d H:i:s")."\n");
        fwrite($handle,$str."\n\n");
        fclose($handle);

    }
    /**
     * @Name     registration_cancel
     * @explain  将所有时间过期，并且没有为未就诊的作废
     * @author   zuochuanye
     * @Date     2017/11/24
     * @return bool
     */
    public function registration_cancel(){
        $this->startTrans();
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $todaytime= mktime(23,58,00,$month,$day,$year);
        $info = $this->where(array("create_time"=>array('lt',$todaytime),'registration_status'=>array("in",array(1,5,6))))->select();
        $return_info = $this->where(array("create_time"=>array('lt',$todaytime),'registration_status'=>array("in",array(1,5,6))))->save(array('registration_status'=>4));
        if($return_info){
            $this->commit();
            $files_name = C("REGISTRATION_LOG_PATH")."registration_log_success_".date("Y_m_d").".log";
            $this->logs($info,$files_name,'作废操作成功——');
            return true;
        }else{
            $this->rollback();
            $files_name = C("REGISTRATION_LOG_PATH")."registration_log_error_".date("Y_m_d").".log";
            $this->logs($info,$files_name,'作废操作失败——');
            return false;
        }
    }

    /**
     * 未就诊列表
     * @param $hid
     * @param $uid
     * @param array $search
     * @param int $isVisit
     * @return array
     * Author: doreen
     */
    public function getNoVisitList($hid, $uid, $search = [], $isVisit = 1)
    {
        $where = array(
            'r.company_id' => $hid,
            'r.physician_id' => $uid,
            'r.registration_status' => $isVisit,
        );
        if($search)  $where = array_merge($where, $search);
        $join = 'LEFT JOIN __PATIENT__ p ON p.patient_id = r.patient_id LEFT JOIN __HIS_REGISTEREDFEE__ f ON f.reg_id = r.registeredfee_id';
        $field = 'p.patient_id,p.name,p.birthday,p.sex,p.mobile,r.registration_id,r.create_time,r.registration_status,f.registeredfee_name';
        $count = $this->alias('r')->join($join)->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $result = $this->alias('r')
            ->join($join)
            ->where($where)
            ->order('r.create_time desc')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->field($field)
            ->select();
        return array('page' => $pager->getPage() , 'list' => $result, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     * @param array $condition
     * @param string $field
     * @Name     is_new_submit
     * @explain  判断是否需要重新提交
     * @author   zuochuanye
     * @Date     2017/11/09
     * @return bool
     */
    public function is_new_submit($condition = [],$field="*"){
        $info = $this
            ->alias('r')
            ->join("__HIS_CARE_PKG__ p ON p.id = r.pkg_id")
            ->field($field)
            ->where($condition)
            ->find();
        return $info ? $info : false;

    }

    /**
     * 已就诊列表
     * @param $hid
     * @param $uid
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getVisitList($hid, $uid, $search = [])
    {
        $where = array(
            'pkg.hospital_id' => $hid,
            'pkg.doctor_id' => $uid,
            'pkg.type_id' => 0,
            'pkg.status' => array('in','0,1'),
        );
        if($search) $where = array_merge($where, $search);
        $carePkg = D('his_care_pkg');
        $join = "LEFT JOIN __PATIENT__ p ON p.patient_id = pkg.patient_id LEFT JOIN __HIS_REGISTRATION__ r ON r.registration_id = pkg.registration_id LEFT JOIN __HIS_REGISTEREDFEE__ f ON f.reg_id = r.registeredfee_id";
        $field = 'p.patient_id,p.name,p.birthday,p.sex,p.mobile,r.registration_id,f.registeredfee_name,pkg.status,pkg.addtime,pkg.registration_id as pkg_registration_id';
        $count = $carePkg->alias('pkg')->join($join)->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $result = $carePkg->alias('pkg')
            ->join($join)
            ->where($where)
            ->order('r.create_time desc')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->field($field)
            ->select();
        return array('page' => $pager->getPage() , 'list' => $result, 'count'=>$count, 'pager_str'=>$pager_str);
    }
}