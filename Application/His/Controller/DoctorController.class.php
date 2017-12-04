<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: dxx & wsl
// +----------------------------------------------------------------------

namespace His\Controller;

use His\Model\GoodsModel as goods;
use Org\Nx\Page;
use Home\Model\PatientModel as patient;
use Org\Wx\Wechat;

/**
 * 医生综合操作
 * DoctorController
 */
class DoctorController extends HisBaseController
{
    private $md;
    protected $conf;
    protected $L_PKG;

    public function _initialize()
    {
        parent::_initialize();
        $this->L_PKG = C('ORDER_STATUS');
        $this->md = D('HisDoctor');
    }

    /**
     * 首页,开诊
     * Author: wsl
     */
    public function index()
    {
        $patient_id = I('get.patient_id', 0);
        $registration_id = I('get.registration_id', 0);

        if ($registration_id > 0) {
            $reg = $this->md->getRegistrationById($registration_id);
            if ($reg) {

                $patient_id = $reg['patient_id'];

            }
        }
        $this->assign('registration_id', $registration_id);

        $patient = array('patient_id' => 0, 'sex' => 1);


        if ($patient_id > 0) {
            $patient_ = $this->md->getPatientById($patient_id);
            if ($patient_) $patient = $patient_;
        }

        $this->assign('patient', $patient);
        $this->display();
    }

    /**
     * ajax获取药品
     * Author: wsl
     */
    public function getMedicines()
    {
        $mlist = $this->md->mygetMedicines($this->hospitalInfo['uid']);

        $this->resJSON(0, 'ok', array('num' => count($mlist), 'list' => $mlist));

    }

    /**
     * 获取附加费用
     * Author: wsl
     */
    public function getExtracharges()
    {

        $list = $this->md->getExtracharges($this->hospitalInfo['uid']);
        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list));
    }

    /**
     * 获取检查项目费用
     * Author: wsl
     */
    public function getInspectionfee()
    {

        $list = $this->md->getInspectionfee($this->hospitalInfo['uid']);
        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list));
    }

    /**
     * 获取挂号列表
     * Author: wsl
     */
    public function getRegistrations()
    {

        list($row_count,$list,$pager_str,$page) = $this->md->getRegistrations($this->userInfo['uid']);

        $this->resJSON(0, 'ok',
            array('num' => count($list), 'list' => $list,'page'=>$page,'row_count'=>$row_count,'pager_str'=>$pager_str)
        );

    }

    /**
     * 用手机号获取用户信息
     * Author: wsl
     */
    public function searchPatientByMobile()
    {
        $kw = I("post.m", '');
        if (!$kw) $this->resJSON(1, '缺少参数：m');
        if (!is_numeric($kw)) $this->resJSON(2, '只支持数字');

        $rc = $this->md->searchPatientByMobile($this->hospitalInfo['uid']);

        if (!$rc) $this->resJSON(2, '无信息');

        $this->resJSON(0, 'ok', $rc);
    }

    /**
     * 获取患者档案
     * Author: wsl
     */
    public function getUserInfo()
    {
        $patient_id = I("post.patient_id", 0);
        if (!$patient_id) $this->resJSON(1, '参数缺失：patient_id');

        $r = $this->md->getUserInfo($patient_id);

        if (!$r) $this->resJSON(2, 'patient_id无效');

        $this->resJSON(0, 'ok', $r);

    }

    /**
     * 获取患者历史病历
     * Author: wsl
     */
    public function getCareHistory()
    {
        $patient_id = I('post.patient_id', 0);
        if (!$patient_id) $this->resJSON(1, '用户id不可为空');

        $list = $this->md->getCareHistory($patient_id);

        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list));
    }

    /**
     * 获取患者列表
     * Author: wsl
     */
    public function getPatientList()
    {

        list($row_count,$list,$pager_str,$page) = $this->md->getPatientList($this->hospitalInfo['uid']);

        $this->resJSON(0, 'ok',
            array('num' => count($list), 'list' => $list,'page'=>$page,'row_count'=>$row_count,'pager_str'=>$pager_str)
        );
    }

    /**
     * ajax获取医生看诊记录
     * Author: wsl
     */
    public function getPkgList()
    {
        if (!$_POST) exit;

        list($list,$page,$page_str) = $this->md->getPkgList($this->hospitalInfo['uid'],$this->userInfo['uid']);

        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list, 'page' => $page, 'page_str' => $page_str));
    }

    /**
     * 统一支付
     * Author: wsl
     */
    public function pkgPay()
    {
        $pkg_id = I('get.pkg_id', 0);
        if (!$pkg_id) exit('pkg_id not found');

        $pkg = $this->md->getPkgByID($pkg_id);

        if (!$pkg) exit('pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) exit('安全限制：1');#非同一医院无法访问

        $this->assign('pkg', $pkg);

        $this->display();
    }

    /**
     * 保存看诊记录
     * Author: wsl
     */
    public function saveOrder()
    {
        if (!$_POST) exit;

        $hid = $this->hospitalInfo['uid'];
        if (!$hid) $this->resJSON(1, '无法获取医院标识');
        $ol_pay_part = 0;
        $registration_id = I('post.registration_id', 0);
        $care_pkg = M("His_care_pkg");
        if ($_POST['pkg_id']) {
            $pkg_id = $_POST['pkg_id'];
            #判断状态
            $pkg = $this->md->getPkgByID($pkg_id);
            if (!$pkg) {
                M()->rollback();
                $this->resJSON(4, 'pkg_id无效');
            }
            if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) {
                M()->rollback();
                $this->resJSON(5, '无权访问外院信息');
            }
            if ($pkg['status'] != 0) {
                M()->rollback();
                $this->resJSON(6, 'pkg状态不支持更新');
            }
            $ol_pay_part = $pkg['ol_pay_part'];
        }

        #保存患者信息
        $data = $_POST['patient'];
        $mobile = $data['mobile'];
        if (!$mobile) $this->resJSON(1, '手机号必填！');

        M()->startTrans();#启用事务

        $ids = array();
        $patient_id = $data['patient_id'];
        unset($data['patient_id']);
        $patient = M('Patient');
        if (!$patient_id) {
            $data['hospital_id'] = $hid;
            $data['is_del'] = 0;
            $data['create_time'] = time();
            $data['update_time'] = time();

            #判断用户手机号存在
            $ck = $patient->where("mobile='$mobile' AND hospital_id='$hid'")->find();
            $patient_id = $ck['patient_id'];
            if ($ck) {
                $patient->where(array('patient_id' => $patient_id))->data($_POST)->save();
            } else {
                $patient_id = $patient->data($data)->add();
            }
            if (!$patient_id) {
                M()->rollback();
                $this->resJSON(2, '新增患者信息时出错', $data);
            }
        } else {
            #更新
            #判断手机号唯一
            $ck = $patient->where("mobile='$mobile' AND hospital_id='$hid'")->find();# AND patient_id!=$patient_id
            if (!$ck) $this->resJSON(2, '手机号异常');
            $data['update_time'] = time();
            $patient->where(array('patient_id' => $patient_id))->data($data)->save();
        }

        if (!$patient_id) {
            M()->rollback();
            $this->resJSON(2, '患者ID信息时出错', $data);
        }

        $ids['patient_id'] = $patient_id;

        #保存患者历史病历
        $data = $_POST['history'];
        $care_history_id = $data['id'];
        unset($data['id']);
        $history = M('His_care_history');

        if (!$care_history_id) {
            $data['hospital_id'] = $this->hospitalInfo['uid'];
            $data['doctor_id'] = $this->userInfo['uid'];
            $data['department_id'] = $this->userInfo['department_id'];
            $data['patient_id'] = $patient_id;
            $data['addtime'] = time();
            $data['case_code'] = date('YmdHis') . '00' . $this->hospitalInfo['uid'] . '00' . $this->userInfo['uid'] . '00' . rand(10, 99);#诊断编号
            $care_history_id = $history->data($data)->add();
            if (!$care_history_id) {
                M()->rollback();
                $this->resJSON(3, '新增患者病历信息时出错', $data);
            }
        } else {
            $history->where('id=' . $care_history_id)->data($data)->save();
        }
        $ids['care_history_id'] = $care_history_id;

        #创建收费总表care_pkg

        if (!$_POST['pkg_id']) {

            $pkg = array(
                'hospital_id' => $this->hospitalInfo['uid'],
                'doctor_id' => $this->userInfo['uid'],
                'patient_id' => $patient_id,
                'type_id' => 0,
                'care_history_id' => $care_history_id,
                'order_code' => date('YmdHis') . '03' . $this->hospitalInfo['uid'] . '03' . $this->userInfo['uid'] . '03' . rand(10, 99),#商户订单号
                'amount' => 0,
                'addtime' => time(),
                'status' => 0
            );

            $pkg_id = $care_pkg->data($pkg)->add();
            if (!$pkg_id) {
                M()->rollback();
                $this->resJSON(3, '新增患者病历信息时出错', $data);
            }
        }

        $ids['pkg_id'] = $pkg_id;

        #保存药方及药品   0未支付，1已支付，2确认收款，3申请退款，4已退款
        $care_order = M('His_care_order');
        $care_order_sub = M('His_care_order_sub');

        $full_amount = 0;

        #删除旧数据
        $care_order->where("pkg_id='$pkg_id'")->delete();
        $care_order_sub->where("pkg_id='$pkg_id'")->delete();

        #创建新数据
        foreach ($_POST['care_order'] as $k => $v) {
            $amount = 0;

            $order = $v['order'];

            if (isset($order['id'])) unset($order['id']);
            #创建订单
            $order['hospital_id'] = $this->hospitalInfo['uid'];
            $order['doctor_id'] = $this->userInfo['uid'];
            $order['patient_id'] = $patient_id;
            $order['care_history_id'] = $care_history_id;
            $order['pkg_id'] = $pkg_id;
            $order['label'] = $v['label'];
            $order['addtime'] = time();
            $order['status'] = 0;
            $order['case_code'] = date('YmdHis') . '01' . $this->hospitalInfo['uid'] . '01' . $this->userInfo['uid'] . '01' . rand(10, 99);#处方编号 20171030012901290001

            #保存一个处方
            $care_order_id = $care_order->data($order)->add();
            if (!$care_order_id) {
                M()->rollback();
                $this->resJSON(4, '新增处方信息时出错', $data);
            }


            #保存处方明细
            #药
            $tmp_sub = 0;
            foreach ($v['order_sub'] as $k1 => $v1) {
                if (!$v1) continue;

                $tmp_amount = $v1['num'] * $v1['info']['inventory_prescription_price'];
                $order_sub = array(
                    'fid' => $care_order_id,
                    'pkg_id' => $pkg_id,
                    'type_id' => 0,#分类：0药，1附加费，2检查项目
                    'goods_id' => $v1['info']['medicines_id'],
                    'goods_name' => $v1['info']['medicines_name'],
                    'single' => 1,
                    'unit' => $v1['info']['inventory_unit'],
                    'price' => $v1['info']['inventory_prescription_price'],
                    'num' => $v1['num'],
                    'tips' => $v1['tips'],
                    'amount' => $tmp_amount
                );
                $add = $care_order_sub->data($order_sub)->add();
                if (!$add) {
                    M()->rollback();
                    $this->resJSON(5, '新增处方子信息时出错', $data);
                }
                $tmp_sub += $tmp_amount;
            }

            #检查
            foreach ($v['order_inspectionfee'] as $k2 => $v2) {
                if (!$v2) continue;
                $tmp_amount = $v2['num'] * $v2['info']['unit_price'];
                $order_sub = array(
                    'fid' => $care_order_id,
                    'pkg_id' => $pkg_id,
                    'type_id' => 2,#分类：0药，1附加费，2检查项目
                    'goods_id' => $v2['info']['ins_id'],
                    'goods_name' => $v2['info']['inspection_name'],
                    'single' => 1,
                    'unit' => $v2['info']['unit'],
                    'price' => $v2['info']['unit_price'],
                    'num' => $v2['num'],
                    'tips' => $v2['info']['class'],
                    'amount' => $tmp_amount
                );
                $add = $care_order_sub->data($order_sub)->add();
                if (!$add) {
                    M()->rollback();
                    $this->resJSON(5, '新增处方子信息时出错', $data);
                }
                $amount += $tmp_amount;
            }

            #附加费
            foreach ($v['order_extracharges'] as $k3 => $v3) {
                if (!$v3) continue;
                $tmp_amount = $v3['num'] * $v3['info']['fee'];
                $order_sub = array(
                    'fid' => $care_order_id,
                    'pkg_id' => $pkg_id,
                    'type_id' => 1,#分类：0药，1附加费，2检查项目
                    'goods_id' => $v3['info']['pre_id'],
                    'goods_name' => $v3['info']['extracharges_name'],
                    'single' => 1,
                    'unit' => '1次',
                    'price' => $v3['info']['fee'],
                    'num' => $v3['num'],
                    'tips' => '',
                    'amount' => $tmp_amount
                );
                $add = $care_order_sub->data($order_sub)->add();
                if (!$add) {
                    M()->rollback();
                    $this->resJSON(5, '新增处方子信息时出错', $data);
                }
                $amount += $tmp_amount;
            }

            #############################更新订单金额
            $up_data = array('amount' => $amount, 'all_amount' => $amount + ($tmp_sub * $order['num_d']));
            #更新处方金额
            $care_order->where('id=' . $care_order_id)->save($up_data);

            $full_amount += $up_data['all_amount'];#支付总额
        }
        #更新订单总金额

        if ($ol_pay_part > $full_amount) $ol_pay_part = $full_amount;

        $dddd = array(
            'amount' => $full_amount,
            'registration_id' => I('post.registration_id', 0),
            'ol_pay_part' => $ol_pay_part > 0 ? $ol_pay_part : $full_amount,
            'order_code' => date('YmdHis') . '03' . $this->hospitalInfo['uid'] . '03' . $this->userInfo['uid'] . '03' . rand(10, 99),#商户订单号预防重复  201711201151270364503646036700
        );

        if ($full_amount <= 0 && $_POST['save_type']) $dddd['status'] = 6;#直接完成交易

        $care_pkg->where('id=' . $pkg_id)->save($dddd);

        #更新挂号记录状态 dzm_his_registration.registration_status=2
        if ($registration_id > 0) {
            M('His_registration')->where("registration_id='$registration_id'")->save(array('registration_status' => 2));
        }

        #提交事务
        M()->commit();

        $ids['amount'] = $full_amount;
        $ids['order_code'] = $pkg['order_code'];

        $this->resJSON(0, 'ok', $ids);
    }

    /**
     * 获取用药详情
     * Author: wsl
     */
    public function getCareOrder()
    {
        $pkg_id = I('post.pkg_id', 0);
        if (!$pkg_id) $this->resJSON(1, '参数错误：pkg_id');

        $list = M('His_care_order')->where("pkg_id='$pkg_id'")->select();# AND doctor_id=".$this->userInfo['uid']

        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list));
    }

    /**
     * 获取用药详情列表
     * Author: wsl
     */
    public function getCareOrderSub()
    {
        $fid = I('post.fid', 0);
        if (!$fid) $this->resJSON(1, '参数错误：pid');

        $list = M('His_care_order_sub')->where("fid=$fid")->order("id DESC")->select();

        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list));

    }

    /**
     * 更新在线支付额度
     * Author: wsl
     */
    public function change_ol_pay_part()
    {
        $pkg_id = I('post.pkg_id', 0);
        $ol_pay_part = I('post.ol', 0);
        if (!$pkg_id) $this->resJSON(1, '参数缺失：pkg_id');
        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where('id=' . $pkg_id)->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问

        $care_pkg->where('id=' . $pkg_id)->save(
            array(
                'ol_pay_part' => $ol_pay_part,
                'order_code' => date('YmdHis') . '03' . $this->hospitalInfo['uid'] . '03' . $this->userInfo['uid'] . '03' . rand(10, 99)
            )
        );
        $this->resJSON();
    }

    /**
     * 支付订单
     * Author: wsl
     */
    public function payOrder()
    {
        $pkg_id = I('post.pkg_id', 0);
        $ol_pay_part = I('post.ol', 0);
        $pkg_status = I('post.pkg_status', 0);
        $cash = I('post.cash', 0);
        if (!$pkg_id) $this->resJSON(1, '参数缺失：pkg_id');
        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where('id=' . $pkg_id)->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问

        if ($pkg_status == 0 && $cash < $pkg['amount']) $this->resJSON(4, '在线支付未到账，您可以用现金全额支付');

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
            $care_paylog = M('His_care_paylog');

            $care_paylog->data($paylog)->add();
        }

        #写日志
        #记录dzm_his_work_log
        $log = array(
            'tab_name' => 'his_care_pkg',
            'rel_id' => $pkg_id,
            'title' => '订单支付',
            'session' => json_encode($_SESSION),
            'cookie' => json_encode($_COOKIE),
            'ip' => get_client_ip(),
            'dev_info' => 'filename:' . basename(__FILE__) . ',class:' . __CLASS__ . ',method:' . __METHOD__,
        );
        $add = M('His_work_log')->add($log);
        if (!$add) $this->resJSON(3, '日志记录失败');


        #更新为已支付状态
        $care_pkg->where('id=' . $pkg_id)->save(array('status' => 1));

        #更新订单表
        M('His_care_order')->where("pkg_id='$pkg_id'")->save(array('status' => 1));

        $this->resJSON(0, 'ok', $pkg);
    }

    /**
     * 获取在线支付
     * Author: wsl
     */
    public function getOnLinePay()
    {
        $pkg_id = I('post.pkg_id', 0);
        $pkg_status = I('post.pkg_status', 0);
        if (!$pkg_id) $this->resJSON(1, '缺少参数：pkg_id');

        #$fix = $pkg_status==0?" AND payment_platform>0 AND status=1":"";

        $list = M('His_care_paylog')->where("pkg_id='$pkg_id'")->select();

        #0现金，1微信，2支付宝，3，4，5....
        $Lp = array('现金', '微信', '支付宝', '备用');
        foreach ($list as &$v) {
            $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
        }

        $pkg = M("His_care_pkg")->where('id=' . $pkg_id)->find();

        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list, 'pkg' => $pkg));
    }

    /**
     * 显示订单交易收款退款
     * Author: wsl
     */
    public function pkgIO()
    {
        $pkg_id = I('get.pkg_id', 0, 'intval');
        if (!$pkg_id) $this->resJSON(1, '缺少参数：pkg_id');
        $pkg = M("His_care_pkg")->where("id='$pkg_id'")->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问
        #if(!in_array($pkg['status'],array(1,4,7)))exit('状态不支持');

        $pkg['status_str'] = isset($this->L_PKG[$pkg['status']]) ? $this->L_PKG[$pkg['status']] : '未知';

        #产品列表
        $mall_list = M('His_care_order')->where("pkg_id='$pkg_id'")->select();

        if ($pkg['status'] > 0) {

            #支付列表
            $list_pay = M('His_care_paylog')->where("pkg_id='$pkg_id'")->select();

            $Lp = array('现金', '微信', '支付宝', '备用');
            foreach ($list_pay as &$v) {
                $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
            }


            #退款列表
            $refund_list_pay = M('His_care_refundlog')->where("pkg_id='$pkg_id'")->select();

            foreach ($refund_list_pay as &$v) {
                $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
            }


            $this->assign('list_pay', $list_pay);
            $this->assign('refund_list_pay', $refund_list_pay);

        }


        $this->assign('mall_list', $mall_list);

        $this->assign('pkg', $pkg);

        $this->display();

    }

    /**
     * 退款
     * Author: wsl
     */
    public function pkgRefund()
    {
        $pkg_id = I('get.pkg_id', 0, 'intval');
        if (!$pkg_id) $this->resJSON(1, '缺少参数：pkg_id');
        $pkg = M("His_care_pkg")->where("id='$pkg_id'")->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问
        if (!in_array($pkg['status'], array(1, 4, 5, 7))) exit('状态不支持');

        #$fix = $pkg_status==0?" AND payment_platform>0 AND status=1":"";

        #产品列表
        $mall_list = M('His_care_order')->where("pkg_id='$pkg_id'")->select();

        #支付列表
        $list_pay = M('His_care_paylog')->where("pkg_id='$pkg_id'")->select();

        #0现金，1微信，2支付宝，3，4，5....
        $Lp = array('现金', '微信', '支付宝', '备用');
        foreach ($list_pay as &$v) {
            $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
        }


        $this->assign('mall_list', $mall_list);
        $this->assign('list_pay', $list_pay);
        $this->assign('list_pay_json', json_encode($list_pay));
        $this->assign('pkg', $pkg);

        $this->display();
    }

    /**
     * ajax 执行退款
     * Author: wsl
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


        #print_r($_POST);exit;#Array( [pkg_id] => 622 [ids] => [refund_type] => 1 [cash] => 1000000.00)

        $adm_uid = $this->userInfo['uid'];

        $log = array(
            'pkg_id' => $pkg_id,
            #'platform_code'=>$rs['refund_id'],
            # 'payment_platform'=>1,
            #'refund_amount'=>$rs['refund_fee']/100,
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

            #$this->resJSON(1,'debug',$pay_list);

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
                    #if(!$this->conf)$this->error('获取配置信息出错');
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
                    #if(!$this->conf)$this->error('获取配置信息出错');
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
        if ($ids) {
            M('His_care_order')->where("id IN($ids)")->save(array('status' => 4));
        }

        $trans->commit();

        $this->resJSON(0, 'ok', array('status' => $status));
    }

    /**
     * 获取退款记录
     * Author: wsl
     */
    public function getRefundLog()
    {
        $pkg_id = I('get.pkg_id', 0, 'intval');
        if (!$pkg_id) $this->resJSON(1, '缺少参数：pkg_id');
        $pkg = M("His_care_pkg")->where("id='$pkg_id'")->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问

        $list = M('His_care_refundlog')->where("pkg_id='$pkg_id'")->select();

        #0现金，1微信，2支付宝，3，4，5....
        $Lp = array('现金', '微信', '支付宝', '备用');
        foreach ($list as &$v) {
            #$v['addtime_str']=date('Y-m-d H:i:s',$v['addtime']);
            $v['payment_platform_label'] = isset($Lp[$v['payment_platform']]) ? $Lp[$v['payment_platform']] : '未知';
        }


        $this->resJSON(0, 'ok', array('num' => count($list), 'list' => $list, 'pkg' => $pkg));
    }

    /**
     * 微信退款
     * @param $paylog
     * @param int $amount
     * @return array
     * Author: wsl
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


        if ($paylog['platform_code']) {
            $data['transaction_id'] = $paylog['platform_code'];
        } else {
            $data['out_trade_no'] = $paylog['order_code'];
        }

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
     * 支付宝退款
     * @param $paylog
     * @param int $amount
     * @return array
     * Author: wsl
     */
    protected function ali_refund($paylog, $amount = 0)
    {
        require_once THINK_PATH . 'Library/Vendor/aliwap/wappay/service/AlipayTradeService.php';
        require_once THINK_PATH . 'Library/Vendor/aliwap/wappay/buildermodel/AlipayTradeRefundContentBuilder.php';
        if (!$amount) $amount = $paylog['pay_amount'];

        $config = array(
            //应用ID,您的APPID。
            'app_id' => $this->conf['app_id'],

            //商户私钥，您的原始格式RSA私钥
            'merchant_private_key' => $this->conf['merchant_private_key'],

            //异步通知地址
            'notify_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_notify",

            //同步跳转
            'return_url' => C('MAIN_SERVER_DOMAIN')."Pay/ali_pay_done",

            //编码格式
            'charset' => "UTF-8",

            //签名方式
            'sign_type' => "RSA2",

            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => $this->conf['alipay_public_key'],


        );

        $out_request_no = date('YmdHis') . '87' . $paylog['hospital_id'] . '87' . $paylog['patient_id'] . '87' . rand(10, 99);#商户退款单号;

        $RequestBuilder = new \AlipayTradeRefundContentBuilder();

        if ($paylog['platform_code']) {
            $RequestBuilder->setTradeNo($paylog['platform_code']);
            #$data['transaction_id'] = $paylog['platform_code'];
        } else {
            $RequestBuilder->setOutTradeNo($paylog['order_code']);
            # $data['out_trade_no'] = $paylog['order_code'];
        }

        $RequestBuilder->setRefundAmount($amount);
        $RequestBuilder->setRefundReason($paylog['adm_memo']);
        $RequestBuilder->setOutRequestNo($out_request_no);

        $Response = new \AlipayTradeService($config);
        $result = $Response->Refund($RequestBuilder);

        if ($result->code != '10000') return array(6, $result->msg . ',' . $result->sub_msg);

        return array(0, $result);

        /*M('His_care_pkg')->where("id='$paylog[pkg_id]'")->save(array('status'=>4));
        #dzm_his_care_refundlog
        $log = array(
            'pkg_id'=>$paylog['pkg_id'],
            'platform_code'=>$result->trade_no,
            'payment_platform'=>2,
            'refund_amount'=>$result->refund_fee,
            'status'=>1,
            'adm_uid'=>$adm_uid,
            'adm_ip'=>get_client_ip(),
            'adm_memo'=>$adm_memo,
        );
        $log_id = M('His_care_refundlog')->add($log);

        if(!$log_id)$this->resJSON(8,'记录失败');

        $this->resJSON(0,'ok',array('refund_id'=>$log_id));*/

        /*var_dump($result);


object(stdClass)#22 (6) {
  ["code"]=>
  string(5) "40004"
  ["msg"]=>
  string(15) "Business Failed"
  ["sub_code"]=>
  string(22) "ACQ.TRADE_STATUS_ERROR"
  ["sub_msg"]=>
  string(21) "交易状态不合法"
  ["refund_fee"]=>
  string(4) "0.00"
  ["send_back_fee"]=>
  string(4) "0.00"
}




        object(stdClass)#22 (10) {
  ["code"]=>
  string(5) "10000"
  ["msg"]=>
  string(7) "Success"
  ["buyer_logon_id"]=>
  string(11) "132****2725"
  ["buyer_user_id"]=>
  string(16) "2088702475545360"
  ["fund_change"]=>
  string(1) "Y"
  ["gmt_refund_pay"]=>
  string(19) "2017-11-13 15:16:05"
  ["out_trade_no"]=>
  string(26) "20171110095953031033032956"
  ["refund_fee"]=>
  string(4) "0.01"
  ["send_back_fee"]=>
  string(4) "0.00"
  ["trade_no"]=>
  string(28) "2017111021001004360248374070"
}

        */
    }

    /**
     * 显示收费列表
     * Author: wsl
     */
    public function getOrder()
    {
        $pkg_id = I('post.pkg_id', 0);
        if (!$pkg_id) $this->resJSON(1, '参数缺失：pkg_id');
        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where('id=' . $pkg_id)->find();
        if (!$pkg) $this->resJSON(2, 'pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(3, '安全限制：1');#非同一医院无法访问

        $care_order = M('His_care_order');
        $orders = $care_order->where('pkg_id=' . $pkg_id)->select();

        $this->resJSON(0, 'ok', array('num' => count($orders), 'list' => $orders, 'pkg' => $pkg));
    }

    /**
     * 收费发药
     * Author: wsl
     */
    public function pkgList()
    {


        $this->display();
    }

    /**
     * 订单明细
     * Author: wsl
     */
    public function pkgShow()
    {
        $pkg_id = I('get.pkg_id', 0);
        if (!$pkg_id) exit('pkg_id not found');

        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where('id=' . $pkg_id)->find();
        if (!$pkg) exit('pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) exit('安全限制：1');#非同一医院无法访问

        $this->assign('pkg', $pkg);

        $this->display();
    }

    /**
     * 完成交易
     * Author: wsl
     */
    public function pkgDone()
    {
        $pkg_id = I('post.pkg_id', 0);
        if (!$pkg_id) $this->resJSON(1, 'pkg_id not found');

        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where('id=' . $pkg_id)->find();
        if (!$pkg) exit('pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) $this->resJSON(2, '安全限制：1');#非同一医院无法访问

        if ($pkg['status'] != 1) $this->resJSON(4, '状态不支持');

        #记录dzm_his_work_log
        $log = array(
            'tab_name' => 'his_care_pkg',
            'rel_id' => $pkg_id,
            'title' => '完成交易',
            'session' => json_encode($_SESSION),
            'cookie' => json_encode($_COOKIE),
            'ip' => get_client_ip(),
            'dev_info' => 'filename:' . basename(__FILE__) . ',class:' . __CLASS__ . ',method:' . __METHOD__,
        );
        $add = M('His_work_log')->add($log);
        if (!$add) $this->resJSON(3, '日志记录失败');

        $care_pkg->where('id=' . $pkg_id)->save(array('status' => 6));

        $this->resJSON();
    }

    /**
     * 打印收费单
     * Author: wsl
     */
    public function printPay()
    {
        $pkg_id = I('get.pkg_id', 0);
        if (!$pkg_id) exit('pkg_id not found');

        $care_pkg = M("His_care_pkg");
        $pkg = $care_pkg->where('id=' . $pkg_id)->find();
        if (!$pkg) exit('pkg_id无效！');
        if ($pkg['hospital_id'] != $this->hospitalInfo['uid']) exit('安全限制：1');#非同一医院无法访问

        $this->assign('pkg', $pkg);

        $this->display();
    }

    /**
     * 打印处方
     * Author: wsl
     */
    public function printOrder()
    {
        $id = I('get.id', 0);
        if (!$id) exit('id not found');

        $rs = $this->md->getOrderDetail($id);
        if (!$rs) exit('id无效！');
        $order = $rs[0];
        if ($order['hospital_id'] != $this->hospitalInfo['uid']) exit('安全限制：1');#非同一医院无法访问

        if (!$order['true_name']) $order['true_name'] = $order['user_name'];

        #处方详情
        $list = M('His_care_order_sub')->where("fid=$id")->order("id DESC")->select();

        $this->assign('print_time', date('Y-m-d H:i:s'));
        $this->assign('order', $order);
        $this->assign('list', $list);

        $this->display();
    }

    /*
 * @description    取得两个时间戳相差的年龄
 * @before         较小的时间戳
 * @after          较大的时间戳
 * @return str     返回相差年龄y岁m月d天
     * Author : 来自网络
**/
    protected function datediffage($before, $after = 0)
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

    /**
     * 未就诊/已就诊列表
     * Author: doreen
     */
    public function getVisitList()
    {
        $hid = $this->hospitalInfo['uid'];
        $uid = $this->userInfo['uid'];
        if (IS_AJAX) {
            $registration = D('his_registration');
            $action = I('post.action', '', 'htmlspecialchars');
            $searchContent = [
                'name' => I('post.name', '', 'htmlspecialchars'),
                'startTime' => !empty(I('post.start_time')) ? strtotime(I('start_time')) : 0,
                'endTime' => !empty(I('post.end_time')) ? strtotime(I('end_time') . '23:59:59') : time(),
            ];
            $search['r.create_time'] = array(array('gt', $searchContent['startTime']), array('lt', $searchContent['endTime']));
            if (!empty($searchContent['name'])) {
                $search['p.name'] = array('like', '%' . $searchContent['name'] . '%');
            }
            if ($action == 'noVisit') {
                //未就诊列表
                $list = $registration->getNoVisitList($hid, $uid, $search);
            } else {
                //已就诊列表
                $searchVisit['pkg.addtime'] = array(array('gt', $searchContent['startTime']), array('lt', $searchContent['endTime']));
                if (!empty($searchContent['name'])) {
                    $searchVisit['p.name'] = array('like', '%' . $searchContent['name'] . '%');
                }
                $list = $registration->getVisitList($hid, $uid, $searchVisit);
            }
            $this->ajaxSuccess('', $list);
            exit;
        }

        $this->display('visitList');
    }

    /**
     * 调用系统日志功能
     * @param $msg
     * Author: wsl
     */
    protected function log($msg)
    {
        if (is_string($msg)) {
            \Think\Log::write($msg, '', '', '');
        } else {
            \Think\Log::write(json_encode($msg));
        }
    }
}

?>