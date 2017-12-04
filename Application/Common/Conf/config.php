<?php
return array(
//*************************************附加设置***********************************
    'SHOW_PAGE_TRACE'        => false,                           // 是否显示调试面板
    'URL_CASE_INSENSITIVE'   => false,                           // url区分大小写
    'LOAD_EXT_CONFIG'        => 'db',               // 加载网站设置文件
    'TMPL_PARSE_STRING'      => array(                           // 定义常用路径
        '__PUBLIC_HIS__'    => __ROOT__.'/Public/his',
        
    ),
//***********************************URL设置**************************************
    'MODULE_ALLOW_LIST'      => array('His'), //允许访问列表
    'URL_HTML_SUFFIX'        => '',  // URL伪静态后缀设置
    'URL_MODEL'              => 1,  //启用rewrite
    'VAR_PATHINFO'           => 's',
    'URL_PATHINFO_DEPR'      => '/',
    'LAYOUT_ON'              => true,
    'LAYOUT_NAME'            =>'Layout/layout',
    #'URL_CASE_INSENSITIVE'   =>true,
    'MODULE_DENY_LIST'       =>  array('Common','Runtime'),//禁止访问列表
    'URL_PARAMS_BIND'        =>  true, //URL变量绑定到操作方法作为参数
//***********************************SESSION设置**********************************
    'SESSION_OPTIONS'        => array(
        'name'               => 'DZMADMIN',//设置session名
        'expire'             => 24*3600*15, //SESSION保存15天
        'use_trans_sid'      => 1,//跨页传递
        'use_only_cookies'   => 0,//是否只开启基于cookies的session的会话方式
    ),
//***********************************上传设置*************************************
    'UPLOAD_DOCTOR'=>'/Upload/doctor/',//医生证件文件路径

//***********************************定时任务设置*********************************
    /* 自动运行配置 */
    'CRON_CONFIG_ON'         => false, //是否开启自动运行
    'CRON_CONFIG'            => array(
        //路径(格式同R)、间隔秒（0为一直运行）、指定一个开始时间
        '定时任务_城市联动'  => array('Common/Crons/writeCache', '3600', ''),
    ),
//**********************************语言包设置*************************************
    // 开启语言包功能
    'LANG_SWITCH_ON'         => true,
    // 自动侦测语言 开启多语言功能后有效
    'LANG_AUTO_DETECT'       => true,
    'DEFAULT_LANG'           => 'zh-cn', // 默认语言
    // 允许切换的语言列表 用逗号分隔
    'LANG_LIST'              => 'zh-cn',
    // 默认语言切换变量
    'VAR_LANGUAGE'           => 'l',
//**********************************默认控制器***************************************
     'DEFAULT_MODULE'         =>  'His',  // 默认模块
     'DEFAULT_CONTROLLER'     =>  'Index', // 默认控制器名称
     'DEFAULT_ACTION'         =>  'index', // 默认操作名称

//***********************************开启域名部署*************************************
    'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'    =>    array(   
        'his.dzmtech.com'  => 'His',  // his.dzmtech.com域名指向Admin模块  门诊系统
    ),

    'DEV_IP'=>'',#开发IP
    'SPBILL_CREATE_IP'=>'',#微信企业付款服务器IP

    'DEFAULT_HOSPITAL_ID'=>1,#默认医院id

    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => false,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => 'dzm',     // 缓存前缀
    'DATA_CACHE_TYPE'       => 'File',  // 数据缓存类型
    'DATA_CACHE_PATH'       => TEMP_PATH,// 缓存路径设置 (仅对File方式有效)
    'DATA_CACHE_SUBDIR'     => false,  // 使用子目录缓存 (根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       => 1,        // 子目录缓存级别
    'DATA_CACHE_KEY'=>'think_dzm',#文件安全


    #redis
    #'DATA_CACHE_PREFIX' => 'Redis_',//缓存前缀
    #'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
    'REDIS_RW_SEPARATE' => true, //Redis读写分离 true 开启
    'REDIS_HOST'=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    'REDIS_PORT'=>'6379',//端口号
    'REDIS_TIMEOUT'=>'300',//超时时间
    'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接
    'REDIS_AUTH'=>'',//AUTH认证密码

    #网站基本信息
    'WEB_INFO'=>[
        'website_name'=>'大宅门云诊所系统', #网站名称
        'def_kw'=>'',   #默认网页 mata keywords
        'def_desc'=>''  #默认网页 mata description
    ],

    //***********************************邮件设置**************************************
    'MAIL_SERVER' =>'', #邮件服务器
    'MAIL_USER_NAME' =>'', #邮件用户名
    'MAIL_PASSWORD' =>'', #邮件密码
    'MAIL_PORT' =>'465',#邮件端口号吗
    //***********************************短信设置**************************************
    'ALIYUN_MEASSAGE_APPKEY'=>'', #阿里云短信发送key
    'ALIYUN_MEASSAGE_SECRET'=>'', #阿里云短信发送secret
    'ALIYUN_SIGNAME'=>'明医复诊平台',          #阿里云短信发送签名
    'ALIYUN_TEMPLATE_CODE'=>''          #阿里云短信发送模板
);
