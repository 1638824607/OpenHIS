<?php
/**
 * Created by PhpStorm.
 * User: wsl
 * Date: 2017/11/15
 * Time: 9:01
 */

if(!is_cli())exit('cli mode only!');

define( 'APP_PATH', dirname(__FILE__).'/Application/' );
define('APP_MODE','cli');

require dirname( __FILE__).'/ThinkPHP/ThinkPHP.php';


/*
判断当前的运行环境是否是cli模式
*/
function is_cli(){
    return preg_match("/cli/i", php_sapi_name()) ? true : false;
}