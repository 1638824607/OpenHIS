<?php
namespace Common\Controller;

class CronsController extends PublicBaseController
{
    
   /**
    * @Author   malixiao
    * @DateTime 2017-08-16
    * @return   [type]     [description]
    */
    public function  writeCache()
    {
        $Region = M('Region');
        $province_data = $Region->field('region_id, parent_id, region_name')->select(); 
        // $data = $this->channelLevel($province_data, $pid = 1, $html = "&nbsp;", $fieldPri = 'region_id', $fieldPid = 'parent_id', $level = 1);
       
        // $path = './Public/home/js/paddress_data.js';
        // $res = \Think\Storage::put($path, 'var jsonData = '.var_export(json_encode($data), true).';'."\r\n".'var jsonStr=jsonData,areaData=eval("("+jsonStr+")"),lareadata=formatArea(areaData);function formatArea(a){var c=[],b;for(b in a){var d=formatProvincial(a[b].child);c.push({id:a[b].id+"",name:a[b].name+"",child:d})}return c}function formatProvincial(a){var c=[],b;for(b in a){var d=formatCity(a[b].child);c.push({id:a[b].id+"",name:a[b].name+"",child:d})}return c}function formatCity(a){var c=[],b;for(b in a)c.push({id:a[b].id+"",name:a[b].name+""});return c};', 'F');
        // if($res)
        // {
        //     echo '写入成功';
        // }
        // else
        // {
        //     echo '写入失败';
        // }
    }
    /**
     * @Name     cancel_all_registration
     * @explain  将所有时间过期，并且没有为未就诊的作废
     * @author   zuochuanye
     * @Date     2017/11/24
     */
    public function cancel_all_registration(){
        $this->_registration = D('his_registration');
        $this->_registration->registration_cancel();
    }

   /**
    * 按级别处理数组
    * @Author   malixiao
    * @DateTime 2017-08-16
    * @param    [type]     $data     [description]
    * @param    integer    $pid      [description]
    * @param    string     $html     [description]
    * @param    string     $fieldPri [description]
    * @param    string     $fieldPid [description]
    * @param    integer    $level    [description]
    * @return   [type]               [description]
    */
    private function channelLevel($data, $pid = 0, $html = "&nbsp;", $fieldPri = 'cid', $fieldPid = 'pid', $level = 1)
    {
        if (empty($data)) {
            return array();
        }
        $arr = array();
        foreach ($data as $key=>$v) {
            if ($v[$fieldPid] == $pid) {
                $newarr = array('id'=>$v['region_id'],'name'=>$v['region_name']);
                $arr[$key] = $newarr;
                if($this->channelLevel($data, $v[$fieldPri], $html, $fieldPri, $fieldPid, $level + 1)){
                  $arr[$key]["child"] = $this->channelLevel($data, $v[$fieldPri], $html, $fieldPri, $fieldPid, $level + 1);
                }
            }
        }
        return $arr;
    }

}