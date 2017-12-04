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
 * 处方附加费model
 * HisPrescriptionExtrachargesModel
 * Author: doreen
 */
class HisPrescriptionExtrachargesModel extends BaseModel
{
    //自动验证
    protected $_validate=array(
        array('extracharges_name', 'require', '费用名称不能为空！', self::EXISTS_VALIDATE),
        array('fee', 'require', '附加费用不能为空！', self::EXISTS_VALIDATE),
        array('type', 'require', '处方类型！', self::EXISTS_VALIDATE),
    );

    /**
     * 处方附加费列表
     * @param string $hid
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getExtraChargesList($hid = '', $search = [])
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
     * 添加处方附加费
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addExtraCharges($data = [])
    {
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        if ( !$data = $this->create($data) ) {
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
     * 修改处方附加费
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function editExtraCharges($map = [], $data = [])
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
            if($extraCharges = $this->where($map)->find()){
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
     * 根据id查看处方附加费信息
     * @param int $preId
     * @return bool|mixed
     * Author: doreen
     */
    public function getChargesInfoById($preId = 0)
    {
        if ($preId) {
            $chargesInfo = $this->where('pre_id = %d', array('pre_id' => $preId))->find();
            return $chargesInfo ? $chargesInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 删除处方附加费
     * @param int $preId
     * @return bool
     * Author: doreen
     */
    public function deleteExtraCharges($preId = 0)
    {
        $this->startTrans();
        if ($this->where('pre_id = %d', array('pre_id' => $preId))->find()) {
            $deleteCharges = $this->where('pre_id = %d', array('pre_id' => $preId))->delete();
            if ($deleteCharges) {
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
     * 获取当前医院所有的处方附加费
     * @param array $condition
     * @return mixed
     * Author: doreen
     */
    public function getExtracharges($condition = array())
    {
        $where = array();
        $where = array_merge($where, $condition);
        $result =  $this
            ->where($where)
            ->select();
        return $result;
    }

}