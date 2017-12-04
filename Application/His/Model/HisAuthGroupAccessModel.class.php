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
 * 用户权限菜单显示操作
 * HisAuthGroupAccessModel
 * Author: gmq
 */
class HisAuthGroupAccessModel extends BaseModel
{

    /**
     * 根据用户id 获得一级菜单
     * @param $user_id
     * @return array|\multitype
     * Author: gmq
     */
    public function getUserRules0($user_id)
    {
        $where = array(
            'a.uid' => $user_id,
        );
        $join = 'LEFT JOIN __HIS_AUTH_GROUP__ b ON b.id=a.group_id';
        $rules = $this->alias('a')
            ->where($where)
            ->join($join)
            ->field('b.rules')
            ->select();

        if(!$rules){
            return array();
        }

        $rules_str = '';
        foreach($rules as $v){
            $rules_str .= $v['rules'] . ',';
        }

        $rules_str = rtrim($rules_str, ',');

        $rules_arr = array_unique(explode(',', $rules_str));

        $admin_menu_model = new AdminMenuModel();
        $menus = $admin_menu_model->getMenus0($rules_arr);

        $menus = get_column($menus, 2);
        return $menus;

    }

    /**
     * 根据用户的id获取用户所拥有权限的的一级子权限列表
     * @param $user_id
     * @param $pid
     * @return array|mixed
     * Author: gmq
     */
    public function getMenuByPid($user_id,$pid){
        $where = array(
            'a.uid' => $user_id,
        );
        $join = 'LEFT JOIN __HIS_AUTH_GROUP__ b ON b.id=a.group_id';
        $rules = $this->alias('a')
            ->where($where)
            ->join($join)
            ->field('b.rules')
            ->select();

        if(!$rules){
            return array();
        }

        $rules_str = '';
        foreach($rules as $v){
            $rules_str .= $v['rules'] . ',';
        }

        $rules_str = rtrim($rules_str, ',');

        $rules_arr = array_unique(explode(',', $rules_str));

        $admin_menu_model = new AdminMenuModel();
        $menus = $admin_menu_model->getMenusByPid($rules_arr,$pid);
        return $menus;
    }
}