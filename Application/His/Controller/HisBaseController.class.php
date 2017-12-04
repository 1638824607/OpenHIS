<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq
// +----------------------------------------------------------------------
namespace His\Controller;
use Common\Controller\BaseController;
use Think\Auth;

/**
 * 公共控制器
 * HisBaseController
 * Author: gmq
 */
class HisBaseController extends BaseController {

    protected $userInfo;#医护人员
    protected $hospitalInfo;#医院信息

    public function _initialize()
    {
        parent::_initialize();

        $user_info = session('user_info');

        if(!$user_info['uid'])
        {
            $this->redirect('Login/index');
        }

        $name = CONTROLLER_NAME . '/' . ACTION_NAME;
        if(CONTROLLER_NAME != 'Index')
        {
            $auth = new Auth();
            $auth_result = $auth->check($name, $user_info['uid']);

            if($auth_result === false)
            {
                if(IS_AJAX)
                {

                    $this->ajaxError(L('_VALID_ACCESS_'));
                }
                else
                {
                    exit(L('_VALID_ACCESS_').',uid:'.$user_info['uid'].','.$name);
                    $this->error(L('_VALID_ACCESS_').',uid:'.$user_info['uid']);
                }

            } 
        }

        $this->userInfo =$user_info;
        $this->hospitalInfo = session('hospital_info');
    }

}