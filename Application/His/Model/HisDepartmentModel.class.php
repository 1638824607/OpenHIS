<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: doreen
// +----------------------------------------------------------------------

namespace His\Model;
use Common\Model\BaseModel;

/**
 * 科室Model
 * HisDepartmentModel
 * Author: doreen
 */
class HisDepartmentModel extends BaseModel
{
    //自动验证
    protected $_validate=array(
        array('department_name', 'require', '科室名称不能为空！', self::EXISTS_VALIDATE),
    );

    /**
     * 科室管理列表
     * @param int $hid
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getDepartmentList($hid = 0, $search = array())
    {
        $where = array(
            'hid' => $hid,
        );
        $where = array_merge($where, $search);
        $count = $this->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $result =  $this
            ->where($where)
            ->order("create_time desc,update_time desc")
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage() , 'list' => $result, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     * 当前医院科室数量
     * @param array $condition
     * @return mixed
     * Author: doreen
     */
    public function departmentCount($condition = array())
    {
        $where = array();
        $where = array_merge($where, $condition);
        return $this->where($where)->count();
    }

    /**
     * 当前医院科室
     * @param int $hid
     * @return mixed
     * Author: doreen
     */
    public function currentDepartment($hid = 0)
    {
        if ($hid) {
            $currentDepartment = $this->where('hid = %d', array('hid' => $hid))->select();
            return $currentDepartment;
        }
    }

    /**
     * 添加科室
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addData($data)
    {
        $this->startTrans();
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        // 对data数据进行验证
        if ( !$data = $this->create($data) ) {
            return false;
        } else {
            //验证通过
            $arr = array(
                'department_name' => $data['department_name'],
                'department_number' => $data['department_number'],
                'hid' => $data['hid'],
                'create_time' => $data['create_time']
            );
            $result = $this->add($arr);
            if ($result) {
                $this->commit();
                return $result;
            } else {
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * 修改科室
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function updateData($map, $data)
    {
        $this->startTrans();
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        // 对data数据进行验证
        if( !$data = $this->create($data) ) {
            //验证不通过返回错误
            return false;
        } else {
            if ($department = $this->where($map)->find()) {
                $updateData['department_name'] = $data['department_name'];
                $updateData['update_time'] = $data['update_time'];
                $result = $this->where($map)->save($updateData);
                if($result) {
                    $this->commit();
                    return true;
                } else {
                    $this->rollback();
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 根据did取出科室信息
     * @param int $did
     * @return bool|mixed
     * Author: doreen
     */
    public function findNoticeById($did = 0)
    {
        if ($did) {
            $departmentInfo = $this->where('did = %d', array('did' => $did))->find();
            return $departmentInfo ? $departmentInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 删除数据
     * @param int $did
     * @return bool
     * Author: doreen
     */
    public function deleteDepartment($did = 0)
    {
        $this->startTrans();
        if ($this->where('did = %d', array('did' => $did))->find()) {
            $deleteDepartment = $this->where('did = %d', array('did' => $did))->delete();
            if ($deleteDepartment) {
                $this->commit();
                return true;
            } else {
                $this->rollback();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取当前医院的所有科室
     * @param array $condition
     * @return mixed
     * Author: doreen
     */
    public function getDepartment($condition = array())
    {
        $where = array();
        $where = array_merge($where, $condition);
        $result =  $this
            ->where($where)
            ->select();
        return $result;
    }

}