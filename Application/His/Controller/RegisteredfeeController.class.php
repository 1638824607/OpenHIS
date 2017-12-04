<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;
class RegisteredfeeController extends HisBaseController
{
    protected $_registeredfee;
    protected $_company_id;  //医院ID
    protected $mid;//用户ID
    public function __construct()
    {
        parent::__construct();
        C('TITLE', "挂号费用设置");
        C('KEYEORDS', "");
        C('DESCRIPTION', "");

        $this->_registeredfee = D('his_registeredfee');
        $this->_company_id = $this->hospitalInfo['uid'];
        $this->mid = $this->userInfo['uid'];
    }

    /**
     * @Name      Registeredfee_list
     * @explain   挂号费用展示
     * @author    zuochuanye
     * @Date      2017/10/23
     */
    public function Registeredfee_list()
    {
        $this->display('registeredfeeList');
    }

    /**
     * @Name     getRegisteredFeeList
     * @explain  获取挂号费用列表
     * @author   zuochuanye
     * @Date     2017/10/31
     */
    public function getRegisteredFeeList()
    {
        if (IS_AJAX) {
            $search = I('post.search', '', 'htmlspecialchars') ? I('post.search', '', 'htmlspecialchars') : '';
            $where = array();
            if (!empty($search)) $where['registeredfee_name'] = array('like', '%' . $search . '%');
            $Registeredfee_list_info = $this->_registeredfee->getRegisteredfeeList($this->_company_id, $where);
            $Registeredfee_list_info ? $this->ajaxSuccess('成功', $Registeredfee_list_info) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name      Registeredfee_add
     * @explain   挂号费用添加
     * @author    zuochuanye
     * @Date      2017/10/23
     */
    public function Registeredfee_add()
    {
        if (IS_AJAX) {
            $mid = $this->mid;
            $company_id = $this->_company_id;
            $registeredfee_name = I('post.registeredfee_name', '', 'htmlspecialchars');
            $counts = M('his_registeredfee')->where(array('registeredfee_name'=>$registeredfee_name,'company_id'=>$company_id))->count();
            if($counts > 0)$this->ajaxError('挂号费用不能重复');
            $registeredfee_fee = number_format(I('post.registeredfee_fee'), 2);
            if (!is_numeric($registeredfee_fee)) $this->ajaxError('费用金额为整数或小数');
            $sub_registeredfee_name = I('post.sub_registeredfee_name') ? filter_array(I('post.sub_registeredfee_name'), ['']) : '';
            $sub_registeredfee_fee = I('post.sub_registeredfee_fee') ? filter_array(I('post.sub_registeredfee_fee'), ['']) : '';
            if (count($sub_registeredfee_name) != count($sub_registeredfee_fee)) $this->ajaxError('子费用不正确');
            $registeredfee_aggregate_amount = array_sum($sub_registeredfee_fee)  + $registeredfee_fee;
            $numberOfSub = $sub_registeredfee_fee ? count($sub_registeredfee_fee) : 0;
            $registeredfee_insert_info = array(
                'mid' => $mid,
                'company_id' => $company_id,
                'registeredfee_name' => $registeredfee_name,
                'registeredfee_fee' => $registeredfee_fee,
                'registeredfee_sub_fee' => array_sum($sub_registeredfee_fee),
                'registeredfee_aggregate_amount' => $registeredfee_aggregate_amount,
                'numberOfSub' => $numberOfSub
            );
            $registeredfee_sub_insert_info = [];
            if ($sub_registeredfee_name && $sub_registeredfee_fee) {
                foreach ($sub_registeredfee_name as $k => $v) {
                    $registeredfee_sub_insert_info[$k]['sub_registeredfee_name'] = $v;
                    $registeredfee_sub_insert_info[$k]['sub_registeredfee_fee'] = number_format($sub_registeredfee_fee[$k], 2);
                }
            }
            $registeredfee_return = $this->_registeredfee->addRegisteredfee($registeredfee_insert_info, $registeredfee_sub_insert_info);
            if ($registeredfee_return) {
                $this->ajaxSuccess('添加成功');
            } elseif ($this->_registeredfee->getError()) {
                $this->ajaxError($this->_registeredfee->getError());
            } else {
                $this->ajaxError('添加失败');
            }
        }
    }

    /**
     * @Name      Registeredfee_edit
     * @explain   挂号费用修改
     * @author    zuochuanye
     * @Date      2017/10/23
     */
    public function Registeredfee_edit()
    {
        if (IS_AJAX) {
            $mid = $this->mid;
            $reg_id = I('post.reg_id', '', 'intval');
            $registeredfee_name = I('post.registeredfee_name', '', 'htmlspecialchars');
            $counts = M('his_registeredfee')->where(array('registeredfee_name'=>$registeredfee_name,'company_id'=>$this->_company_id,'reg_id'=>array('neq',$reg_id)))->count();
            if($counts > 0)$this->ajaxError('挂号费用不能重复');
            $registeredfee_fee = number_format(I('post.registeredfee_fee'), 2);
            if (!is_numeric($registeredfee_fee)) $this->ajaxError('费用金额为整数或小数');
            $reg_sub_id = I('post.reg_sub_id') ? filter_array(I('post.reg_sub_id'), ['']) : '';
            $sub_registeredfee_name = I('post.sub_registeredfee_name') ? filter_array(I('post.sub_registeredfee_name'), ['']) : '';
            $sub_registeredfee_fee = I('post.sub_registeredfee_fee') ? filter_array(I('post.sub_registeredfee_fee'), ['']) : '';
            if (count($sub_registeredfee_name) != count($sub_registeredfee_fee)) $this->ajaxError('子费用不正确');
            $registeredfee_aggregate_amount = array_sum($sub_registeredfee_fee) + $registeredfee_fee;
            $numberOfSub = $sub_registeredfee_fee ? count($sub_registeredfee_fee) : 0;
            $registeredfee_edit_info = array(
                'mid' => $mid,
                'company_id' => $this->_company_id,
                'registeredfee_name' => $registeredfee_name,
                'registeredfee_fee' => $registeredfee_fee,
                'registeredfee_sub_fee' => array_sum($sub_registeredfee_fee),
                'registeredfee_aggregate_amount' => $registeredfee_aggregate_amount,
                'numberOfSub' => $numberOfSub
            );
            $registeredfee_sub_edit_info = [];
            if ($sub_registeredfee_name && $sub_registeredfee_fee) {
                foreach ($sub_registeredfee_name as $k => $v) {
                    $registeredfee_sub_edit_info[$k]['reg_sub_id'] = $reg_sub_id[$k];
                    $registeredfee_sub_edit_info[$k]['sub_registeredfee_name'] = $v;
                    $registeredfee_sub_edit_info[$k]['sub_registeredfee_fee'] = number_format($sub_registeredfee_fee[$k], 2);
                }
            }
            $registeredfee_return = $this->_registeredfee->editRegisteredfee($registeredfee_edit_info, $registeredfee_sub_edit_info, $reg_id);
            if ($registeredfee_return) {
                $this->ajaxSuccess('修改成功');
            } elseif ($this->_registeredfee->getError()) {
                $this->ajaxError($this->_registeredfee->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        }
    }

    /**
     * @Name     getRegisteredfeeInfoByReg_id
     * @explain  根据reg_id获取挂号费用信息
     * @author   zuochuanye
     * @Date     2017/11/27
     */
    public function getRegisteredfeeInfoByReg_id()
    {
        if (IS_AJAX) {
            $reg_id = I('post.reg_id', '', 'intval');
            $info = $this->_registeredfee->getRegisteredfeeInfoByReg_id(array('r.reg_id' => $reg_id, 'r.company_id' => $this->_company_id));
            $info ? $this->ajaxSuccess('成功', $info) : $this->ajaxError('失败');
        }
    }

    /**
     * @Name     Registeredfee_delete
     * @explain  挂号费用删除
     * @author   zuochuanye
     * @Date     2017/10/23
     */
    public function Registeredfee_delete()
    {
        if (IS_AJAX) {
            $reg_id = I('post.reg_id', '', 'intval');
            $areThereRights = TRUE;
            if ($areThereRights) {
                $delete_return = $this->_registeredfee->deleteRegisteredfee($reg_id);
                $delete_return ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
            }
        }
    }
}