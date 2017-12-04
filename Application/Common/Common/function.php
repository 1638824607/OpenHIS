<?php

header("Content-type:text/html;charset=utf-8");

//传递数据以易于阅读的样式格式化后输出
function p($data){
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}

/**
 * @desc     根据生日获取年龄
 * @Author   malixiao
 * @DateTime 2017-08-17
 * @param    [type]     $birthday [Y-m-d]
 * @return   [type]               [description]
 */
function birthday($birthday){
    $age = strtotime($birthday);
    if($age === false)
    {
        return false;
    }
    list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
    $now = strtotime("now");
    list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
    $age = $y2 - $y1;
    if((int)($m2.$d2) < (int)($m1.$d1))
        $age -= 1;
    return $age;
}

function get_sex($key=''){
    $array = array(
        ''=>'-请选择-',
        1=>'男',
        2=>'女',
    );
    if(key_exists($key, $array))
        return $array[$key];
    else
        return false;
}

/**
 * description: 递归菜单
 * @author: malixiao
 * @param unknown $array
 * @param number $fid
 * @param number $level
 * @param number $type 1:顺序菜单 2树状菜单
 * @return multitype:number
 */
function get_column($array,$type=1,$fid=0,$level=0)
{
    $column = array();
    if($type == 2)
        foreach($array as $key => $vo)
        {
            if($vo['pid'] == $fid)
            {
                $vo['level'] = $level;
                $column[$key] = $vo;
                $column [$key][$vo['id']] = get_column($array,$type=2,$vo['id'],$level+1);
            }
        }
    else
    {
        foreach($array as $key => $vo)
        {
            if($vo['pid'] == $fid)
            {
                $vo['level'] = $level;
                $column[] = $vo;
                $column = array_merge($column, get_column($array,$type=1,$vo['id'],$level+1));
            }
        }
    }

    return $column;
}

/**
 * 删除指定的标签和内容
 * @param array $tags 需要删除的标签数组
 * @param string $str 数据源
 * @param string $content 是否删除标签内的内容 0保留内容 1不保留内容
 * @return string
 */
function strip_html_tags($tags,$str,$content=0){
    if($content){
        $html=array();
        foreach ($tags as $tag) {
            $html[]='/(<'.$tag.'.*?>[\s|\S]*?<\/'.$tag.'>)/';
        }
        $data=preg_replace($html,'',$str);
    }else{
        $html=array();
        foreach ($tags as $tag) {
            $html[]="/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";
        }
        $data=preg_replace($html, '', $str);
    }
    return $data;
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 * @return string
 */
function re_substr($str, $start=0, $length, $suffix=true, $charset="utf-8") {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    $omit=mb_strlen($str) >=$length ? '...' : '';
    return $suffix ? $slice.$omit : $slice;
}

// 设置验证码
function show_verify($config=''){
    if($config==''){
        $config=array(
            'codeSet'=>'1234567890',
            'fontSize'=>30,
            'useCurve'=>false,
            'imageH'=>60,
            'imageW'=>240,
            'length'=>4,
            'fontttf'=>'4.ttf',
        );
    }
    $verify=new \Think\Verify($config);
    return $verify->entry();
}

// 检测验证码
function check_verify($code){
    $verify=new \Think\Verify();
    return $verify->check($code);
}

/**
 * 实例化page类
 * @param  integer  $count 总数
 * @param  integer  $limit 每页数量
 * @return subject       page类
 */
function new_page($count,$limit=10,$isNew=0,$isPagerString=0){
    if ($isNew == 0) return new \Org\Nx\Page($count,$limit);
    $page=I('p',1);
    $pagesize=I('pagesize',$limit);
    $pager = new \Org\Nx\Page($count,$pagesize);
    $pager->setPage($page);
    return $isPagerString ? $pager->showHis() : $pager;
}

/**
 * 获取分页数据
 * @param  subject  $model  model对象
 * @param  array    $map    where条件
 * @param  string   $order  排序规则
 * @param  integer  $limit  每页数量
 * @return array            分页数据
 */
function get_page_data($model,$map,$order='',$limit=10){
    $count=$model
        ->where($map)
        ->count();
    $page=new_page($count,$limit);
    // 获取分页数据
    $list=$model
        ->where($map)
        ->order($order)
        ->limit($page->firstRow.','.$page->listRows)
        ->select();
    $data=array(
        'data'=>$list,
        'page'=>$page->show()
    );
    return $data;
}

/**
 * 处理post上传的文件；并返回路径
 * @param  string $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string $format  文件格式限制
 * @param  string $maxSize 允许的上传文件最大值 52428800
 * @return array           返回ajax的json格式数据
 */
function post_upload($path='file', $format='empty', $maxSize='52428800', $autosub=true, $rename=false){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    if (!is_dir($path)){
        //生成文件夹
        mkdir($path, 0777, true);
    }
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
    );
    if(!empty($_FILES)){

        if( $rename === true )
        {
            foreach ($_FILES as $key => $value)
            {
                $_FILES[$key]['name'] = $value['name'].'.jpg';
            }
        }

        // 上传文件配置
        $config=array(
            'maxSize'   =>  $maxSize,       //   上传文件最大为50M
            'rootPath'  =>  './',           //文件上传保存的根路径
            'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
            'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
            'autoSub'   =>  $autosub,                   //  自动使用子目录保存上传文件 默认为true
            'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
        );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info)
        {
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error.','.$path;
            return $data;
        }
        else
        {
            return $info;
        }
    }
}


/**
 * 将路径转换加密
 * @param  string $file_path 路径
 * @return string            转换后的路径
 */
function path_encode($file_path){
    return rawurlencode(base64_encode($file_path));
}

/**
 * 将路径解密
 * @param  string $file_path 加密后的字符串
 * @return string            解密后的路径
 */
function path_decode($file_path){
    return base64_decode(rawurldecode($file_path));
}


/**
 * 传入时间戳,计算距离现在的时间
 * @param  number $time 时间戳
 * @return string     返回多少以前
 */
function word_time($time) {
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2){
        $str = sprintf('刚刚', $int);
    }elseif ($int < 60){
        $str = sprintf('%d秒前', $int);
    }elseif ($int < 3600){
        $str = sprintf('%d分钟前', floor($int / 60));
    }elseif ($int < 86400){
        $str = sprintf('%d小时前', floor($int / 3600));
    }elseif ($int < 1728000){
        $str = sprintf('%d天前', floor($int / 86400));
    }else{
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}

/**
 * 生成缩略图
 * @param  string  $image_path 原图path
 * @param  integer $width      缩略图的宽
 * @param  integer $height     缩略图的高
 * @return string             缩略图path
 */
function crop_image($image_path,$width=170,$height=170){
    $image_path=trim($image_path,'.');
    $min_path='.'.str_replace('.', '_'.$width.'_'.$height.'.', $image_path);
    $image = new \Think\Image();
    $image->open($image_path);
    // 生成一个居中裁剪为$width*$height的缩略图并保存
    $image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($min_path);
    oss_upload($min_path);
    return $min_path;
}

/**
 * 上传文件类型控制 此方法仅限ajax上传使用
 * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string   $format  文件格式限制
 * @param  integer  $maxSize 允许的上传文件最大值 52428800
 * @return booler   返回ajax的json格式数据
 */
function ajax_upload($path='file',$format='empty',$maxSize='52428800'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf'),
        'upload_file'=>array('doc', 'docx', 'xls', 'xlsx','ppt','txt','pdf')
    );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
            'maxSize'   =>  $maxSize,               // 上传文件最大为50M
            'rootPath'  =>  './',                   // 文件上传保存的根路径
            'savePath'  =>  './'.$path.'/',         // 文件上传的保存路径（相对于根路径）
            'saveName'  =>  array('uniqid',''),     // 上传文件的保存规则，支持数组和字符串方式定义
            'autoSub'   =>  true,                   // 自动使用子目录保存上传文件 默认为true
            'exts'      =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
        );
        // p($_FILES);
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        // p($info);
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            echo json_encode($data);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
                // p($data);
                echo json_encode($data);
            }
        }
    }
}

/**
 * 检测是否是手机访问
 */
function is_mobile(){
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
    function _is_mobile($substrs,$text){
        foreach($substrs as $substr)
            if(false!==strpos($text,$substr)){
                return true;
            }
        return false;
    }
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');

    $found_mobile=_is_mobile($mobile_os_list,$useragent_commentsblock) ||
        _is_mobile($mobile_token_list,$useragent);
    if ($found_mobile){
        return true;
    }else{
        return false;
    }
}


/**
 * 获取当前访问的设备类型
 * @return integer 1：其他  2：iOS  3：Android
 */
function get_device_type(){
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 1;
    //分别进行判断
    if(strpos($agent, 'iphone')!==false || strpos($agent, 'ipad')!==false){
        $type = 2;
    }
    if(strpos($agent, 'android')!==false){
        $type = 3;
    }
    return $type;
}

/**
 * 生成pdf
 * @param  string $html      需要生成的内容
 */
function pdf($html='<h1 style="color:red">hello word</h1>'){
    vendor('Tcpdf.tcpdf');
    $pdf = new \Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // 设置打印模式
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nicola Asuni');
    $pdf->SetTitle('TCPDF Example 001');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    // 是否显示页眉
    $pdf->setPrintHeader(false);
    // 设置页眉显示的内容
    $pdf->SetHeaderData('logo.png', 60, 'baijunyao.com', '白俊遥博客', array(0,64,255), array(0,64,128));
    // 设置页眉字体
    $pdf->setHeaderFont(Array('dejavusans', '', '12'));
    // 页眉距离顶部的距离
    $pdf->SetHeaderMargin('5');
    // 是否显示页脚
    $pdf->setPrintFooter(true);
    // 设置页脚显示的内容
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    // 设置页脚的字体
    $pdf->setFooterFont(Array('dejavusans', '', '10'));
    // 设置页脚距离底部的距离
    $pdf->SetFooterMargin('10');
    // 设置默认等宽字体
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // 设置行高
    $pdf->setCellHeightRatio(1);
    // 设置左、上、右的间距
    $pdf->SetMargins('10', '10', '10');
    // 设置是否自动分页  距离底部多少距离时分页
    $pdf->SetAutoPageBreak(TRUE, '15');
    // 设置图像比例因子
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->setFontSubsetting(true);
    $pdf->AddPage();
    // 设置字体
    $pdf->SetFont('stsongstdlight', '', 14, '', true);
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_001.pdf', 'I');
}

/**
 * 生成二维码
 * @param  string  $url  url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url,$size=4){
    Vendor('Phpqrcode.phpqrcode');
    QRcode::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
}

/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 *      示例数据：
$data = array(
array(NULL, 2010, 2011, 2012),
array('Q1',   12,   15,   21),
array('Q2',   56,   73,   86),
array('Q3',   52,   61,   69),
array('Q4',   30,   32,    0),
);
 */
function create_xls($data,$filename='simple.xls'){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Dzm")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle('dzm');
    $phpexcel->setActiveSheetIndex(0);
    $phpexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);//设置默认行高
    $phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth('20');//设置列宽
    $phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth('20');//设置列宽
    $phpexcel->getActiveSheet()->getColumnDimension('C')->setWidth('20');//设置列宽
    $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth('20');//设置列宽
    $phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth('20');//设置列宽
    $phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth('20');//设置列宽
    $phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth('20');//设置列宽

    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
   // $phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(true);
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

/**
 * 数据转csv格式的excle
 * @param  array $data      需要转的数组
 * @param  string $header   要生成的excel表头
 * @param  string $filename 生成的excel文件名
 *      示例数组：
$data = array(
'1,2,3,4,5',
'6,7,8,9,0',
'1,3,5,6,7'
);
$header='用户名,密码,头像,性别,手机号';
 */
function create_csv($data,$header=null,$filename='simple.csv'){
    // 如果手动设置表头；则放在第一行
    if (!is_null($header)) {
        array_unshift($data, $header);
    }
    // 防止没有添加文件后缀
    $filename=str_replace('.csv', '', $filename).'.csv';
    ob_clean();
    Header( "Content-type:  application/octet-stream ");
    Header( "Accept-Ranges:  bytes ");
    Header( "Content-Disposition:  attachment;  filename=".$filename);
    foreach( $data as $k => $v){
        // 如果是二维数组；转成一维
        if (is_array($v)) {
            $v=implode(',', $v);
        }
        // 替换掉换行
        $v=preg_replace('/\s*/', '', $v);
        // 解决导出的数字会显示成科学计数法的问题
        $v=str_replace(',', "\t,", $v);
        // 转成gbk以兼容office乱码的问题
        echo iconv('UTF-8','GBK',$v)."\t\r\n";
    }
}

/**
 * 导入excel文件
 * @param  string $file excel文件路径
 * @return array        excel文件内容数组
 */
function import_excel($file){
    // 判断文件是什么格式
    $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    if ($type=='xlsx') {
        $type='Excel2007';
    }elseif($type=='xls') {
        $type = 'Excel5';
    }
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数 
    $highestRow = $sheet->getHighestRow();
    // 取得总列数      
    $highestColumn = $sheet->getHighestColumn();
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据
    for($j=1;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k='A';$k<=$highestColumn;$k++){
            // 读取单元格
            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        }
    }
    return $data;
}

//自定义函数
function key_map_kou($key=""){
    $array=array(
        1 => '辛辣',
        2 => '油腻',
        3 => '生冷',
        4 => '烟酒',
        5 => '发物',
        6 => '荤腥',
        7 => '酸涩',
        8 =>'刺激性食物',
        9 =>'难消化食物',
    );
    if($key == ''){
        return $array;
    }
    elseif(key_exists($key, $array)){
        return $array[$key];
    }
    else{
        return false;
    }

}


function key_map_jin($key=""){
    $array=array(

        1=>'备孕禁服',
        2=>'怀孕禁服',
        3=>'经期停服',
        4=>'感冒停服',
        5=>'忌与西药同服'
    );
    if($key == ''){
        return $array;
    }
    elseif(key_exists($key, $array)){
        return $array[$key];
    }
    else{
        return false;
    }

}

/**
 * 正则验证手机号
 * @DateTime 2017-08-14
 * @param    [type]     $mobile [description]
 * @return   [type]             [description]
 */
function reg_mobile($mobile){
    if(!preg_match("/^1[34578]{1}\d{9}$/", $mobile))
    {
        return false;
    }
    return ture;
}

function sendMeassage($phone,$content){//发送短信验证码

    require './ThinkPHP/Library/Vendor/send/sendsms.php';
    //Vendor('send.sendsms');
    if($response['RetCode']==0){
        return true;
    }else{
        return false;
    }
}
/**
 * @Author   明强
 * @DateTime 2017-11-17
 * 阿里云发送短信验证码
 * 成功返回{'success':true}
 * 失败返回{'success':false,'message':'"The specified templateCode is wrongly formed'}
 * @param    [type]     $mobile [description]
 * @param    [type]     $code   [description]
 * @return   [type]             [description]
 */
function aliyunSendCode($mobile,$code) {
    $app_key = C('ALIYUN_MEASSAGE_APPKEY');
    $app_secret = C('ALIYUN_MEASSAGE_SECRET');
    $request_host = "http://sms.market.alicloudapi.com";
    $request_uri = "/singleSendSms";
    $request_method = "GET";
    $request_paras = array(
        'ParamString' => "{'code':'{$code}'}",
        'RecNum' => $mobile,
        'SignName' =>C('ALIYUN_SIGNAME'),
        'TemplateCode' => C('ALIYUN_TEMPLATE_CODE')
    );
    ksort($request_paras);
    $request_header_accept = "application/json;charset=utf-8";
    $content_type = "";
    $headers = array(
        'X-Ca-Key' => $app_key,
        'Accept' => $request_header_accept
    );
    ksort($headers);
    $header_str = "";
    $header_ignore_list = array('X-CA-SIGNATURE', 'X-CA-SIGNATURE-HEADERS', 'ACCEPT', 'CONTENT-MD5', 'CONTENT-TYPE', 'DATE');
    $sig_header = array();
    foreach($headers as $k => $v) {
        if(in_array(strtoupper($k), $header_ignore_list)) {
            continue;
        }
        $header_str .= $k . ':' . $v . "\n";
        array_push($sig_header, $k);
    }
    $url_str = $request_uri;
    $para_array = array();
    foreach($request_paras as $k => $v) {
        array_push($para_array, $k .'='. $v);
    }
    if(!empty($para_array)) {
        $url_str .= '?' . join('&', $para_array);
    }
    $content_md5 = "";
    $date = "";
    $sign_str = "";
    $sign_str .= $request_method ."\n";
    $sign_str .= $request_header_accept."\n";
    $sign_str .= $content_md5."\n";
    $sign_str .= "\n";
    $sign_str .= $date."\n";
    $sign_str .= $header_str;
    $sign_str .= $url_str;

    $sign = base64_encode(hash_hmac('sha256', $sign_str, $app_secret, true));
    $headers['X-Ca-Signature'] = $sign;
    $headers['X-Ca-Signature-Headers'] = join(',', $sig_header);
    $request_header = array();
    foreach($headers as $k => $v) {
        array_push($request_header, $k .': ' . $v);
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $request_host . $url_str);
    //curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ret = json_decode(curl_exec($ch),true);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return $ret;
}
/**
 * 邮件发送
 * @param $receiver    接收人
 * @param string $subject   邮件标题
 * @param string $content   邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function sendMail($receiver,$subject,$content){
    $mail = new \Org\Nx\MySendMail();
    $mail->setServer(C('MAIL_SERVER'), C('MAIL_USER_NAME'), C('MAIL_PASSWORD'), C('MAIL_PORT'), true); //设置smtp服务器，到服务器的SSL连接
    $mail->setFrom("server@dzmtech.com"); //设置发件人
    $mail->setReceiver($receiver); //设置收件人，多个收件人，调用多次
    $mail->setMail($subject, $content); //设置邮件主题、内容
    $mail->sendMail(); //发送

}

/**
 * 密码加密
 * BCRYPT的默认成本增加到12
 * @Author   malixiao
 * @DateTime 2017-08-21
 * @param    string     $value [description]
 */
function encrypt_password($password=''){
    $options = array(
        'cost' => 12,
        'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
    );
    return password_hash($password, PASSWORD_BCRYPT, $options); //使用BCRYPT算法加密密码
}

/**
 * 密码解密
 * 验证密码是否与散列匹配
 * @Author   malixiao
 * @DateTime 2017-08-21
 * @return   [type]     [description]
 */
function decrypt_password($password='', $hash=''){
    if( password_verify($password , $hash) )
    {
        return true;
    }
    return false;
}

/**
 * 获取显示图片地址
 * @Author   malixiao
 * @DateTime 2017-08-29
 * @param    string     $filename [id+文件名]
 * @param    string     $sign     [图片表示]
 * @return   [url]
 */
function getPicPath($id='',$filename='',$sign='doctor')
{
    if( empty($filename) || empty($id))
    {
        return false;
    }
    switch($sign)
    {
        case 'doctor':
            $default_img = C('UPLOAD_DOCTOR').$id.'/'.$filename;
            break;
        case 'patient':
            $default_img = C('UPLOAD_PATIENT').$id.'/'.$filename;
            break;
        default:
            break;
    }
    return $default_img;
}

/**
 * 判断是否是微信内置浏览器
 * @Author   malixiao
 * @DateTime 2017-08-30
 * @return   boolean    [description]
 */
function is_weixin()
{
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}

/**
 * 数组分页page
 * @param  array    $array 要分页的数组
 * @param  integer  $rows 每页数量
 * @return array
 */
function array_page($array, $rows){
    $count = count($array);
    $Page = new \Org\Nx\Page($count, $rows);
    $list = array_slice($array,$Page->firstRow,$Page->listRows);
    return $list;
}

/**
 * 生成带logo的二维码
 * @Author   高明强
 * @DateTime 2017-08-30
 * @return
 */
function qrcodeLogo($url,$size,$gid){
    Vendor('Phpqrcode.phpqrcode');
    $value = $url; //二维码内容
    $errorCorrectionLevel = 'L';//容错级别
    $matrixPointSize = $size;//生成图片大小
    QRcode::png($value, 'Upload/groupCode/'.$gid.'.png', $errorCorrectionLevel, $matrixPointSize, 2);
    $logo = 'Upload/groupCode/logo.jpg';//准备好的logo图片
    $QR = 'Upload/groupCode/'.$gid.'.png';//已经生成的原始二维码图
    if ($logo !== FALSE) {
        $QR = imagecreatefromstring(file_get_contents($QR));
        $logo = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);//二维码图片宽度
        $QR_height = imagesy($QR);//二维码图片高度
        $logo_width = imagesx($logo);//logo图片宽度
        $logo_height = imagesy($logo);//logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
            $logo_qr_height, $logo_width, $logo_height);
        $code_path = 'Upload/groupCode/'.$gid.'qrlogin.png';
        imagepng($QR,$code_path);
       // echo "<img src=$code_path>";
        return $code_path;
    }

}
/**
 * 微信机器人获取消息解码
 * @Author   高明强
 * @DateTime 2017-08-30
 * @return
 */
function unicode2utf8($str){
    $t = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $str);
    return $t;
}

/**
 * 网页授权获取用户信息
 * @Author   高明强
 * @DateTime 2017-08-30
 * @return
 */
function oauth_userinfo() {
    #wsl disabled 2017.10.31
    return false;

    $code  =  isset($_GET['code']) ? $_GET['code'] : ""; //code 微信接口参数(必须)
    $state =  isset($_GET['state']) ? $_GET['state'] : ""; //state微信接口参数(不需传参则不用)；若传参可考虑规则： 'act'.'arg1'.'add'.'arg2'

    $APPID = _APPID_;
    $SECRET = _APPSECRET_;
    $REDIRECT_URL = _URL_.__ACTION__."?".$_SERVER['QUERY_STRING']; //当前页面地址

    $oauth2 = new \Org\Nx\oauth2();
    $oauth2->init($APPID, $SECRET, $REDIRECT_URL);
    if (empty($code)) {
        $oauth2->get_code_by_authorize($state); //获取code，会重定向到当前页。若需传参，使用$state变量传参。
        return false;
    }else{
        $data = $oauth2->get_userinfo_by_authorize();
        return $data;
    }



}
/**
 * 网页授权获取用户openid
 * @Author   高明强
 * @DateTime 2017-08-30
 * @return
 */
function oauth_openid(){
    $code = isset($_GET['code']) ? $_GET['code'] : ""; //code 微信接口参数(必须)
    $state = isset($_GET['state']) ? $_GET['state'] : ""; //state微信接口参数(不需传参则不用)；若传参可考虑规则： 'act'.'arg1'.'add'.'arg2'
    $APPID = _APPID_;
    $SECRET = _APPSECRET_;
    $REDIRECT_URL = _URL_.__ACTION__."?".$_SERVER['QUERY_STRING']; //当前页面地址
    $oauth2 = new \Org\Nx\oauth2();
    $oauth2->init($APPID, $SECRET,$REDIRECT_URL);
    if(empty($code)){
        $oauth2->get_code($state);//获取code，会重定向到当前页。若需传参，使用$state变量传参。
        return false;
    }else{
        $openid=$oauth2->get_openid();//获取openid
        return $openid;
    }


}
/**
 * 获取用户权限
 * @Author   高明强
 * @DateTime 2017-08-30
 * @return
 */
function wx_mem_priv($mid=""){
    if ($mid = 0){//0 普通用户
        return array();
    }elseif($mid = 1){//1管理员
        return array();
    }else{//2 群管理员 
        return array();
    }
}
/**
 * 把cookie加密
 * @Author   高明强
 * @DateTime 2017-08-30
 * @return
 */
function set_cookie_encrypt($userInfo,$data){
    $dstr = json_encode($data);
    $s = \Think\Crypt\Driver\Base64::encrypt($dstr);
    cookie($userInfo,$s);
}
/**
把用户输入的文本转义（主要针对特殊符号和emoji表情）
入库用
 */
/**
 * 转义特殊符号和emoji表情（使用userTextEncode进行解码）
 * @Author   左传业
 * @DateTime 2017-09-30
 * @param   string  $str  需要转义的字符串
 * @return string
 */
function userTextEncode($str){
    if(!is_string($str))return $str;
    if(!$str || $str=='undefined')return '';

    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
        return addslashes($str[0]);
    },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    return json_decode($text);
}


/**
 * 解码特殊符号和emoji表情（配合userTextDecode方法使用）
 * @Author   左传业
 * @DateTime 2017-09-30
 * @param   string  $str  需要解码的字符串
 * @return string
 */
function userTextDecode($str){
    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback('/\\\\\\\\/i',function($str){
        return '\\';
    },$text); //将两条斜杠变成一条，其他不动
    return json_decode($text);
}
/**
 * 把cookie解密
 * @Author   高明强
 * @DateTime 2017-08-30
 */
function get_cookie_decript($str){
    $s = \Think\Crypt\Driver\Base64::decrypt($str);
    $data = json_decode($s,true);
    return $data;
}

/**
 * 判断是否存在手机端表情
 * @Author   malixiao
 * @DateTime 2017-09-30
 */
function emoji_is_emoji($str='')
{
    Vendor('Emoji.emoji');
    if(emoji_contains_emoji($str))
    {
        return true;
    }
    return false;
}
/**
 * 将字节根据情况转化为相对应单位
 * @Author   zuochuanye
 * @DateTime 2017-10-12
 */
function getFilesize($num){
    $p = 0;
    $format='b';
    if($num>0 && $num<1024){
        $p = 0;
        return number_format($num).' '.$format;
    }
    if($num>=1024 && $num<pow(1024, 2)){
        $p = 1;
        $format = 'KB';
    }
    if ($num>=pow(1024, 2) && $num<pow(1024, 3)) {
        $p = 2;
        $format = 'MB';
    }
    if ($num>=pow(1024, 3) && $num<pow(1024, 4)) {
        $p = 3;
        $format = 'GB';
    }
    if ($num>=pow(1024, 4) && $num<pow(1024, 5)) {
        $p = 3;
        $format = 'TB';
    }
    $num /= pow(1024, $p);
    return number_format($num, 1).''.$format;
}
/**
 * 下载文件 （原\Org\Net\Http()自有函数）
 * 可以指定下载显示的文件名，并自动发送相应的Header信息
 * 如果指定了content参数，则下载该参数的内容
 * @static
 * @access public
 * @param string $filename 下载文件名
 * @param string $showname 下载显示的文件名
 * @param string $content  下载的内容
 * @param integer $expire  下载内容浏览器缓存时间
 * @return void
 */
function download ($filename, $showname='',$content='',$expire=180) {
    if(is_file($filename)) {
        $length = filesize($filename);
    }elseif(is_file(UPLOAD_PATH.$filename)) {
        $filename = UPLOAD_PATH.$filename;
        $length = filesize($filename);
    }elseif($content != '') {
        $length = strlen($content);
    }else {
        return false;
    }
    if(empty($showname)) {
        $showname = $filename;
    }
    $showname = get_basename($showname);
    if(!empty($filename)) {
        $finfo 	= 	new \finfo(FILEINFO_MIME);
        $type 	= 	$finfo->file($filename);
    }else{
        $type	=	"application/octet-stream";
    }
    //发送Http Header信息 开始下载
    header("Pragma: public");
    header("Cache-control: max-age=".$expire);
    //header('Cache-Control: no-store, no-cache, must-revalidate');
    header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
    header("Content-Disposition: attachment; filename=".$showname);
    header("Content-Length: ".$length);
    header("Content-type: ".$type);
    header('Content-Encoding: none');
    header("Content-Transfer-Encoding: binary" );
    if($content == '' ) {
        readfile($filename);
    }else {
        echo($content);
    }
    exit();
}
/**
 * 解决basename删除汉字
 * @Author   zuochuanye
 * @DateTime 2017-10-12
 */
function get_basename($filename){
    return preg_replace('/^.+[\\\\\\/]/', '', $filename);
}
/**
 * 根据生日获取年龄精确到天
 * @Author   dingxiaoxin
 * @DateTime 2017-10-27
 */
function getAge($date1,$date2){
    $datestart= date('Y-m-d',strtotime($date1));
    if(strtotime($datestart)>strtotime($date2)){
        $tmp=$date2;
        $date2=$datestart;
        $datestart=$tmp;
    }
    list($Y1,$m1,$d1)=explode('-',$datestart);
    list($Y2,$m2,$d2)=explode('-',$date2);
    $Y=$Y2-$Y1;
    $m=$m2-$m1;
    $d=$d2-$d1;
    if($d<0){
        $d+=(int)date('t',strtotime("-1 month $date2"));
        $m--;
    }
    if($m<0){
        $m+=12;
        $y--;
    }
    if($Y == 0){
        return $m.'月'.$d.'天';
    }elseif($Y == 0 && $m == 0){
        return $d.'天';
    }else{
        return $Y.'岁'.$m.'月'.$d.'天';
    }
}
/**
 * 计算月份
 * @author    dingxiaoxin
 * @Date      2017-11-13
 */
function getMonth($startTime, $endTime)
{
    $monArr = array();
    while( $startTime <= $endTime ){
        $monArr[] = date('Y/m',$startTime); // 取得递增月;
        $startTime = strtotime('+1 month', strtotime(date('Y-m-01', $startTime)));
    }
    return $monArr;
}

/**
 * gmq
 * 生成唯一邀请码
 * @return string
 * 2017.11.9
 */
function make_coupon_card() {
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0,25)]
        .strtoupper(dechex(date('m')))
        .date('d').substr(time(),-5)
        .substr(microtime(),2,5)
        .sprintf('%02d',rand(0,99));
    for(
        $a = md5( $rand, true ),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
        $d = '',
        $f = 0;
        $f < 8;
        $g = ord( $a[ $f ] ),
        $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
        $f++
    );
    return $d;
}

/**
 * 获得推广人员的信息
 * gmq
 * @param string $key
 * @return array|bool|mixed
 * 2017.11.9
 */
function getSell($key=""){
    $array=['3828'=>'寇学伟'
            ,'5696'=>'刘向文'
            ,'9537'=>'乌凯
          ','0948'=>'朱明科
          ','5605'=>'孙碧瑶'
            ,'3481'=>'姚涛'
    ];
    if($key == ''){
        return $array;
    }
    elseif(key_exists($key, $array)){
        return $array[$key];
    }
    else{
        return false;
    }

}