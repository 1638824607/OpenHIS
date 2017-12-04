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
 * 科室管理
 * DepartmentController
 * Author: doreen
 */
class DepartmentController extends HisBaseController
{
    protected $department_model;

    public function _initialize()
    {
        parent::_initialize();
        $department_model = D('HisDepartment');
        $this->department_model = $department_model;
    }

    /**
     * 科室管理列表
     * Author: doreen
     */
    public function index()
    {
        //搜索内容
        $search_content = I('post.search','','htmlspecialchars');
        if(!empty($search_content)) {
            $search['department_name'] = array('like','%'.$search_content.'%');
        } else {
            $search = array();
        }
        //取出科室列表
        $hospitalId = $this->hospitalInfo['uid'];
        $departmentList = $this->department_model->getDepartmentList($hospitalId, $search);
        if (IS_AJAX) {
            $this->ajaxSuccess('',$departmentList);
        } else {
            $this->display();
        }
    }

    /**
     * 科室添加
     * Author: doreen
     */
    public function addDepartment()
    {
        if(IS_AJAX) {
            $data = I();
            $hospitalId = $this->hospitalInfo['uid'];
            $startTime = strtotime(date('Y-m-d', time()));
            $endTime = time();
            //取出当前医院已保存的科室，添加时去重
            $condition = array(
                'hid' => $hospitalId,
            );
            $departmentList = $this->department_model->getDepartment($condition);
            foreach ($departmentList as $key => $value) {
                if ($value['department_name'] == $data['department_name']) {
                    $this->ajaxError('科室名称已存在');
                }
            }
            //生成科室编号
            $departmentNum = $this->makeDepartmentNum($startTime, $endTime);
            //保存添加科室
            $data = [
                'department_name' => $data['department_name'],
                'department_number' => $departmentNum,
                'hid' => $hospitalId,
                'create_time' => time(),
            ];
            $res = $this->department_model->addData($data);
            if ($res) {
                $department = $this->department_model->findNoticeById($res);
                $this->ajaxSuccess('添加成功', $department);
            } elseif ($this->department_model->getError()) {
                $this->ajaxError($this->department_model->getError());
            } else {
                $this->ajaxError('添加失败');
            }
        }
    }

    /**
     * 科室编辑
     * Author: doreen
     */
    public function editDepartment()
    {
        if (IS_POST) { //ajax提交编辑
            $data = I();
            //去重
            $condition = array(
                'hid' => $this->hospitalInfo['uid'],
                'did' => array('NEQ', I('post.did','','intval')),
            );
            $departmentList = $this->department_model->getDepartment($condition);
            foreach ($departmentList as $key => $value) {
                if ($value['department_name'] == $data['department_name']) {
                    $this->ajaxError('科室名称已存在');
                }
            }
            //保存编辑科室
            $condition['did'] = I('post.did','','intval');
            $data['update_time'] = time();
            $res = $this->department_model->updateData($condition, $data);
            if ($res) {
                $this->ajaxSuccess('修改成功');
            } elseif ($this->department_model->getError()) {
                $this->ajaxError($this->department_model->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        } else { //显示编辑页面
            $did = I('get.did','','intval');
            $departmentInfo = $this->department_model->findDepartmentInfo($did);
            $this->assign('departmentInfo',$departmentInfo);
            $this->display();
        }
    }

    /**
     * 删除科室
     * Author: doreen
     */
    public function deleteDepartment()
    {
        if (IS_AJAX) {
            $did = I('post.did','','intval');
            if ($did) {
                if ($this->department_model->deleteDepartment($did)) {
                    $this->ajaxSuccess("删除成功");
                } else {
                    $this->ajaxError("删除失败");
                }
            }
        }
    }

    /**
     * 生成科室编号
     * @param $startTime
     * @param $endTime
     * @return string
     * Author: doreen
     */
    protected function makeDepartmentNum($startTime, $endTime)
    {
        //生成科室编号
        $condition = [
            'hid' => $this->hospitalInfo['uid'],
            'create_time' => array('between', array($startTime, $endTime)),
        ];
        $count = $this->department_model->departmentCount($condition);
        $departmentNum = date('Ymd', time()) . str_pad($count + 1, 8, '0', STR_PAD_LEFT);
        return $departmentNum;
    }
}
?>