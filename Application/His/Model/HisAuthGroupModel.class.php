<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: gmq
// +----------------------------------------------------------------------
namespace His\Model;
use Common\Model\BaseModel;

/**
 * 获得角色对应的权限
 * HisAuthGroupModel
 * Author: gmq
 */
class HisAuthGroupModel extends BaseModel
{
    protected $tableName = 'his_auth_group';

    /**
     * 获取角色列表
     * @param array $search
     * @param int $page
     * @param int $pageoffset
     * @param int $pagesize
     * @param int $user_id
     * @return array
     * Author: gmq
     */
    public function getGroupList($search=array(),$user_id = 0)
    {
        $where = array(
            'status' => parent::NORMAL_STATUS,
        );
        $where = array_merge($where,$search);
        $count = $this->where($where)->count();
        $pager       = new_page($count,10,1);
        $pager_str  = $pager->showHis();//获得页码字符串
        if(!$user_id){
            $list = $this->where($where)->limit($pager->firstRow.','.$pager->listRows)->order('is_manage DESC')->select();
        }else{
            $where = array(
                'a.status' => 1,
            );
            $field = "a.*,b.uid";
            $join = "LEFT JOIN __ADMIN_AUTH_GROUP_ACCESS__ b ON a.id=b.group_id AND b.uid={$user_id}";
            $list = $this->alias('a')->field($field)->join($join)->where($where)->order('a.is_manage DESC,a.id DESC')->select();
        }
        return array('page' => $pager->getPage() , 'list' => $list, 'count'=>$count, 'pager_str'=>$pager_str);
    }

    /**
     *
     * @param $id
     * @return mixed
     * Author: gmq
     */
    public function findGroup($id)
    {
        $where = array(
            'id'     => $id,
            'status' => parent::NORMAL_STATUS,
        );
        return $this->where($where)->find();
    }



}