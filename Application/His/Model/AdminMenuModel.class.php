<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq
// +----------------------------------------------------------------------
namespace His\Model;
use Common\Model\BaseModel;

/**
 * 用户菜单相关操作
 * AdminMenuModel
 * Author: gmq
 */
class AdminMenuModel extends BaseModel
{
    protected $tableName = 'his_auth_rule';

    /**
     * 显示菜单
     * @param int $type
     * @return mixed
     * Author: gmq
     */
    public function selectAllMenu($type=1)
    {
        $where = array(
            'status'  => parent::NORMAL_STATUS,
            'is_menu' => 1,
        );

        if($type == 2){
            unset($where['is_menu']);
        }

        return $this->where($where)->select();


    }

    /**
     * 根据规则id数组获取顶级菜单
     * @param $rules_arr
     * @param int $is_menu
     * @return mixed
     * Author: gmq
     */
    public function getMenus0($rules_arr ,$is_menu=1)
    {
        $where = array(
            'id' => array('in', $rules_arr),
            'is_menu' => 1,
            'status' => 1,
            'pid' =>0
        );

        return $this->where($where)->order('order_list asc')->select();
    }

    /**
     *  根据规则pid获取子权限列表
     * @param $rules_arr
     * @param $pid
     * @param int $is_menu
     * @return mixed
     * Author: gmq
     */
    public function getMenusByPid($rules_arr ,$pid,$is_menu=1)
    {
        $where = array(
            'id' => array('in', $rules_arr),
            'is_menu' => 1,
            'status' => 1,
            'pid' => array('eq',$pid)
        );
        return $this->where($where)->select();
    }
}