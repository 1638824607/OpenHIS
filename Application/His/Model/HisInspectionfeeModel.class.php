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
 * 检查项目model
 * HisInspectionfeeModel
 * Author: doreen
 */
class HisInspectionfeeModel extends BaseModel
{
    //自动验证
    protected $_validate=array(
        array('inspection_name', 'require', '项目名称不能为空！', self::EXISTS_VALIDATE),
        array('unit_price', 'require', '项目单价不能为空！', self::EXISTS_VALIDATE),
    );

    /**
     * 检查项目列表
     * @param string $hid
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getInspectionLists($hid = '', $search = [])
    {
        $where = array(
            'hid' => $hid,
        );
        $where = array_merge($where, $search);
        $count = $this->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->where($where)
            ->order('create_time DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        return array('page' => $pager->getPage() , 'list' => $list, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     * 添加检查项目
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addInspection($data = [])
    {
        $this->startTrans();
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        // 对data数据进行验证
        if ( !$data = $this->create($data) ) {
            //验证不通过返回错误
            return false;
        }else{
            $insertId = $this->add($data);
            if($insertId){
                $this->commit();
                return $insertId;
            } else {
                $this->rollback();
                return false;
            }
        }
    }

    /**
     * 修改检查项目
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function editInspection($map = [], $data = [])
    {
        $this->startTrans();
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        // 对data数据进行验证
        if (!$data = $this->create($data)) {
            //验证不通过返回错误
            return false;
        } else {
            if($inspection = $this->where($map)->find()){
                $result = $this->where($map)->save($data);
                if($result){
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
     * 根据id查看检查项目
     * @param int $insId
     * @return bool|mixed
     * Author: doreen
     */
    public function getInspectionInfoById($insId = 0)
    {
        if ($insId) {
            $inspectionInfo = $this->where('ins_id = %d', array('ins_id' => $insId))->find();
            return $inspectionInfo ? $inspectionInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 删除检查项目
     * @param int $insId
     * @return bool
     * Author: doreen
     */
    public function deleteInspection($insId = 0)
    {
        $this->startTrans();
        if ($this->where('ins_id = %d', array('ins_id' => $insId))->find()) {
            $deleteInspection = $this->where('ins_id = %d', array('ins_id' => $insId))->delete();
            if ($deleteInspection) {
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
     * 获取项目类型列表
     * @param array $map
     * @return mixed
     * Author: doreen
     */
    public function getClassLists($map = [])
    {
        $where = array();
        $where = array_merge($where, $map);
        $result = M('his_dictionary')
            ->where($where)
            ->order("create_time desc,update_time desc")
            ->field('dictionary_name,did')
            ->select();
        return $result;
    }

    /**
     * 获取检查项统计
     * @param int $hid
     * @param array $map
     * @param int $type
     * @return mixed
     * Author: doreen
     */
    public function inspectionStatistics($hid = 0, $map = [], $type = 1)
    {
        $where = array(
            's.type_id' => 2,
            'o.hospital_id' => $hid,
            'p.status' => array('in','1,6'),
        );
        $where = array_merge($where, $map);
        $careOrderSub = D('his_care_order_sub');
        $join = "JOIN __HIS_CARE_ORDER__ o ON o.id = s.fid JOIN __HIS_CARE_PKG__ p ON p.id = o.pkg_id";
        if ($type == 2) {
            //合计检查项数目及收入
            $result = $careOrderSub->alias('s')
                ->join($join)
                ->where($where)
                ->field('SUM(s.amount) as amount,SUM(s.num) as num,s.goods_name')
                ->group('s.goods_id')
                ->select();
        } else {
            //各检查项收入
            $result = $careOrderSub->alias('s')
                ->join($join)
                ->where($where)
                ->field('SUM(s.amount) as amount,SUM(s.num) as num')
                ->select();
        }
        return $result;
    }

    /**
     * 获取当前医院的所有检查项目
     * @param array $condition
     * @return mixed
     * Author: doreen
     */
    public function getInspectionfee($condition = array())
    {
        $where = array();
        $where = array_merge($where, $condition);
        $result =  $this
            ->where($where)
            ->select();
        return $result;
    }

}