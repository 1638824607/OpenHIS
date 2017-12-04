<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;

use Org\Wx\Wechat;

class RegistrationController extends HisBaseController
{
    protected $_scheduling;
    protected $_registeredfee;
    protected $_registration;
    protected $_patient;

    protected $order_info = array();

    protected $_company_id;//医院id
    protected $_operator_id;//登录人ID
    public function __construct()
    {
        parent::__construct();
        C('TITLE', "诊所挂号");
        C('KEYEORDS', "");
        C('DESCRIPTION', "");
        $this->_scheduling = D('his_scheduling');
        $this->_registeredfee = D('his_registeredfee');
        $this->_registration = D('his_registration');
        $this->_patient = D('patient');

        $this->_company_id = $this->hospitalInfo['uid'];
        $this->_operator_id = $this->userInfo['uid'];
    }

    /**
     * @Name     Registration_list
     * @explain  挂号列表
     * @author   zuochuanye
     * @Date     2017/10/25
     */
    public function Registration_list()
    {
        $condition = [];
        if($this->userInfo['p_id'] != 0)$condition['da.uid'] = $this->_operator_id;
        $name = I('get.names', '', 'htmlspecialchars');
        $registration_status = I('get.registration_status', '', 'intval');
        if (!empty($registration_status)) $condition['r.registration_status'] = $registration_status;
        if (!empty($name)) $condition['p.name'] = array('like', '%' . $name . '%');
        $start_time = I('get.start_time');
        $end_time = I('get.end_time');
        if (!empty($start_time) && !empty($end_time)) $condition['r.create_time'] = array(array('gt', strtotime("$start_time + 1 day")), array('lt', strtotime("$end_time + 1 day")));//,de.true_name AS operator,da.true_name,
        $field = 'r.registration_id,r.registration_amount,r.registration_number,r.company_id,r.registration_status,r.create_time,r.operator_id,r.department_id,r.patient_id,r.physician_id,d.department_name,p.name,r.registeredfee_id,re.registeredfee_name,da.true_name as user_name';
        $registration_info = $this->_registration->getRegistrationList($condition, $field);
        foreach ($registration_info['list'] as $k => $v) {
            $registration_info['list'][$k]['operator'] = D("HisMember")->role_judgement($v['operator_id']);
        }
        $this->assign('page', $registration_info['page']);
        $this->assign('list', $registration_info['list']);
        $this->assign('count', $registration_info['count']);
        $this->assign('names', $name);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('registration_status', $registration_status);
        $this->display('registrationList');


    }

    /**
     * @Name     Registration_add
     * @explain  添加挂号信息
     * @author   zuochuanye
     * @Date     2017/10/25
     */
    public function Registration_add()
    {
        if (IS_AJAX) {
            $name = I('post.name', '', 'htmlspecialchars');
            $mobile = I('post.mobile');
            $patient_id = I('post.patient_id') ? I('post.patient_id') : '';
            $where = $patient_id ? array('patient_id' => $patient_id) : array('mobile' => $mobile);
            $countPatient = $this->_patient->get_the_number_of_patient($where);
            if (!$countPatient) {
                $patient_insert_info = array(
                    'name' => $name,
                    'mobile' => $mobile,
                    'password' => encrypt_password(substr($mobile, -6)),
                    'sex' => I('post.sex', '', 'intval'),
                    'birthday' => I('post.birthday'),
                    'id_card' => I('post.id_card') ? I('post.id_card') : '',
                    'address' => I('post.address', '', 'htmlspecialchars') ? I('post.address', '', 'htmlspecialchars') : '',
                    'hospital_id'=>$this->_company_id
                );
                $patient_id = $this->_patient->patient_add($patient_insert_info);
            } else {
                $patient_info = $this->_patient->get_the_patient_info_of_patient(array('mobile' => $mobile), 'patient_id');
                $patient_id = $patient_info['patient_id'];
            }
            if ($patient_id) {
                $registration_condition = array(
                    'r.patient_id' => $patient_id,
                    'r.operator_id' => $this->_operator_id,
                    'r.company_id' => $this->_company_id,
                    'r.registration_number' => I('post.registration_number'),
                    'p.status' => 0
                );
                $is_new_submit = $this->_registration->is_new_submit($registration_condition, 'r.registration_id,r.registration_number');
                if (!$is_new_submit) {
                    $registration_number = I('post.registration_number') == self::getRegistrationNumber() ? I('post.registration_number') : self::getRegistrationNumber();
                    $pkg_insert_array = array(
                        'hospital_id' => $this->_company_id,
                        'doctor_id' => $this->_operator_id,
                        'patient_id' => $patient_id,
                        'type_id' => 1,
                        'order_code' => $registration_number . rand(0, 999),#商户订单号
                        'amount' => I('post.registration_amount'),
                        'ol_pay_part' => I('post.registration_amount'),
                        'addtime' => time(),
                        'status' => 0,
                        'op_place' => 4
                    );
                    //添加pkg
                    $pkg_insert_id = M("his_care_pkg")->add($pkg_insert_array);
                    if ($pkg_insert_id) {
                        $condition = array(
                            's.scheduling_id' => I('post.scheduling_id', '', 'intval'),
                            'su.scheduling_subsection_id' => I('post.scheduling_subsection_id', '', 'intval'),
                            'sw.scheduling_week_id' => I('post.scheduling_week_id', '', 'intval'),
                        );
                        $fields = 's.company_id,sw.registeredfee_id,m.uid,s.department_id';
                        $scheduling_info = $this->_scheduling->getSchedulingInfo($condition, $fields);
                        $registration_insert_info = array(
                            'department_id' => $scheduling_info['department_id'],
                            'physician_id' => $scheduling_info['uid'],
                            'registeredfee_id' => $scheduling_info['registeredfee_id'],
                            'patient_id' => $patient_id,
                            'registration_number' => $registration_number,//挂号单号需添加完成后更新
                            'company_id' => $this->_company_id,
                            'registration_amount' => I('post.registration_amount'),
                            'scheduling_id' => I('post.scheduling_id', '', 'intval'),
                            'scheduling_subsection_id' => I('post.scheduling_subsection_id', '', 'intval'),
                            'scheduling_week_id' => I('post.scheduling_week_id', '', 'intval'),
                            'registration_status' => 5,
                            'operator_id' => $this->_operator_id,
                            'pkg_id' => $pkg_insert_id
                        );
                        $registration_id = $this->_registration->registration_add($registration_insert_info);
                    } else {
                        $this->ajaxError('无法创建');
                    }

                } else {
                    $registration_id = $is_new_submit['registration_id'];
                }
                if ($registration_id) {
                    $fields = 'r.registration_id,r.pkg_id,r.registration_amount,r.registration_number,r.company_id,r.registration_status,re.registeredfee_aggregate_amount';
                    $fields .= ',r.create_time,r.operator_id,r.department_id,r.patient_id,r.physician_id,d.department_name,p.name,r.registeredfee_id,re.registeredfee_name,su.subsection_type,sw.date,da.true_name as user_name';
                    $registration_return_info = $this->_registration->getRegistrationInfo(array('r.registration_id' => $registration_id), $fields);
                    $registration_return_info ? $this->ajaxSuccess('创建成功', $registration_return_info) : $this->ajaxError('无法展示列表');
                } else {
                    $this->ajaxError('创建失败');
                }

            } else {
                $this->ajaxError('无法创建2');
            }

        } else {
            $department_info = M('his_department')->field('did,department_name')->where(array('hid' => $this->_company_id))->select();
            $doctor_info = M('his_member')
                ->alias('m')
                ->field('de.true_name as user_name,m.uid')
                ->join("__HIS_DOCTOR__ de ON de.uid = m.uid")
                ->where(array('m.p_id' => $this->_company_id, 'type' => 2))
                ->select();
            $registeredfee_info = M('his_registeredfee')->field('registeredfee_name,reg_id')->where(array('company_id' => $this->_company_id))->select();
            $member_info['user_name'] = D("HisMember")->role_judgement($this->_operator_id);
            $this->assign('department_info', $department_info);
            $this->assign('doctor_info', $doctor_info);
            $this->assign('registeredfee_info', $registeredfee_info);
            $this->assign('registration_number', self::getRegistrationNumber());
            $this->assign('operator_name', $member_info['user_name']);
            $this->display('registrationAdd');
        }
    }

    /**
     * @Name     change_ol_pay_part
     * @explain  更改在线支付金额
     * @author   zuochuanye
     * @Date     2017/11/22
     */
    public function change_ol_pay_part()
    {
        if (IS_AJAX) {
            $pkg_id = I('post.pkg_id', '', 'intval');
            $ol = I("post.ol");
            if ($pkg_id) {
                $update_info = M("his_care_pkg")->where(array('id' => $pkg_id))->save(array("ol_pay_part" => $ol));
                $update_info !== false ? $this->ajaxSuccess("成功") : $this->ajaxError("更新失败");
            } else {
                $this->ajaxError("无法完成更新");
            }
        }
    }

    /**
     * @Name     getOnLinePay
     * @explain  获取在线支付信息
     * @author   zuochuanye
     * @Date     2017/11/28
     */
    public function getOnLinePay()
    {
        $pkg_id = I('post.pkg_id', 0);
        if (!$pkg_id) $this->resJSON(1, '缺少参数：pkg_id');
        $list = M('His_care_paylog')->where("pkg_id='$pkg_id' AND payment_platform>0 AND status=1")->select();
        #0现金，1微信，2支付宝，3，4，5....
        $Lp = array('现金', '微信', '支付宝', '备用');
        foreach ($list as &$v) {
            $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
        }
        $pkg = M("His_care_pkg")->where('id=' . $pkg_id)->find();
        if (count($list)) {
            $pay_log = M('his_care_paylog')->field("SUM(pay_amount) AS amount")->where(array('pkg_id' => $pkg_id))->group('pkg_id')->find();
            $registration_info = M('his_registration')->field('registration_amount')->where(array('pkg_id' => $pkg_id))->find();
            if ($registration_info['registration_amount'] == $pay_log['amount']) {
                M('his_registration')->where(array('pkg_id' => $pkg_id))->save(array('registration_status' => 1));
            } else {
                M('his_registration')->where(array('pkg_id' => $pkg_id))->save(array('registration_status' => 6));
            }
        }
        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list, 'pkg' => $pkg));
    }

    /**
     * @Name     payOrder
     * @explain  支付操作
     * @author   zuochuanye
     * @Date     2017/11/28
     */
    public function payOrder()
    {
        if (IS_AJAX) {
            $pkg_id = I('post.pkg_id', 0);
            $ol_pay_part = I('post.ol', 0);
            $pkg_status = I('post.pkg_status', 0);
            $cash = I('post.cash', 0);
            if (!$pkg_id) $this->ajaxError("参数缺失：pkg_id");
            $care_pkg = M("His_care_pkg");
            $pkg = $care_pkg->where('id=' . $pkg_id)->find();
            if (!$pkg) $this->ajaxError('pkg_id无效！');
            if ($pkg['hospital_id'] != $this->_company_id) $this->ajaxError('安全限制：1');#非同一医院无法访问
            if ($pkg_status == 0 && $cash < $pkg['amount']) $this->ajaxError('在线支付未到账，您可以用现金全额支付');
            #有现金部分
            if ($cash > 0) {
                #记录
                $paylog = array(
                    'pkg_id' => $pkg['id'],
                    'platform_code' => '现金',
                    'payment_platform' => 0,
                    'pay_amount' => $cash,
                    'status' => 1,
                    'addtime' => time()
                );
                 M('His_care_paylog')->data($paylog)->add();
            }
            #更新为已支付状态
            $care_pkg->where('id=' . $pkg_id)->save(array('status' => 1));
            M('his_registration')->where(array('pkg_id' => $pkg_id))->save(array('registration_status' => 1));
            $this->ajaxSuccess('成功');
        }
    }

    /**
     * @Name     registrationGoToPay
     * @explain  去付款
     * @author   zuochuanye
     * @Date     2017/11/22
     */
    public function registrationGoToPay()
    {
        $registration_id = I('get.registration_id');
        $registration_info = M('his_registration')->field('pkg_id,registration_number')->where(array('registration_id' => $registration_id))->find();
        M('his_care_pkg')->where(array('id' => $registration_info['pkg_id']))->save(array('order_code' => $registration_info['registration_number'] . rand(0, 999)));
        if (!$registration_info['pkg_id']) $this->ajaxError('pkg_id not found');
        $care_pkg = M('his_care_pkg')->where(array('id' => $registration_info['pkg_id']))->find();
        if (!$care_pkg) $this->ajaxError('pkg_id无效');
        if ($care_pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->ajaxError('安全限制：1');#非同一医院无法访问
        $this->assign('pkg', $care_pkg);
        $this->assign('registration_id', $registration_id);
        $this->display();

    }

    /**
     * @Name     getRegistrationPayInfo
     * @explain  获取支付信息
     * @author   zuochuanye
     * @Date     2017/11/22
     */
    public function getRegistrationPayInfo()
    {
        if (IS_AJAX) {
            $registration_id = I('post.registration_id');
            //获取收费信息
            $fields = 'r.registration_id,r.pkg_id,r.registration_amount,r.registration_number,r.company_id,r.registration_status,re.registeredfee_aggregate_amount';
            $fields .= ',r.create_time,r.operator_id,r.department_id,r.patient_id,r.physician_id,d.department_name,p.name,r.registeredfee_id,re.registeredfee_name,su.subsection_type,sw.date,da.true_name as user_name';
            $registration_info = $this->_registration->getRegistrationInfo(array('r.registration_id' => $registration_id), $fields);
            //计算已经支付的金额
            $pay_log = M('his_care_paylog')->field("SUM(pay_amount) AS amount")->where(array('pkg_id' => $registration_info['pkg_id']))->group('pkg_id')->find();
            $info = array();
            if ($pay_log) {
                if ($registration_info['registration_amount'] == $pay_log['amount']) {
                    $info['amount'] = '0.00';
                } else {
                    $info['amount'] = ($registration_info['registration_amount']) - ($pay_log['amount']);
                }
            } else {
                M("his_care_pkg")->where(array('id' => $registration_info['pkg_id']))->save(array("ol_pay_part" => $registration_info['registration_amount']));
                $info['amount'] = $registration_info['registration_amount'];
            }
            $info['registration_info'] = $registration_info;
            $this->ajaxSuccess('成功', $info);
        }
    }

    /**
     * @param $paylog
     * @param int $amount
     * @Name     wx_refund
     * @explain  微信退款
     * @author   zuochuanye
     * @Date     2017/11/28
     * @return array
     */
    protected function wx_refund($paylog, $amount = 0)
    {
        if (!$amount) $amount = $paylog['pay_amount'];
        $data = array(
            'appid' => $this->conf['appid'],
            'mch_id' => $this->conf['mchid'],
            'nonce_str' => md5(md5($this->conf['mchkey'] . time() . rand(100, 999))),
            'out_refund_no' => date('YmdHis') . '88' . $paylog['hospital_id'] . '88' . $paylog['patient_id'] . '88' . rand(10, 99),#商户退款单号,
            'refund_fee' => $amount * 100,#单位：分
            'total_fee' => $paylog['pay_amount'] * 100#单位：分
        );
        $paylog['platform_code'] ? $data['transaction_id'] = $paylog['platform_code'] :  $data['out_trade_no'] = $paylog['order_code'];
        $wxmp = new Wechat($this->conf);
        #签名
        $data['sign'] = $wxmp->makeSign($data, $this->conf['mchkey']);
        $xml = $wxmp->makeXml($data);
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $rc = $wxmp->curl_post_ssl($url, $xml);
        if (!$rc) return array(6, $wxmp->errMsg);
        if (function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);
        $x = simplexml_load_string($rc, 'SimpleXMLElement', LIBXML_NOCDATA);
        $rs = (array)$x;
        if (!$rs['refund_id']) return array(7, $rs['err_code_des']);
        return array(0, $rs);
    }

    /**
     * @param $paylog
     * @param int $amount
     * @Name     ali_refund
     * @explain  支付宝退款
     * @author   zuochuanye
     * @Date      2017/11/28
     * @return array
     */
    protected function ali_refund($paylog, $amount = 0)
    {
        require_once THINK_PATH . 'Library/Vendor/aliwap/wappay/service/AlipayTradeService.php';
        require_once THINK_PATH . 'Library/Vendor/aliwap/wappay/buildermodel/AlipayTradeRefundContentBuilder.php';
        if (!$amount) $amount = $paylog['pay_amount'];
        $config = array(
            'app_id' => $this->conf['app_id'],//应用ID,您的APPID。
            'merchant_private_key' => $this->conf['merchant_private_key'],//商户私钥，您的原始格式RSA私钥
            'notify_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_notify",//异步通知地址
            'return_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_pay_done",//同步跳转
            'charset' => "UTF-8",//编码格式
            'sign_type' => "RSA2",//签名方式
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do", //支付宝网关
            'alipay_public_key' => $this->conf['alipay_public_key'],//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        );
        $out_request_no = date('YmdHis') . '87' . $paylog['hospital_id'] . '87' . $paylog['patient_id'] . '87' . rand(10, 99);#商户退款单号;
        $RequestBuilder = new \AlipayTradeRefundContentBuilder();
        $paylog['platform_code'] ? $RequestBuilder->setTradeNo($paylog['platform_code']) : $RequestBuilder->setOutTradeNo($paylog['order_code']);
        $RequestBuilder->setRefundAmount($amount);
        $RequestBuilder->setRefundReason($paylog['adm_memo']);
        $RequestBuilder->setOutRequestNo($out_request_no);
        $Response = new \AlipayTradeService($config);
        $result = $Response->Refund($RequestBuilder);
        if ($result->code != '10000') return array(6, $result->msg . ',' . $result->sub_msg);
        return array(0, $result);
    }

    /**
     * @Name     getSchedulingList
     * @explain  获取排班信息列表
     * @author   zuochuanye
     * @Date     2017/10/30
     */
    public function getSchedulingList()
    {
        if (IS_AJAX) {
            $times = date('Y/m/d', time());
            $where = array(
                's.company_id' => $this->_company_id,
                'sw.date' => array('egt', $times)
            );
            //医生ID
            if (!empty(I('post.physicianid', '', 'intval'))) {
                $where['s.physicianid'] = I('post.physicianid', '', 'intval');
                $where['s.physicianid'] = I('post.physicianid', '', 'intval');
                $where['m.type'] = 2;
            }
            //科室ID
            if (!empty(I('post.department_id', '', 'intval'))) $where['s.department_id'] = I('post.department_id', '', 'intval');
            //挂号类别
            if (!empty(I('post.registeredfee_id', '', 'intval'))) $where['sw.registeredfee_id'] = I('post.registeredfee_id', '', 'intval');
            //时间类型 上午：1 下午：2 晚上：3
            if (!empty(I('post.subsection_type', '', 'intval'))) $where['su.subsection_type'] = I('post.subsection_type', '', 'intval');
            //时间
            if (!empty(I('post.dates',''))){
                $where['sw.date'] = array(array('egt',$times),array('eq',str_replace('-','/',I('post.dates'))),'AND');
            }
            $fields = 's.scheduling_id,s.physicianid,s.department_id,sw.scheduling_week_id,su.scheduling_subsection_id,sw.registeredfee_id,su.subsection_type,sw.date,d.department_name,de.true_name as user_name,r.registeredfee_name,r.registeredfee_aggregate_amount';
            $scheduling_info = $this->_scheduling->getSchedulingList($where, $fields);
            foreach ($scheduling_info['list'] as $k => $v) {
                $judge_subsection = self::judge_subsection();
                if ($judge_subsection == 2) {
                    if ($v['date'] == date("Y/m/d", time()) && $v['subsection_type'] == 1) {
                        unset($scheduling_info['list'][$k]);
                    }
                }
                if ($judge_subsection == 3) {
                    if (($v['date'] == date("Y/m/d", time()) && $v['subsection_type'] == 1) || ($v['date'] == date("Y/m/d", time()) && $v['subsection_type'] == 2)) {
                        unset($scheduling_info['list'][$k]);
                    }
                }
            }
            $scheduling_info['physicianid'] = I('post.physicianid', '', 'intval');
            $scheduling_info['department_id'] = I('post.department_id', '', 'intval');
            $scheduling_info['registeredfee_id'] = I('post.registeredfee_id', '', 'intval');
            $scheduling_info['subsection_type'] = I('post.subsection_type', '', 'intval');
            $scheduling_info['dates'] = I('post.dates');
            $scheduling_info['list'] ? $this->ajaxSuccess('成功', $scheduling_info) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name     judge_subsection
     * @explain  判断上下午晚上 用户删除过期排班信息
     * @author   zuochuanye
     * @Date    2017/11/23
     * @return int
     */
    private function judge_subsection()
    {
        $h = date("G");
        if ($h > 0 && $h < 12) {
            return 1;
        } elseif ($h > 12 && $h < 17) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * @Name     getPatientPool
     * @explain  获取患者库
     * @author   zuochuanye
     * @Date     2017/10/31
     */
    public function getPatientPool()
    {
        if (IS_AJAX) {
            $search = I('post.search', '', 'htmlspecialchars') ? I('post.search', '', 'htmlspecialchars') : '';
            $where = array();
            if (!empty($search)) $where['name'] = array('like', '%' . $search . '%');
            $patientInfo = $this->_patient->getPatientLists($this->_company_id, $where);
            foreach ($patientInfo['list'] as $k => $v) {
                $patientInfo['list'][$k]['age'] = $v['birthday'] ? birthday($v['birthday']) : 0;
            }
            $patientInfo['search'] = $search;
            $patientInfo ? $this->ajaxSuccess('成功', $patientInfo) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name     getPatientInfo
     * @explain  获取患者信息
     * @author   zuochuanye
     * @Date     2017/10/31
     */
    public function getPatientInfo()
    {
        if (IS_AJAX) {
            $patient_id = I('post.patient_id', '', 'intval') ? I('post.patient_id', '', 'intval') : '';
            if (!empty($patient_id)) {
                $fields = 'patient_id,name,mobile,id_card,sex,address,birthday';
                $patient_info = $this->_patient->get_the_patient_info_of_patient(array('patient_id' => $patient_id), $fields);
                $patient_info['year'] = birthday($patient_info['birthday']);
                $patient_info['month'] = date('m', strtotime($patient_info['birthday']));
                $patient_info ? $this->ajaxSuccess('成功', $patient_info) : $this->ajaxError('失败');
            } else {
                $this->ajaxError('获取失败');
            }
        }
    }

    /**
     * @Name     Registration_quit
     * @explain  退号
     * @author   zuochuanye
     * @Date     2017/10/25
     */
    public function Registration_quit()
    {
        if (IS_AJAX) {
            $registration_id = I('post.registration_id', '', 'intval');
            $Registration_quit_return = $this->_registration->registration_quit($registration_id, array('registration_status' => 3));
            $Registration_quit_return ? $this->ajaxSuccess('退号成功') : $this->ajaxError('退号失败');
        }
    }

    /**
     * @Name     registrationRefund
     * @explain  挂号退款
     * @author   zuochuanye
     * @Date     2017/11/28
     */
    public function registrationRefund()
    {
        $registration_id = I('get.registration_id', '', 'intval');
        $registration_info = M('his_registration')->field('registration_id,registration_amount,department_id,registration_number,registeredfee_id,pkg_id')->where(array('registration_id' => $registration_id))->find();
        $pkg = M("his_care_pkg")->where(array('id' => $registration_info['pkg_id']))->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        $show = array(
            'pkg_id' => $registration_info['pkg_id'],
            'pay_amount' => $registration_info['registration_amount'],
            'registeredfee_name' => M('his_registeredfee')->field('registeredfee_name')->where(array('reg_id' => $registration_info['registeredfee_id']))->find()['registeredfee_name'],
            'department_name' => M('his_department')->field('department_name')->where(array('dis' => $registration_info['department_id']))->find()['department_name'],
            'registration_id' => $registration_info['registration_id']
        );
        #支付列表
        $list_pay = M('His_care_paylog')->where(array('pkg_id' => $registration_info['pkg_id']))->select();
        $Lp = array('现金', '微信', '支付宝', '备用');
        foreach ($list_pay as &$v) {
            $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
        }
        #退款列表
        $refund_list_pay = M('His_care_refundlog')->where(array('pkg_id' => $registration_info['pkg_id']))->select();
        foreach ($refund_list_pay as &$v) {
            $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
        }
        $this->assign('show', $show);
        $this->assign('list_pay', $list_pay);
        $this->assign('refund_list_pay', $refund_list_pay);
        $this->display();
    }

    /**
     * @Name     pkgRefundDo
     * @explain  退款操作
     * @author   zuochuanye
     * @Date     2017/11/28
     */
    public function pkgRefundDo()
    {
        $ids = I('post.ids', '');
        $adm_memo = I('post.memo', '');
        $pkg_id = I('post.pkg_id', 0, 'intval');
        $cash = I('post.cash', 0);
        $refund_type = I('post.refund_type', 0, 'intval');#0原路，1现金
        if (!$pkg_id) $this->resJSON(1, '缺少参数：pkg_id');
        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where("id='$pkg_id'")->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问
        if (!in_array($pkg['status'], array(1, 5, 7))) $this->resJSON(4, '状态不支持:' . $pkg['status']);
        $trans = M();
        $trans->startTrans();#启用事务
        #开发记录
        #写日志
        #记录dzm_his_work_log
        $log = array(
            'tab_name' => 'his_care_pkg',
            'rel_id' => $pkg_id,
            'title' => '退款',
            'session' => json_encode($_SESSION),
            'cookie' => json_encode($_COOKIE),
            'ip' => get_client_ip(),
            'dev_info' => 'filename:' . basename(__FILE__) . ',class:' . __CLASS__ . ',method:' . __METHOD__,
        );
        $add = M('His_work_log')->add($log);
        if (!$add) {
            $trans->rollback();
            $this->resJSON(3, '日志记录失败');
        }
        #更新记录明细
        $adm_uid = $this->userInfo['uid'];
        $log = array(
            'pkg_id' => $pkg_id,
            'status' => 1,
            'adm_uid' => $adm_uid,
            'adm_ip' => get_client_ip(),
            'adm_memo' => $adm_memo,
        );
        $refundlog = M('His_care_refundlog');
        if ($refund_type == 1) {
            #查看已退款
            $rs = $refundlog->where("pkg_id='$pkg_id'")->select();
            $refund_amount = 0;
            if ($rs) {
                foreach ($rs as $v4) {
                    $refund_amount += $v4['refund_amount'];
                }
            }
            #记录
            $log['payment_platform'] = 0;
            $log['platform_code'] = '现金';
            $log['refund_amount'] = $cash;
            #记录
            $log_id = $refundlog->add($log);
            if (!$log_id) {
                $trans->rollback();
                $this->resJSON(8, '记录失败');
            }
            $status = 7;
            #退款大于等于支付金额了，完成退款
            if ($refund_amount + $cash >= $pkg['amount']) $status = 4;
            #更新
            $care_pkg->where("id='$pkg_id'")->save(array('status' => $status));
        } else {
            $status = 4;
            #原路退款
            $pay_list = M("His_care_paylog")->where("pkg_id='$pkg_id'")->select();
            foreach ($pay_list as $v) {
                $log['payment_platform'] = $v['payment_platform'];
                if ($v['payment_platform'] == 0) {
                    #记录
                    $log['platform_code'] = '现金';
                    $log['refund_amount'] = $v['pay_amount'];
                    #记录
                    $log_id = $refundlog->add($log);
                    if (!$log_id) {
                        $trans->rollback();
                        $this->resJSON(8, '记录失败');
                    }
                } else if ($v['payment_platform'] == 1) {
                    #微信
                    #配置信息
                    $this->conf = M('His_wxmp')->where("userid='$pkg[hospital_id]'")->find();
                    if (!$this->conf) {
                        $trans->rollback();
                        $this->resJSON(9, '获取配置信息出错:' . $pkg['hospital_id']);
                    }
                    list($rc_status, $rs) = $this->wx_refund($v);
                    if ($rc_status != 0) {
                        $trans->rollback();
                        $this->resJSON(9, '微信退款出错:' . $rs);
                    }
                    #dzm_his_care_refundlog
                    $log['payment_platform'] = 1;
                    $log['platform_code'] = $rs['refund_id'];
                    $log['refund_amount'] = $rs['refund_fee'] / 100;
                    #记录
                    $log_id = $refundlog->add($log);
                    if (!$log_id) {
                        $trans->rollback();
                        $this->resJSON(8, '记录失败');
                    }

                } else if ($v['payment_platform'] == 2) {
                    #支付宝
                    #配置信息
                    $this->conf = M('His_wxmp')->where("userid='$pkg[hospital_id]'")->find();
                    if (!$this->conf) {
                        $trans->rollback();
                        $this->resJSON(9, '获取配置信息出错:' . $pkg['hospital_id']);
                    }
                    list($rc_status, $result) = $this->ali_refund($v);
                    if ($rc_status != 0) {
                        $trans->rollback();
                        $this->resJSON(9, '支付宝退款出错:' . $result);
                    }
                    $log['payment_platform'] = 2;
                    $log['platform_code'] = $result->trade_no;
                    $log['refund_amount'] = $result->refund_fee;
                    #记录
                    $log_id = $refundlog->add($log);
                    if (!$log_id) {
                        $trans->rollback();
                        $this->resJSON(8, '记录失败');
                    }
                } else {
                    #todo 其它支付方式？？？
                }
            }
            #更新
            $care_pkg->where("id='$pkg_id'")->save(array('status' => $status));
        }
        #更新订单为已退款dzm_his_care_order
        if ($ids) M('his_registration')->where(array('registration_id' => $ids))->save(array('registration_status' => 3));
        $trans->commit();
        $this->resJSON(0, 'ok', array('status' => $status));
    }

    /**
     * @Name     Registration_cancel
     * @explain  作废
     * @author   zuochuanye
     * @Date     2017/11/14
     */
    public function Registration_cancel()
    {
        if (IS_AJAX) {
            $registration_id = I('post.registration_id', '', 'intval');
            $Registration_quit_return = $this->_registration->registration_quit($registration_id, array('registration_status' => 4));
            $Registration_quit_return ? $this->ajaxSuccess('作废成功') : $this->ajaxError('作废失败');
        }
    }

    /**
     * @Name     getPkglog_id
     * @explain  获取paylog信息
     * @author   zuochuanye
     * @Date
     */
    public function getPaylogInfo()
    {
        if (IS_AJAX) {
            $registration_id = I('post.registration_id', '', 'intval');
            $paylog_info = M('his_care_paylog')->field('pay_amount,id')->where(array('pkg_id' => M('his_registration')->field('pkg_id')->where(array('registration_id' => $registration_id))->find()['pkg_id'], 'status' => 1, 'payment_platform' => array('gt', 0)))->find();
            $paylog_info ? $this->ajaxSuccess('成功', $paylog_info) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name     ForAge
     * @explain  获取年龄
     * @author   zuochuanye
     * @Date     2017/10/30
     */
    public function ForAge()
    {
        if (IS_AJAX) {
            $birthday = I('post.birthday');
            if (!empty($birthday)) {
                $data['year'] = birthday($birthday);
                $data['month'] = date('m', strtotime($birthday));
                $data ? $this->ajaxSuccess('成功', $data) : $this->ajaxError('失败');
            } else {
                $this->ajaxError('错误');
            }

        }
    }

    /**
     * @Name     getRegistrationNumber
     * @explain  生成挂诊编号
     * @author   zuochuanye
     * @Date     2017/10/25
     */
    public function getRegistrationNumber()
    {
        $year = date('Y');
        $month = str_pad(date('m', time()), 2, 0, STR_PAD_LEFT);
        $days = str_pad(date('d', time()), 2, 0, STR_PAD_LEFT);
        $company_id = str_pad($this->_company_id, 5, 0, STR_PAD_LEFT);
        $registration_id = $this->_registration->gets_the_largest_id_of_the_registration($this->_company_id);
        $registration_id = $registration_id ? $registration_id : 0;
        $registration_id = str_pad($registration_id + 1, 5, 0, STR_PAD_LEFT);
        $registration_number = $year . $month . $days . $company_id . $registration_id;
        return $registration_number;
    }
    /**
     * @Name     cancel_all_registration
     * @explain  将所有时间过期，并且没有为未就诊的作废   *定时任务请求*
     * @author   zuochuanye
     * @Date     2017/11/24
     */
    public function cancel_all_registration(){
        $this->_registration = D('his_registration');
        $this->_registration->registration_cancel();
    }
}