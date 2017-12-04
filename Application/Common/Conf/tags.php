<?php
/**
 * 行为扩展
 * @Author   malixiao
 * @DateTime 2017-08-16
 */
return array(

    'app_begin' => array(
		    'Behavior\CronRunBehavior',//定时任务行为扩展
		    'Behavior\CheckLangBehavior'//语言包行为扩展
	),
);