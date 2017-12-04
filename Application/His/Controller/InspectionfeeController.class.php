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
 * 检查项目设置类
 * InspectionfeeController
 * Author: doreen
 */
class InspectionfeeController extends HisBaseController
{

    protected $inspectionfee_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->inspectionfee_model = D('his_inspectionfee');
    }

    /**
     * 检查项目列表
     * Author: doreen
     */
    public function index()
    {
        $search_content = I('post.search','','htmlspecialchars');
        if ($search_content) {
            $search['inspection_name'] = array('like','%'.$search_content.'%');;
        } else {
            $search = [];
        }
        $hospitalId = $this->hospitalInfo['uid'];
        $inspectionLists = $this->inspectionfee_model->getInspectionLists($hospitalId, $search);
        if (IS_AJAX) {
            $this->ajaxSuccess('', $inspectionLists);
        } else {
            $this->display();
        }
    }

    /**
     * 检查项目添加
     * Author: doreen
     */
    public function addInspection()
    {
        if(IS_POST){ //保存添加检查项目
            $mid = $this->userInfo['uid'];
            $hospitalId = $this->hospitalInfo['uid'];
            $inspectionName = I('post.inspection_name','','htmlspecialchars');
            //添加时去重
            $condition = array(
                'hid' => $this->hospitalInfo['uid'],
            );
            $inspectionList = $this->inspectionfee_model->getInspectionfee($condition);
            foreach ($inspectionList as $key => $value) {
                if ($value['inspection_name'] == $inspectionName) {
                    $this->ajaxError('项目名称已存在');
                }
            }
            $unitPrice  = number_format(I('post.unit_price'),2);
            $cost  = !empty(I('post.cost')) ? number_format(I('post.cost'),2) : I('post.cost');
            if(!is_numeric($unitPrice)) $this->ajaxError('单价金额为整数或小数');
            if(!empty(I('post.cost')) && !is_numeric($cost)) $this->ajaxError('成本金额为整数或小数');
            //保存添加的检查项目
            $classId = I('post.class_id','','intval');
            $class = D('his_dictionary')->findDictionaryById($classId);
            $data = array(
                'mid'                  =>  $mid,
                'hid'                  =>  $hospitalId,
                'inspection_name'      =>  $inspectionName,
                'unit_price'           =>  $unitPrice,
                'unit'                 =>  I('post.unit','','htmlspecialchars'),
                'cost'                 =>  $cost,
                'class'                =>  $class ? $class['dictionary_name'] : '系统默认',
                'class_id'             =>  $class ? $class['did'] : 0,
                'create_time'          =>  time(),
            );
            $result = $this->inspectionfee_model->addInspection($data);
            if ($result) {
                $this->ajaxSuccess('添加成功');
            } elseif ($this->inspectionfee_model->getError()) {
                $this->ajaxError($this->inspectionfee_model->getError());
            } else {
                $this->ajaxError('添加失败');
            }
        }else{ //显示添加页面
            $hospitalId = $this->hospitalInfo['uid'];
            $map = [
                'parent_id' => 16, //字典项目分类id
                'hid' => array('in', [0,$hospitalId]),
            ];
            $classLists = $this->inspectionfee_model->getClassLists($map);
            $this->ajaxSuccess('', $classLists);
        }
    }

    /**
     * 检查项目修改
     * Author: doreen
     */
    public function editInspection()
    {
        if(IS_POST){ //保存编辑的检查项目
            $mid = $this->userInfo['uid'];
            $insId = I('post.ins_id','','intval');
            $inspectionName = I('post.inspection_name','','htmlspecialchars');
            //保存前去重
            $condition = array(
                'hid' => $this->hospitalInfo['uid'],
                'ins_id' => array('NEQ', $insId),
            );
            $inspectionList = $this->inspectionfee_model->getInspectionfee($condition);
            foreach ($inspectionList as $key => $value) {
                if ($value['inspection_name'] == $inspectionName) {
                    $this->ajaxError('项目名称已存在');
                }
            }
            $unitPrice  = number_format(I('post.unit_price'),2);
            $cost  = !empty(I('post.cost')) ? number_format(I('post.cost'),2) : I('post.cost');
            if(!is_numeric($unitPrice)) $this->ajaxError('单价金额为整数或小数');
            if(!empty(I('post.cost')) && !is_numeric($cost)) $this->ajaxError('成本金额为整数或小数');
            $classId = I('post.class_id','','intval');
            //保存编辑的检查项目
            if ($classId) {
                $classInfo = D('his_dictionary')->findDictionaryById($classId);
                $class = $classInfo['dictionary_name'];
                $class_id = $classInfo['did'];
            } else {
                $classInfo = $this->inspectionfee_model->getInspectionInfoById($insId);
                $class = $classInfo['class'];
                $class_id = $classInfo['did'];
            }
            $data = array(
                'mid'                  =>  $mid,
                'inspection_name'      =>  $inspectionName,
                'unit_price'           =>  $unitPrice,
                'unit'                 =>  I('post.unit','','htmlspecialchars'),
                'cost'                 =>  $cost,
                'class'                =>  $class,
                'class_id'             =>  $class_id,
                'update_time'          =>  time(),
            );
            $map = [
                'ins_id' => $insId,
            ];
            $result = $this->inspectionfee_model->editInspection($map, $data);
            if ($result) {
                $this->ajaxSuccess('修改成功');
            } elseif ($this->inspectionfee_model->getError()) {
                $this->ajaxError($this->inspectionfee_model->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        }else{ //编辑页面显示
            $insId = I('get.ins_id','','intval');
            $hospitalId = $this->hospitalInfo['uid'];
            $inspectionInfo = $this->inspectionfee_model->getInspectionInfoById($insId);
            $map = [
                'parent_id' => 16,
                'hid' => array('in', [0,$hospitalId]),
            ];
            $classLists = $this->inspectionfee_model->getClassLists($map);
            $data = [
                'inspectionInfo' => $inspectionInfo,
                'classLists' => $classLists,
            ];
            $this->ajaxSuccess('',$data);
        }
    }

    /**
     * 检查项目删除
     * Author: doreen
     */
    public function deleteInspection()
    {
        if(IS_AJAX){
            $insId = I('post.ins_id','','intval');
            $res = $this->inspectionfee_model->deleteInspection($insId);
            $res ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
        }
    }

    /**
     * 检查项目统计
     * Author: doreen
     */
    public function inspectionStatistics()
    {
        $hid = $this->hospitalInfo['uid'];
        //合计检查项数目及收入
        $searchContent = [
            'startTime' => !empty(I('startTime')) ? strtotime(I('startTime')) : strtotime(date('Y-m-d 0:0:0', time())),
            'endTime' => !empty(I('endTime')) ? strtotime(I('endTime').'23:59:59') : time(),
        ];
        $groupMap['o.addtime'] = array(array('gt', $searchContent['startTime']), array('lt', $searchContent['endTime']));
        $inspectionSum = $this->inspectionfee_model->inspectionStatistics($hid, $groupMap);
        //各检查项收入
        $inspectionList = $this->inspectionfee_model->inspectionStatistics($hid, $groupMap, 2);
        $inspectionName = [];
        $inspectionAmount = [];
        foreach ($inspectionList as $key => $value) {
            $inspectionName[] = $value['goods_name'];
            $inspectionAmount[] = $value['amount'];
        }
        if (IS_POST) {
            $data['inspectionSum'] = $inspectionSum[0];
            $data['inspectionName'] = $inspectionName;
            $data['inspectionAmount'] = $inspectionAmount;
            $data['inspectionList'] = $inspectionList;
            $this->ajaxSuccess('请求成功', $data);
        } else {
            $this->display();
        }
    }

}