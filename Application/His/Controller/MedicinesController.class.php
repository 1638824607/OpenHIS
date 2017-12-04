<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: doreen
// +----------------------------------------------------------------------

namespace His\Controller;

/**
 * 药品信息类
 * MedicinesController
 * Author: doreen
 */
class MedicinesController extends HisBaseController
{

    protected $medicines_model;

    public function _initialize()
    {
        parent::_initialize();
        $this->medicines_model = D('his_medicines');
    }

    /**
     * 全部药品信息列表
     * Author: doreen
     */
    public function medicinesLists()
    {
        //搜索条件
        $search_content = I('post.search','','htmlspecialchars');
        $classId = I('post.classId','','intval');
        $search = [];
        if ($search_content) {
            $search['_string'] = ' (medicines_name like "%'.$search_content.'%")  OR ( keywords like "%'.$search_content.'%") ';
        }
        $class = D('his_dictionary')->findDictionaryById($classId);
        if ($class) {
            $search['medicines_class'] = array('like','%'.$class['dictionary_name'].'%');
        }
        //已添加药品id
        $hid = $this->hospitalInfo['uid'];
        $medicinesIdLists = $this->medicines_model->getMedicinesIdLists($hid);
        $addedMedicines = [];
        foreach ($medicinesIdLists as $key => $value) {
            $addedMedicines[] = $value['medicines_id'];
        }
        //取出药品分类子项
        $map = [
            'parent_id' => 11,
        ];
        $classLists = D('his_dictionary')->getLevelLists($map);
        if ($addedMedicines) {
            $medicinesLists = $this->medicines_model->getAllMedicinesLists($search, $addedMedicines);
        } else {
            $medicinesLists = $this->medicines_model->getAllMedicinesLists($search);
        }
        if (IS_AJAX) {
            $data = [
                'classLists' => $classLists,
                'medicinesLists' => $medicinesLists,
                'search' => $search_content,
                'class_id' => $classId,
            ];
            $this->ajaxSuccess('', $data);
        } else {
            $this->display();
        }
    }

    /**
     * 当前诊所添加的药品信息列表
     * Author: doreen
     */
    public function index()
    {
        $search_content = I('post.search','','htmlspecialchars');
        $classId = I('post.classId','','intval');
        $search = [];
        if ($search_content) {
            $search['_string'] = ' (m.medicines_name like "%'.$search_content.'%")  OR ( m.keywords like "%'.$search_content.'%") ';
        }
        $class = D('his_dictionary')->findDictionaryById($classId);
        if ($class) {
            $search['m.medicines_class'] = array('like','%'.$class['dictionary_name'].'%');
        }
        //取出药品分类子项
        $map = [
            'parent_id' => 11,
        ];
        $classLists = D('his_dictionary')->getLevelLists($map);
        $hid = $this->hospitalInfo['uid'];
        $medicinesLists = $this->medicines_model->getMedicinesLists($hid,$search);
        if (IS_AJAX) {
            $data = [
                'classLists' => $classLists,
                'medicinesLists' => $medicinesLists,
                'search' => $search_content,
                'class_id' => $classId,
            ];
            $this->ajaxSuccess('', $data);
        } else {
            $this->display();
        }
    }

    /**
     * 药品信息添加
     * Author: doreen
     */
    public function addMedicines()
    {
        if (IS_AJAX) {
            $data = I();
            $hid = $this->hospitalInfo['uid'];
            $medicinesIdLists = $data['medicinesId'];
            $insertData = [];
            foreach ($medicinesIdLists as $key => $value) {
                $insertData[$key]['medicines_id'] = $value;
                $insertData[$key]['hospital_id'] = $hid;
                $insertData[$key]['create_time'] = time();
            }
            $res = $this->medicines_model->addMedicines($insertData);
            $res ? $this->ajaxSuccess('添加成功') : $this->ajaxError('添加失败');
        }
    }

    /**
     * 药品信息删除
     * Author: doreen
     */
    public function deleteMedicines()
    {
        if(IS_POST){
            $rid = I('post.rid','','intval');
            $res = $this->medicines_model->deleteMedicines($rid);
            $res ? $this->ajaxSuccess('删除成功') : $this->ajaxError('删除失败');
        }
    }

}