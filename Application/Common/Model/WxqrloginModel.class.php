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
 * 微信二维码登录基础表
 * WxqrloginModel
 * Author: wsl
 */
class WxqrloginModel extends BaseModel
{
    protected $tableName = 'his_wxqrlogin';

    /**
     * 获取一条数据
     * @param $id
     * @return mixed|string
     * Author: wsl
     */
    public function getOne($id)
    {
        if(empty($id))return '';
            $where = array(
                'id' => $id
            );
        return $this->where($where)->find();
    }

    /**
     * 删除
     * @param $id
     * @param string $f
     * @return mixed
     * Author: wsl
     */
    public function delOne($id,$f='id'){
        return $this->where(array($f=>$id))->delete();
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
     * 添加一条二维码登录记录
     * @return mixed
     * Author: wsl
     */
    public function addqrlog()
    {
        return $this->add(array('addtime'=>time(),'createdate'=>date('Y-m-d')));
    }

}