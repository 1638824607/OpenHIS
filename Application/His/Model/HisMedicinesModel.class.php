<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy && doreen
// +----------------------------------------------------------------------

namespace His\Model;
use Common\Model\BaseModel;

/**
 * 药品信息model
 * HisMedicinesModel
 * Author: zcy && doreen
 */
class HisMedicinesModel extends BaseModel
{
    /**
     * 全部药品信息列表
     * @param array $data
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getAllMedicinesLists( $search = [], $data = [])
    {
        $where = $data ? array('medicines_id' => array('not in', $data)) : '';
        if ($where) {
            $where = array_merge($where, $search);
        } else {
            $where = $search;
        }
        $count = $this->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->where($where)
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    /**
     * 当前诊所已添加的药品信息列表
     * @param string $hid
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getMedicinesLists($hid = '', $search = [])
    {
        $where = array(
            'r.hospital_id' => $hid,
        );
        if ($search) {
            $where['_complex'] = $search;
        }
        $join = "RIGHT JOIN __HIS_HOSPITAL_MEDICINES_RELATION__ r ON r.medicines_id = m.medicines_id";
        $field = "m.medicines_number,m.medicines_name,m.medicines_class,m.prescription_type,m.unit,m.conversion,m.keywords,m.producter,r.hmr_id,r.create_time";
        $count = $this->alias('m')->join($join)->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('m')
            ->join($join)
            ->field($field)
            ->where($where)
            ->order('r.create_time DESC, r.medicines_id DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    public function getAddMedicinesInfo($hid, $search = [])
    {
        $where = array(
            'r.hospital_id' => $hid,
        );
        if ($search) {
            $where['_complex'] = $search;
        }
        $join = "RIGHT JOIN __HIS_HOSPITAL_MEDICINES_RELATION__ r ON r.medicines_id = m.medicines_id";
        $field = "m.medicines_number,m.medicines_name,m.medicines_class,m.prescription_type,m.unit,m.conversion,m.keywords,m.producter,r.hmr_id,r.create_time";
        $list = $this
            ->alias('m')
            ->join($join)
            ->field($field)
            ->where($where)
            ->find();
        return $list ? $list : false;
    }

    /**
     * 当前诊所已添加的药品id
     * @param int $hid
     * @return mixed
     * Author: doreen
     */
    public function getMedicinesIdLists($hid = 0)
    {
        $where = array(
            'r.hospital_id' => $hid,
        );
        $join = "RIGHT JOIN __HIS_HOSPITAL_MEDICINES_RELATION__ r ON r.medicines_id = m.medicines_id";
        $field = "m.medicines_id";
        $list = $this
            ->alias('m')
            ->join($join)
            ->field($field)
            ->where($where)
            ->select();
        return $list;
    }

    /**
     * 添加药品信息
     * @param array $data
     * @return bool|string
     * Author: doreen
     */
    public function addMedicines($data = [])
    {
        $this->startTrans();
        $relation = M('his_hospital_medicines_relation');
        $insertId = $relation->addAll($data);
        if ($insertId) {
            $this->commit();
            return $insertId;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * 删除药品信息
     * @param int $rid
     * @return bool
     * Author: doreen
     */
    public function deleteMedicines($rid = 0)
    {
        $this->startTrans();
        $relation = M('his_hospital_medicines_relation');
        if ($relation->where('hmr_id = %d', array('hmr_id' => $rid))->find()) {
            $deleteMedicines = $relation->where('hmr_id = %d', array('hmr_id' => $rid))->delete();
            if ($deleteMedicines) {
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