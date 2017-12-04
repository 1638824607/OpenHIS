<?php
//*************************************微信相关设置***********************************
//这里填入的你域名
define("_URL_","http://his.dzmtech.com/");

return array(

	'COOKIE_PREFIX' => 'his_', //cookie的前缀
	'COOKIE_EXPIRE' => 3600*30*24, //cookie的生存时间

    'SESS_PREFIX' => 'his_auth_', //session的前缀
	'SESS_EXPIRE' => 3600*30*24, //session的生存时间

	'DEFAULT_MODULE'         =>  'His',  // 默认模块
	'DEFAULT_CONTROLLER'     =>  'Index', // 默认控制器名称
	'DEFAULT_ACTION'         =>  'index', // 默认操作名称
    #'PUB_FILE_DIR'=>_URL_.'Public/his/',
    'PUB_FILE_DIR'=>_URL_.'Public/his/',


    'URL_ROUTER_ON'   => true, //开启路由
    'URL_ROUTE_RULES' => array( //定义路由规则
        'new/:hid\d'    => 'Login/index',
        'Doctor_pkgPay/:pkg_id\d'    => 'Doctor/pkgPay',
    ),

    'SHOW_PAGE_TRACE'=>false,
    'REGISTRATION_LOG_PATH' =>  WEB_ROOT_PATH.'Application/Runtime/Logs/His/',

    #订单状态
    'ORDER_STATUS'=>[
0=>'未支付',
1=>'已支付',
2=>'确认收款',
3=>'申请退款',
4=>'已退款',
5=>'部分支付',
6=>'完成交易',
7=>'部分退款',
    ],

    #ThinkPHP表单令牌
    'TOKEN_ON'=>false,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true

    //支付类型
    'OP_PLACE'=>[
        '',
        '售药',
        '检查项目',
        '附加费用',
        '挂号'
    ],
);