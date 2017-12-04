<?php
namespace His\Model;
use Common\Model\BaseModel;
#药品表
class GoodsModel extends BaseModel
{
    protected $tableName = 'goods';

    #获取一条数据
    public function getOne($id,$f='goods_id')
    {
        if(empty($id))return '';
            $where = array(
                $f => $id
            );
        return $this->where($where)->find();
    }

    public function updateOne($data,$id,$f='id'){
        $where = array(
            $f => $id,
        );
        return $this->where($where)->save($data);
    }

    public function getList($arr)
    {
        return $this->where($arr)->select()->limit(5);
    }

}