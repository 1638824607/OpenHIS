<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq
// +----------------------------------------------------------------------
namespace His\Controller;
/**
 * 职务管理
 * AuthGroupController
 * Author: gmq
 */
class AuthGroupController extends HisBaseController {


    /**
     * 根据角色查看权限
     * Author: gmq
     */
    public function ruleGroup()
    {
            $admin_auth_group_model = D('HisAuthGroup');
            $role_id = I('get.role_id','','intval');
            $role_name = M('HisAuthGroup')->where('id="'.$role_id.'"')->getField('title');
            $menu_model = D('AdminMenu');
            $menus = get_column($menu_model->selectAllMenu(1),2);
            $role_info = $admin_auth_group_model->findGroup($role_id);
            if($role_info['rules']){
                $rulesArr = explode(',',$role_info['rules']);
                $this->assign('rulesArr',$rulesArr);
            }
            $this->assign('menus',$menus);
            $this->assign('role_id',$role_id);
            $this->assign('role_name',$role_name);
            $this->display();
        }


}