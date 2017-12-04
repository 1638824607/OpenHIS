<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: zcy
// +----------------------------------------------------------------------
namespace His\Controller;


class DrugSalesStatisticsController extends HisBaseController
{
    protected $company_id;//公司ID

    protected $_inventory;
    public function __construct()
    {
        C('TITLE', "药品销售统计");
        C('KEYEORDS', "");
        C('DESCRIPTION', "");
        parent::__construct();
        $this->company_id = $this->hospitalInfo['uid'];

        $this->_inventory = D('HisInventory');
    }

    /**
     * @Name     index
     * @explain  药品销售统计列表
     * @author   zuochuanye
     * @Date     2017/11/10
     */
    public function index(){
        //取出药品分类子项
        $map = ['parent_id' => 11];
        $classLists = D('his_dictionary')->getLevelLists($map);
        $this->assign("classLists",$classLists);
        $this->display();
    }

    /**
     * @Name     detailList
     * @explain  药品对应信息
     * @author   zuochuanye
     * @Date     2017/11/27
     */
    public function detailList(){
        if(IS_AJAX){
            $where=[
                'search_class'  =>  I('post.search_class','') ? D('his_dictionary')->findDictionaryById(I('post.search_class'))["dictionary_name"] : '',
                'startTime'     => !empty(I('post.startTime')) ? strtotime(I('post.startTime')) :'',
                'endTime'       =>  !empty(I('post.endTime')) ? strtotime(I('post.endTime').'23:59:59'):''
            ];
            $info = self::getListInfo($where);

            $info['msg']['status'] = 1 ? $this->ajaxSuccess($info['msg']['message'],$info) : $this->ajaxError($info['msg']['message']);
        }
    }

    /**
     * @param $where
     * @Name     getListInfo
     * @explain  获取药品统计所需信息
     * @author   zuochuanye
     * @Date     2017/11/27
     * @return mixed
     */
    private function getListInfo($where){
        $info['msg']['status'] = 1;
        $info['msg']['message'] = '成功';
        if(!empty($where['search_class'])){
            $condition['me.medicines_class'] = array('like','%'.$where['search_class'].'%');
        }
        if(!empty($where['startTime']) || !empty($where['endTime'])){
            if($where['startTime'] && $where['endTime']){
                $condition['o.addtime'] = array(array('gt', $where['startTime']), array('lt', $where['endTime']));
            }else{
                $info['msg']['status'] = 0;
                $info['msg']['message'] = '开始时间和结束时间都要存在';
            }
        }
        //收支概况
        $info['all_total_info'] = $this->_inventory->drugSalesStatistics($condition);
        //列表信息
        $info['total_info_list'] = $this->_inventory->drugSalesStatistics($condition,1);
        if(empty($info['all_total_info']) || empty($info['total_info_list'])){
            $info['msg']['status'] = 0;
            $info['msg']['message'] = '失败';
        }
        return $info;
    }
}