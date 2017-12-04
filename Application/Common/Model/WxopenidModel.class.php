<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

namespace Common\Model;

/**
 * 微信openid与系统用户id绑定表
 * WxopenidModel
 * Author: wsl
 */
class WxopenidModel extends BaseModel
{
    protected $tableName = 'his_wxopenid';

    /**
     * 获取一条数据
     * @param $id
     * @param string $f
     * @return mixed|string
     * Author: wsl
     */
    public function getOne($id,$f='openid')
    {
        if(empty($id))return '';
            $where = array(
                $f => $id
            );
        return $this->where($where)->find();
    }

    /**
     * 更新数据
     * @param $data
     * @param $id
     * @param string $f
     * @return bool
     * Author: wsl
     */
    public function updateOne($data,$id,$f='id'){
        $where = array(
            $f => $id,
        );
        return $this->where($where)->save($data);
    }

    /**
     * 添加用户绑定
     * @param $userid
     * @param $openid
     * @param $appid
     * @return mixed
     * Author: wsl
     */
    public function addConn($userid, $openid, $appid)
    {
        return $this->add(array('appid'=>$appid,'openid'=>$openid,'userid'=>$userid));
    }

}