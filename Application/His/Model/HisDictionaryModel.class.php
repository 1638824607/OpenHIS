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
 * 字典Model
 * HisDictionaryModel
 * Author: doreen
 */
class HisDictionaryModel extends BaseModel
{
    //自动验证
    protected $_validate=array(
        array('dictionary_name', 'require', '字典名称不能为空！', self::EXISTS_VALIDATE),
    );

    /**
     * 字典列表
     * @param array $map
     * @return array
     * Author: doreen
     */
    public function getDictionaryLists($map = [])
    {
        $where = array();
        $where = array_merge($where, $map);
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
     * 字典一二级列表
     * @param array $map
     * @param string $order
     * @return string
     * Author: doreen
     */
    public function getLevelLists($map = [], $order = '')
    {
        $where = array();
        $where = array_merge($where, $map);
        $result =  $this
            ->where($where)
            ->order($order)
            ->select();
        return $result ? $result : '';
    }

    /**
     * 添加字典
     * @param array $data
     * @return bool|mixed
     * Author: doreen
     */
    public function addData($data)
    {
        $this->startTrans();
        // 对data数据进行验证
        if ( !$data = $this->create($data) ) {
            return false;
        } else {
            //验证通过
            $arr = array(
                'dictionary_name' => $data['dictionary_name'],
                'number' => $data['number'] ? $data['number'] : '',
                'hid' => $data['hid'],
                'create_time' => $data['create_time'],
                'type' => $data['type'],
                'parent_id' => $data['parent_id'],
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
     * 修改字典
     * @param array $map
     * @param array $data
     * @return bool
     * Author: doreen
     */
    public function updateData($map, $data)
    {
        // 对data数据进行验证
        if( !$data = $this->create($data) ) {
            //验证不通过返回错误
            return false;
        } else {
            if ($dictionary = $this->where($map)->find()) {
                $updateData['dictionary_name'] = $data['dictionary_name'];
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
     * 根据did取出字典信息
     * @param int $did
     * @return bool|mixed
     * Author: doreen
     */
    public function findDictionaryById($did = 0)
    {
        if ($did) {
            $dictionaryInfo = $this->where('did = %d', array('did' => $did))->find();
            return $dictionaryInfo ? $dictionaryInfo : false;
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
    public function deleteDictionary($did = 0)
    {
        $this->startTrans();
        if ($this->where('did = %d', array('did' => $did))->find()) {
            $deleteDictionary = $this->where('did = %d', array('did' => $did))->delete();
            if ($deleteDictionary) {
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
     * 获取当前医院当前分类下属字典列表
     * @param array $condition
     * @return mixed
     * Author: doreen
     */
    public function getDictionary($condition = array())
    {
        $where = array();
        $where = array_merge($where, $condition);
        $result =  $this
            ->where($where)
            ->select();
        return $result;
    }

}