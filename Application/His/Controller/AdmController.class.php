<?php
/**
 *  信息公示
 * User: wsl
 * Date: 2017-10-24
 */
namespace His\Controller;
use Common\Controller\PublicBaseController;

class AdmController extends PublicBaseController
{

    public function _initialize()
    {
        C('TITLE',"信息公示");

    }


    /**
     * 首页
     * @Author   wsl
     * @DateTime 2017-10-24
     */
    public function data_history()
    {



        session('demo_time',time());


       # print_r($list);

        #$redis = $this->getRedis();


        #$redis->set('test','hello world!');
        #echo $redis->get("test");

        #$p_num = substr(time(),4);

        #$this->assign('p_num', $p_num);

        $this->display('index');
    }

    #返回当前数据
    public function getData()
    {


        $max_id=session('max_id');


        $demo_time = session('demo_time');

        if($demo_time>time()-2){

            $num=20;
            $max_id=0;

        }else{

            if(!$max_id)$max_id=0;
            $num=rand(1,5);
        }





        $sql="SELECT * FROM  dzm_his_demo_patient WHERE id>$max_id LIMIT $num";

        $list = M()->query($sql);

        #$sql="SELECT * FROM  dzm_his_demo_doctor WHERE id=";

        $Ls=array('男','女');


        $Lks = array(
'皮肤科',
'神经内科',
'中医科',
'耳鼻喉科',
'外科',
'呼吸科',
'内科',
'消化科',
'儿科',
'肿瘤科',
'内分泌科',
'肾脏科',
'眼科',
'心血管科',
'推拿科',
'五官科',
        );


        $t = 1000;

        foreach ($list as &$v) {
            $doctor_id = rand(1,1000);
            $sex = rand(0,1);
            $ks = rand(0,15);
            $tt = rand(10,99);
            $doctor = M('His_demo_doctor')->where('id='.$doctor_id)->find();
            $v['doctor'] = $doctor;
            $v['sex'] = $Ls[$sex];
            $v['mobile']=substr_replace($v['mobile'],'****',3,4);
            $v['ks']= $Lks[$ks];

            $t-=$tt;

            $v['addtime']=date('Y-m-d H:i:s',strtotime("-$t second"));


            $v['user_code']='E'.time().'0000'.rand(1000,9999);

            $max_id = $v['id'];
        }

        session('max_id',$max_id);

        $this->resJSON(0,'ok',array('num'=>count($list),'list'=>$list));

    }

}
?>