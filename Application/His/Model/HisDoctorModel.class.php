<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

namespace His\Model;
use Common\Model\BaseModel;
use Org\Nx\Page;

/**
 * 医生操作综合
 * DoctorModel
 * Author: wsl
 */
class HisDoctorModel extends BaseModel
{
    public $tableName = 'his_doctor';
    public $db;
    private $tab_pre;

    public function _initialize(){
        $this->db = M();
        $this->tab_pre = C('DB_PREFIX');
    }

    /**
     * 统计sql
     * @param $sql
     * @return int
     * Author: wsl
     */
    public function counSql($sql)
    {
        $r = $this->db->query($sql);
        return $r ? $r[0]['count_num'] : 0;
    }

    /**
     * 获取挂号列表
     * @param $id
     * @return int|mixed
     * Author: wsl
     */
    public function getRegistrationById($id)
    {
        $id = intval($id);
        if(!$id)return 0;
        return M('His_registration')->where("registration_id='$id'")->find();
    }

    /**
     * 获取患者基本信息
     * @param $id
     * @return int|mixed
     * Author: wsl
     */
    public function getPatientById($id)
    {
        $id = intval($id);
        if(!$id)return 0;
        return M("Patient")->where("patient_id=$id")->find();
    }

    /**
     * 获取患者基本信息
     * @param $id
     * @return int|mixed
     * Author: wsl
     */
    public function getPatientByString($str)
    {
        return M("Patient")->where($str)->find();
    }

    /**
     * 保存患者基本信息
     * @param $id
     * @return int|mixed
     * Author: wsl
     */
    public function savePatient($where,$data)
    {
        return M("Patient")->where($where)->data($data)->save();
    }



    /**
     * ajax获取药品
     * Author: wsl
     */
    public function mygetMedicines($hid)
    {
        $kw = I('post.kw', '');

        $fix = '';

        if ($kw) {
            $fix = " AND";
            if (preg_match("/^[a-zA-Z\s]+$/", $kw)) {
                $fix .= " a.keywords LIKE '$kw%'";

            } else {
                $fix .= " a.medicines_name LIKE '$kw%'";
            }
        }

        $sql = "SELECT 
c.hmr_id as medicines_id,b.inventory_num,b.inventory_unit,b.inventory_prescription_price,a.medicines_name,a.unit
 FROM ".$this->tab_pre."his_hospital_medicines_relation c 
 LEFT JOIN ".$this->tab_pre."his_medicines a ON c.medicines_id=a.medicines_id
 LEFT JOIN  ".$this->tab_pre."his_inventory b ON c.hmr_id=b.hmr_id
 WHERE c.hospital_id='$hid' $fix LIMIT 50";


        return $this->db->query($sql);
    }

    /**
     * 获取附加费用
     * Author: wsl
     */
    public function getExtracharges($hid)
    {
        $type = I('post.type', 0);

        return $this->db->query("SELECT * FROM ".$this->tab_pre."his_prescription_extracharges WHERE hid='$hid' AND type='$type'");
    }

    /**
     * 获取检查项目费用
     * Author: wsl
     */
    public function getInspectionfee($hid)
    {
        $kw = I('post.kw', '');

        $fix = '';

        if ($kw) {
            $fix = " AND inspection_name LIKE '%$kw%'";
        }

        return $this->db->query("SELECT * FROM ".$this->tab_pre."his_inspectionfee WHERE hid='$hid' $fix");
    }


    /**
     * 获取挂号列表
     * Author: wsl
     */
    public function getRegistrations($uid)
    {

        $kw = I("post.kw", '');
        #$hid = $this->hospitalInfo['uid'];
        #$uid = $this->userInfo['uid'];

        $fix = '';
        if (is_numeric($kw)) {
            $fix = " AND b.mobile LIKE '%$kw%'";
        } elseif ($kw) {
            $fix = " AND b.name LIKE '%$kw%'";
        }

        $sql = "SELECT 
 %s 
FROM ".$this->tab_pre."his_registration a 
LEFT JOIN ".$this->tab_pre."patient b ON a.patient_id=b.patient_id 
LEFT JOIN ".$this->tab_pre."his_registeredfee d ON a.registeredfee_id=d.reg_id 
WHERE a.physician_id='$uid' AND a.registration_status=1 %s %s %s";#a.company_id='$hid' AND

        $list = array();
        $pager_str = '';
        $row_count = $this->counSql(sprintf($sql, 'count(a.patient_id) as count_num', $fix, '', ''));
        $page = 1;
        if ($row_count > 0) {
            $pager = new_page($row_count, 10,1);

            $fields = "a.registration_id,a.patient_id,b.allergy_info,b.address,a.create_time,b.name,b.mobile,b.sex,b.birthday,d.registeredfee_name ";
            $orderby = "ORDER BY a.create_time DESC";
            $limit = "LIMIT " . $pager->firstRow . "," . $pager->listRows;

            $exe_sql = sprintf($sql, $fields, $fix, $orderby, $limit);

            $list = $this->db->query($exe_sql);

            foreach ($list as &$v) {
                $v['create_time_str'] = date('Y-m-d H:i', $v['create_time']);
                $v['age'] = $this->datediffage(strtotime($v['birthday']));
                $v['sex_str'] = $v['sex'] == 1 ? '男' : '女';
            }
            $pager_str = $pager->showHis();
            $page = $pager->getPage();
        }


        return array($row_count,$list,$pager_str,$page);

    }


    /**
     * 用手机号获取用户信息
     * Author: wsl
     */
    public function searchPatientByMobile($hid)
    {
        $kw = I("post.m", '');

        $fix = strlen($kw) > 10 ? "mobile='$kw'" : "patient_id='$kw'";

        $sql = "SELECT patient_id,name,mobile,sex,birthday,address,allergy_info FROM ".$this->tab_pre."patient 
WHERE $fix " . (strlen($kw) > 10 ? " AND hospital_id=" . $hid : "");

        $rc = $this->db->query($sql);
        if (!$rc) return 0;
        $v = $rc[0];
        $v['age'] = $this->datediffage(strtotime($v['birthday']));

        return $v;
    }

    /**
     * 获取患者档案
     * Author: wsl
     */
    public function getUserInfo($patient_id)
    {
        $r = $this->db->query("SELECT * FROM ".$this->tab_pre."patient a LEFT JOIN ".$this->tab_pre."his_patient_file b ON a.patient_id=b.patient_id WHERE a.patient_id='$patient_id' LIMIT 1");
        if (!$r) return 0;

        $LR = array(0 => '其它', 1 => '爸爸', 2 => '妈妈', 3 => '儿子', 4 => '女儿', 5 => '亲戚', 6 => '朋友');
        $p = $r[0];
        $p['emergency_contact_relation_label'] = isset($LR[$p['emergency_contact_relation']]) ? $LR[$p['emergency_contact_relation']] : '未知';

        #血型 1:A 2:B 3:AB 4:O    Rh血型 1:阴性 2:阳性
        $LB1 = array(1 => 'A', 2 => 'B', 3 => 'AB', 4 => 'O',);
        $LB2 = array(1 => '阴性', 2 => '阳性',);

        $d = json_decode($p['blood_type'], true);
        $p['blood_a'] = isset($LB1[$d[0]]) ? $LB1[$d[0]] : '未知';
        $p['blood_b'] = isset($LB1[$d[1]]) ? $LB2[$d[1]] : '未知';

        return $p;
    }

    /**
     * 获取患者历史病历
     * Author: wsl
     */
    public function getCareHistory($patient_id,$limit=10)
    {
        $sql = "SELECT a.*,b.hospital_name,c.name as patient_name
FROM ".$this->tab_pre."his_care_history a 
LEFT JOIN ".$this->tab_pre."his_hospital b ON a.hospital_id=b.hid 
LEFT JOIN ".$this->tab_pre."patient c ON a.patient_id=c.patient_id 
WHERE a.patient_id=$patient_id ORDER BY a.id DESC LIMIT $limit";

        $list = $this->db->query($sql);

        $L = array('初诊', '复诊', '急诊');

        foreach ($list as &$v) {
            $v['addtime_str'] = date('Y-m-d H:i', $v['addtime']);
            $v['type_label'] = $L[$v['type_id']];
        }

        return $list;

    }

    /**
     * 获取患者列表
     * Author: wsl
     */
    public function getPatientList($hid)
    {

        $kw = I("post.kw", '');

        $fix = '';
        if (is_numeric($kw)) {
            $fix = " AND b.mobile LIKE '%$kw%'";
        } elseif ($kw) {
            $fix = " AND b.name LIKE '%$kw%'";
        }

        $sql = "SELECT %s FROM ".$this->tab_pre."patient b WHERE b.hospital_id='$hid' %s %s %s";

        $list = array();
        $pager_str = '';
        $sqlc = sprintf($sql, 'count(b.patient_id) as count_num', $fix, '', '');

        $row_count = $this->counSql($sqlc);

        $page=1;
        if ($row_count > 0) {
            $pager = new_page($row_count, 10,1);
            $fields = "*";
            $orderby = "ORDER BY b.patient_id DESC,b.last_login_time DESC";
            $limit = "LIMIT " . $pager->firstRow . "," . $pager->listRows;

            $sql = sprintf($sql, $fields, $fix, $orderby, $limit);
            $list = $this->db->query($sql);

            #LEFT JOIN ".$this->tab_pre."patient_address c ON b.patient_id=c.patient_id

            foreach ($list as &$v) {
                $v['create_time_str'] = date('Y-m-d H:i', $v['last_login_time']?$v['last_login_time']:$v['create_time']);
                $v['age'] = $this->datediffage(strtotime($v['birthday']));
                $v['sex_str'] = $v['sex'] == 1 ? '男' : '女';
            }

            $pager_str = $pager->showHis();
            $page = $pager->getPage();
        }

        return array($row_count,$list,$pager_str,$page);
    }

    /**
     * ajax获取医生看诊记录
     * Author: wsl
     */
    public function getPkgList($hid,$doctor_id)
    {
        if (!$_POST) exit;
        $kw = I('post.kw', '');
        $status = I('post.status', 0);
        $doctor_id = I('post.doctor_id', $doctor_id);
        $page = I('post.p', 0);
        $fix = $doctor_id == 'all' ? '1' : "a.doctor_id='$doctor_id'";
        if ($status != 999) $fix .= " AND a.status='$status'";
        if ($kw) {
            $s = is_numeric($kw) ? "b.mobile LIKE '%$kw%'" : "b.name LIKE '%$kw%'";
            $fix .= $fix == '' ? $s : " AND $s";
        }

        $fix .= $fix == '' ? " a.type_id=0" : " AND  a.type_id=0";

        $fix .= $fix == '' ? "a.hospital_id=" . $hid : " AND a.hospital_id=" . $hid;

        if ($page) {
            $row_count = $this->counSql("SELECT count(a.id) as count_num
 FROM ".$this->tab_pre."his_care_pkg a 
 LEFT JOIN ".$this->tab_pre."patient b ON a.patient_id=b.patient_id
 WHERE $fix ");

            $pager = new_page($row_count, 10,1);
            $limit = "LIMIT " . $pager->firstRow . "," . $pager->listRows;

            $page_str = $pager->showHis();

        } else {
            $limit = " LIMIT 50";
            $page_str = '';
        }


        $sql = "SELECT a.*,b.name,b.mobile,b.sex,b.birthday
,from_unixtime(a.addtime,'%m-%d %h:%i') as addtime_str
,from_unixtime(a.addtime,'%Y-%m-%d %h:%i:%s') as addtime_str_full,c.true_name,d.owner_name
 FROM ".$this->tab_pre."his_care_pkg a 
 LEFT JOIN ".$this->tab_pre."patient b ON a.patient_id=b.patient_id
 LEFT JOIN ".$this->tab_pre."his_doctor c ON a.doctor_id=c.uid
 LEFT JOIN ".$this->tab_pre."his_hospital d ON a.hospital_id=d.hid
 WHERE $fix
ORDER BY a.id DESC
$limit
";

        $list = $this->db->query($sql);

        if ($page) {
            $L_PKG = C('ORDER_STATUS');
            foreach ($list as &$v) {
                $v['add_date'] = date('Y-m-d H:i:s', $v['addtime']);
                $v['age'] = $this->datediffage(strtotime($v['birthday']));
                $v['sex_str'] = $v['sex'] == 1 ? '男' : '女';
                $v['status_str'] = isset($L_PKG[$v['status']]) ? $L_PKG[$v['status']] : '未知';
            }
        }

        return array($list,$page,$page_str);

    }

    /**
     *
     * 获取一个订单简要
     * @param $id
     * @return mixed
     * Author: wsl
     */
    public function getPkgByID($id)
    {
        return M("His_care_pkg")->where('id=' . $id)->find();
    }

    /**
     * 获取处方详情
     * @param $id
     * @return mixed
     * Author: wsl
     */
    public function getOrderDetail($id)
    {
        $sql = "SELECT a.*,b.true_name,c.addtime,d.user_name,e.hospital_name,f.department_name
FROM ".$this->tab_pre."his_care_order a 
LEFT JOIN ".$this->tab_pre."his_doctor b ON a.doctor_id=b.uid 
LEFT JOIN ".$this->tab_pre."his_care_pkg c ON a.pkg_id=c.id
LEFT JOIN ".$this->tab_pre."his_member d ON a.hospital_id=d.uid
LEFT JOIN ".$this->tab_pre."his_hospital e ON a.hospital_id=e.hid

LEFT JOIN ".$this->tab_pre."his_member g ON a.doctor_id=g.uid

LEFT JOIN ".$this->tab_pre."his_department f ON g.department_id=f.did
WHERE a.id='$id'";

        return $this->db->query($sql);
    }




    /*
 * @description    取得两个时间戳相差的年龄
 * @before         较小的时间戳
 * @after          较大的时间戳
 * @return str     返回相差年龄y岁m月d天
     * Author : 来自网络
**/
    public function datediffage($before, $after = 0)
    {

        if (!$after) $after = time();


        if ($before > $after) {
            $b = getdate($after);
            $a = getdate($before);
        } else {
            $b = getdate($before);
            $a = getdate($after);
        }
        $n = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
        $y = $m = $d = 0;
        if ($a['mday'] >= $b['mday']) { //天相减为正
            if ($a['mon'] >= $b['mon']) {//月相减为正
                $y = $a['year'] - $b['year'];
                $m = $a['mon'] - $b['mon'];
            } else { //月相减为负，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12;
            }
            $d = $a['mday'] - $b['mday'];
        } else {  //天相减为负，借月
            if ($a['mon'] == 1) { //1月，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12;
                $d = $a['mday'] - $b['mday'] + $n[12];
            } else {
                if ($a['mon'] == 3) { //3月，判断闰年取得2月天数
                    $d = $a['mday'] - $b['mday'] + ($a['year'] % 4 == 0 ? 29 : 28);
                } else {
                    $d = $a['mday'] - $b['mday'] + $n[$a['mon'] - 1];
                }
                if ($a['mon'] >= $b['mon'] + 1) { //借月后，月相减为正
                    $y = $a['year'] - $b['year'];
                    $m = $a['mon'] - $b['mon'] - 1;
                } else { //借月后，月相减为负，借年
                    $y = $a['year'] - $b['year'] - 1;
                    $m = $a['mon'] - $b['mon'] + 12 - 1;
                }
            }
        }

        return array(($y == 0 ? '' : $y), ($m == 0 ? '' : $m), ($d == 0 ? '' : $d));
    }
}