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
 * 字典管理类
 * DictionaryController
 * Author: doreen
 */
class DictionaryController extends HisBaseController
{
    protected $dictionary_model;

    public function _initialize()
    {
        parent::_initialize();
        $dictionary_model = D('HisDictionary');
        $this->dictionary_model = $dictionary_model;
    }

    /**
     * 字典表列表
     * Author: doreen
     */
    public function index()
    {
        //字典一二级列表
        $map = [
            'parent_id' => 0,
            'is_del' => 1,
        ];
        $firstLists = $this->dictionary_model->getLevelLists($map);
        $secondLists = [];
        foreach ($firstLists as $key => $value) {
            $condition = [
                'parent_id' => $value['did'],
                'is_del' => 1,
            ];
            $secondLists[] = $this->dictionary_model->getLevelLists($condition);
        }
        $this->assign('firstLists', $firstLists);
        $this->assign('secondLists', $secondLists);
        $this->display();
    }

    /**
     * 字典列表
     * Author: doreen
     */
    public function dictionaryLists()
    {
        $data = I();
        $hospitalId = $this->hospitalInfo['uid'];
        //字典一二级列表
        $map = [
            'parent_id' => 0,
            'is_del' => 1,
        ];
        $firstLists = $this->dictionary_model->getLevelLists($map);
        $secondLists = [];
        foreach ($firstLists as $key => $value) {
            $condition = [
                'parent_id' => $value['did'],
                'is_del' => 1,
            ];
            $secondLists[] = $this->dictionary_model->getLevelLists($condition);
        }
        //首次进入页面默认显示
        $firstMenu = $secondLists[0][0];
        $map = [
            'parent_id' => $firstMenu['did'],
            'hid' => array('in', [0,$hospitalId]),
        ];
        $dictionaryLists = $this->dictionary_model->getDictionaryLists($map);
        if (isset($data['pid']) && !empty($data['pid'])) {
            $map = [
                'parent_id' => $data['pid'],
                'hid' => array('in', [0,$hospitalId]),
            ];
            $dictionaryLists = $this->dictionary_model->getDictionaryLists($map);
        }
        $this->ajaxSuccess('', $dictionaryLists);
    }

    /**
     * 字典子列表
     * Author: doreen
     */
    public function getSubDictionary()
    {
        if (IS_AJAX) {
            $data = I();
            $hospitalId = $this->hospitalInfo['uid'];
            $map = [
                'parent_id' => $data['pid'],
                'hid' => array('in', [0,$hospitalId]),
            ];
            $order = 'create_time desc, update_time desc';
            $subDictionaryLists = $this->dictionary_model->getLevelLists($map, $order);
            return $this->ajaxSuccess('', $subDictionaryLists);
        }
    }

    /**
     * 字典添加
     * Author: doreen
     */
    public function addDictionary()
    {
        if(IS_AJAX) {
            $data = I();
            $hospitalId = $this->hospitalInfo['uid'];
            $info = $this->dictionary_model->findDictionaryById($data['parent_id']); //上级字典信息
            //添加时去重
            $condition = array(
                'parent_id' => $data['parent_id'],
                'hid' => array('in', array($hospitalId,0)),
            );
            $doctionaryList = $this->dictionary_model->getDictionary($condition);
            foreach ($doctionaryList as $key => $value) {
                if ($value['dictionary_name'] == $data['dictionary_name']) {
                    $this->ajaxError('字典名称已存在');
                }
            }
            //上级字典信息不存在返回错误信息
            if (!$info) {
                $this->ajaxError('添加失败');
            }
            //保存添加字典信息
            $data['hid'] = $hospitalId;
            $data['create_time'] = time();
            $data['type'] = 1;
            $res = $this->dictionary_model->addData($data);
            if ($res) {
                $this->ajaxSuccess('添加成功',$res);
            } elseif ($this->dictionary_model->getError()) {
                $this->ajaxError($this->dictionary_model->getError());
            } else {
                $this->ajaxError('添加失败');
            }
        }
    }

    /**
     * 字典编辑
     * Author: doreen
     */
    public function editDictionary()
    {
        if (IS_AJAX) {
            $data = I();
            $data['update_time'] = time();
            //编辑时去重
            $condition = array(
                'parent_id' => $data['parent_id'],
                'hid' => array('in', array($this->hospitalInfo['uid'],0)),
                'did' => array('NEQ', I('post.did','','intval')),
            );
            $doctionaryList = $this->dictionary_model->getDictionary($condition);
            foreach ($doctionaryList as $key => $value) {
                if ($value['dictionary_name'] == $data['dictionary_name']) {
                    $this->ajaxError('字典名称已存在');
                }
            }
            //保存编辑字典信息
            $map = [
                'did' => I('post.did','','intval'),
            ];
            $res = $this->dictionary_model->updateData($map, $data);
            if ($res) {
                $this->ajaxSuccess('修改成功');
            } elseif ($this->dictionary_model->getError()) {
                $this->ajaxError($this->dictionary_model->getError());
            } else {
                $this->ajaxError('修改失败');
            }
        }
    }

    /**
     * 删除字典
     * Author: doreen
     */
    public function deleteDictionary()
    {
        if (IS_POST) {
            $did = I('post.did','','intval');
            $hospitalId = $this->hospitalInfo['uid'];
            $map = [
                'parent_id' => $did,
                'hid' => array('in', [0,$hospitalId]),
            ];
            $dictionaryLists = $this->dictionary_model->getLevelLists($map);
            if(!$dictionaryLists) {
                if($this->dictionary_model->deleteDictionary($did)){
                    $this->ajaxSuccess("删除成功");
                }else{
                    $this->ajaxError("删除失败");
                }
            } else {
                $this->ajaxError("删除失败");
            }
        }
    }

}
?>