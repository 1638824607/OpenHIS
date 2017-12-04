<?php
//发送短信文件

include_once dirname(__FILE__)."/config.simple.php";//打开发短信
include_once dirname(__FILE__)."/sdk.php";

$data['phone'] = $phone;

$conn = new UcloudApiClient(BASE_URL,PUBLIC_KEY, PRIVATE_KEY, PROJECT_ID);
$params['Action'] = "SendSms";

//$res['yanzheng_code'] = rand(1000,9999);
//$res['time_out'] = time();

// $_SESSION['yanzheng_code'] =$res['yanzheng_code'];

//$_SESSION['yanzheng_code'] = $res['yanzheng_code'];
//$_SESSION['yanzheng_code'] = 1234;
//$_SESSION['time_out'] = $res['time_out'];
// var_dump($_SESSION);


// var_dump($_SESSION['yanzheng_code']);ex

//$_SESSION['tel'] = $data['message'];

$params["Content"] = $content;//短信内容


$phones = explode("|", $data['phone']);//电话号
foreach($phones as $key => $val){
    $params["Phone.".$key] = $val;
}

$response = $conn->get("/", $params);

//die(json_encode($response));


