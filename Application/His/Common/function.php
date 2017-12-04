<?php
header("Content-type:text/html;charset=utf-8");
/**
 * 去除多维数组中的空值
 * @author
 * @return mixed
 * @param $arr 目标数组
 * @param array $values 去除的值  默认 去除  '',null,false,0,'0',[]
 */
function filter_array($arr, $values = array('', null, false, 0, '0',array())) {
    foreach ($arr as $k => $v) {
        if (is_array($v) && count($v)>0) {
            $arr[$k] = filter_array($v, $values);
        }
        foreach ($values as $value) {
            if ($v === $value) {
                unset($arr[$k]);
                break;
            }
        }
    }
    return $arr;
}