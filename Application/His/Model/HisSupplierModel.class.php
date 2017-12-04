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
 * 供应商model
 * HisSupplierModel
 * Author: doreen
 */
class HisSupplierModel extends BaseModel
{
    //自动验证
    protected $_validate=array(
        array('supplier_name', 'require', '供应商名称不能为空！', self::EXISTS_VALIDATE),
        array('contact_name', 'require', '联系人姓名不能为空！', self::EXISTS_VALIDATE),
        array('contact_mobile', '/^((\(\d{2,3}\))|(\d{3}\-))?((13\d{9})|(15\d{9})|(18\d{9}))$/', '手机号格式不正确', self::EXISTS_VALIDATE),
        array('contact_telephone', '/^0[0-9]{2,3}-[0-9]{8}/', '座机号码格式不正确', self::VALUE_VALIDATE),
    );

    /**
     * 供应商列表
     * @param string $hid
     * @param array $search
     * @return array
     * Author: doreen
     */
    public function getSupplierLists($hid = '', $search = [])
    {
        $where = array(
            'hospital_id' => $hid,
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
     * 添加供应商
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addSupplier($data = [])
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
     * 修改供应商
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function editSupplier($map = [], $data = [])
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
            if($supplier = $this->where($map)->find()){
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
     * 根据id查看供应商
     * @param int $sid
     * @return bool|mixed
     * Author: doreen
     */
    public function getSupplierInfoById($sid = 0)
    {
        if ($sid) {
            $supplierInfo = $this->where('sid = %d', array('sid' => $sid))->find();
            return $supplierInfo ? $supplierInfo : false;
        } else {
            return false;
        }
    }

    /**
     * 删除供应商
     * @param int $sid
     * @return bool
     * Author: doreen
     */
    public function deleteSupplier($sid = 0)
    {
        $this->startTrans();
        if ($this->where('sid = %d', array('sid' => $sid))->find()) {
            $deleteSupplier = $this->where('sid = %d', array('sid' => $sid))->delete();
            if ($deleteSupplier) {
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
     * 获取当前医院的所有供应商
     * @param array $condition
     * @return mixed
     * Author: doreen
     */
    public function getSupplier($condition = array())
    {
        $where = array();
        $where = array_merge($where, $condition);
        $result =  $this
            ->where($where)
            ->select();
        return $result;
    }

}