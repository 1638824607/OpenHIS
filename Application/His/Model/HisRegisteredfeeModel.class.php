<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Model;

use Common\Model\BaseModel;

class HisRegisteredfeeModel extends BaseModel
{
    //自动验证
    protected $_validate = array(
        array('registeredfee_name', 'require', '挂号类型不能为空！', self::EXISTS_VALIDATE),
        array('registeredfee_fee', 'require', '挂号费用不能为空！', self::EXISTS_VALIDATE),
    );
    //自动完成
    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
    );

    /**
     * @param string $company_id 诊所id
     * @param string $field 需要的字段
     * @param int $num
     * @Name     getRegisteredfeeList
     * @explain  获取挂号费用设置列表
     * @author   zuochuanye
     * @Date    2017/10/23
     * @return array
     */
    public function getRegisteredfeeList($company_id = '', $condition = [], $field = '*')
    {
        $where = array(
            'company_id' => $company_id,
        );
        if($condition) $where = array_merge($where, $condition);
        $count = $this
            ->alias('r')
            ->where($where)
            ->count();
        $pager = new_page($count, 10,1);
        $pager_str = $pager->showHis();
        $list = $this
            ->alias('r')
            ->field($field)
            ->where($where)
            ->order('create_time DESC')
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        foreach ($list as $k => $v) {
            $sub_list[$k] = M('his_registeredfee_sub')->field('sub_registeredfee_name')->where(array('reg_id' => $v['reg_id']))->select();
        }
        foreach ($sub_list as $kk => $vv) {
            foreach ($vv as $vk => $vg) {
                $sub_registeredfee_names[$kk][] = $vg['sub_registeredfee_name'];
            }
        }
        foreach ($sub_registeredfee_names as $k => $v) {
            $list[$k]['registeredfee_names'] = $list[$k]['registeredfee_name'] . '+' . implode('+', $v);
        }
        return array('page' => $pager->getPage(), 'list' => $list, 'count' => $count, 'pager_str' => $pager_str);
    }

    /**
     * @param array $data Registeredfee表中所需数据
     * @param array $sub_data Registeredfee_sub表中所需数据
     * @Name     addRegisteredfee
     * @explain  添加挂号费用设置
     * @author   zuochuanye
     * @Date      2017/10/23
     * @return bool|mixed
     */
    public function addRegisteredfee($data = [], $sub_data = [])
    {
        $this->startTrans();
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        if (!$data = $this->create($data)) {
            return false;
        } else {
            $registeredfee_id = $this->add($data);
            if (!empty($sub_data)) {
                foreach ($sub_data as $k => $v) {
                    $sub_data[$k]['reg_id'] = $registeredfee_id;
                }
                $Registeredfee_sub_id = M('his_registeredfee_sub')->addAll($sub_data);
                if ($Registeredfee_sub_id && $registeredfee_id) {
                    $this->commit();
                    return $registeredfee_id;
                } else {
                    $this->rollback();
                    return false;
                }
            } else {
                if ($registeredfee_id) {
                    $this->commit();
                    return $registeredfee_id;
                } else {
                    $this->rollback();
                    return false;
                }
            }

        }
    }

    /**
     * @param string $condition 查询条件
     * @param string $fields 查询所需字段
     * @Name     getRegisteredfeeInfoByReg_id
     * @explain  根据reg_id获取表中的数据
     * @author   zuochuanye
     * @Date     2017/10/23
     * @return bool
     */
    public function getRegisteredfeeInfoByReg_id($condition = [], $fields = '')
    {
        $where = array();
        if($condition) $where = array_merge($where, $condition);
        $info = $this
            ->alias('r')
            ->field($fields)
            ->where($where)
            ->find();
        $info['sub_info'] = M('his_registeredfee_sub')->where(array('reg_id' => $info['reg_id']))->select();
        return $info ? $info : false;
    }

    /**
     * @param array $data Registeredfee表中所需数据
     * @param array $sub_data Registeredfee_sub表中所需数据
     * @param string $reg_id Registeredfee表中id
     * @Name     editRegisteredfee
     * @explain  修改挂号费用设置
     * @author   zuochuanye
     * @Date     2017/10/23
     * @return bool
     */
    public function editRegisteredfee($data = [], $sub_data = [], $reg_id = '')
    {
        $this->startTrans();
        // 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        foreach ($sub_data as $s => $t) {
            foreach ($t as $y => $i) {
                $sub_data[$s][$y] = trim($i);
            }
        }
        // 对data数据进行验证
        if (!$data = $this->create($data)) {
            //验证不通过返回错误
            return false;
        } else {
            if ($this->where(array('reg_id' => $reg_id))->count() > 0) {
                $edit_registeredfee_return = $this->where(array('reg_id' => $reg_id))->save($data);
                if (is_bool($edit_registeredfee_return) && !$edit_registeredfee_return) {
                    $this->rollback();
                    return false;
                } else {
                    foreach ($sub_data as $j => $l) {
                        unset($sub_data[$j]['reg_sub_id']);
                        if ($l{'reg_sub_id'}) {
                            unset($sub_data[$j]['reg_id']);
                            $sub_data_return = M('his_registeredfee_sub')->where(array('reg_sub_id' => $l['reg_sub_id']))->save($sub_data[$j]);
                        } else {
                            $sub_data[$j]['reg_id'] = $reg_id;
                            $sub_data_return = M('his_registeredfee_sub')->add($sub_data[$j]);
                        }
                        if (is_bool($sub_data_return) && !$sub_data_return) {
                            $this->rollback();
                            return false;
                        }
                    }
                    $this->commit();
                    return true;
                }
            } else {
                return false;
            }

        }
    }

    /**
     * @param string $reg_id 挂号费用ID
     * @Name     deleteRegisteredfee
     * @explain     删除挂号费用
     * @author   zuochuanye
     * @Date        2017/10/24
     * @return bool
     */
    public function deleteRegisteredfee($reg_id = '')
    {
        $this->startTrans();
        if ($this->where(array('reg_id' => $reg_id))->count()) {
            $deleteRegisteredfee_return = $this->where(array('reg_id' => $reg_id))->delete();
            if (M('his_registeredfee_sub')->where(array('reg_id' => $reg_id))->count()) {
                $deleteRegisteredfee_sub_return = M('his_registeredfee_sub')->where(array('reg_id' => $reg_id))->delete();
            } else {
                $deleteRegisteredfee_sub_return = true;
            }
            if ($deleteRegisteredfee_return && $deleteRegisteredfee_sub_return) {
                $this->commit();
                return true;
            } else {
                $this->rollback();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $company_id  诊所ID
     * @param string $field       查询数据
     * @Name     getRegisteredfeeAllList
     * @explain  获取诊所内全部的挂号费用
     * @author   zuochuanye
     * @Date     2017/11/27
     * @return bool|mixed
     */
    public function getRegisteredfeeAllList($company_id = '', $field = '')
    {
        $where = array(
            'company_id' => $company_id,
        );
        $info = $this
            ->field($field)
            ->where($where)
            ->select();
        return $info ? $info : false;
    }

}