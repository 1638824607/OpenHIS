<?php

/*
 * 微信公众号相关操作类 
 * 
 */

namespace Org\Nx;

class Wechat {

    public $appid;
    public $appsecret;
    public $type;
    public $access_token;
    public $expires_time;
    
    function __construct($param){
		//$this->token=$param['token'];
		$this->appid=$param['appID'];
		$this->appsecret=$param['appsecret'];
		$this->type = $param['type'];
		/*access_token memcache保存*/
		if($this->type == 'user'){
			$access_token_path = 'access_token';
		}elseif($this->type == 'dealer'){
			$access_token_path = 'access_token';//上线替换access_token_dealer
		}
		$res =  Yii::app()->cache->get($access_token_path);
        if(!empty($res)){
			$result = json_decode($res, true);
			if($result["access_token"])
			{
				$this->access_token = $result["access_token"];
				$this->expires_time = $result["expires_time"];
			}
		}
		else
		{
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appsecret";
			$res =$this->mycurl($url);
			$result = json_decode($res, true);
			if($result['access_token'])
			{
				$expires_time = time() + $result['expires_in'] - 200;
				$cache_expire = $result['expires_in'] - 200; //缓存时间比微信的access_token有限期短
				$cache_key = $access_token_path;
			  	$cache_data = '{"access_token": "'.$result['access_token'].'", "expires_time": '.$expires_time.'}';
			   	$ret = Yii::app()->cache->set($cache_key, $cache_data, $cache_expire);
			   	if($ret)
			   	{
			   		$this->access_token =  $result["access_token"];
            		$this->expires_time = $expires_time;
			   	}
			}
			else{
				//TODO 获取token失败处理
				Yii::log('获取access_token异常 url：'.$url.'  result：'.$res." \r\n",'error','weixin');
			}
		}
	}
	
	 /********微信开发者认证**********/
    public function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
		    return true; 
		}else{
			return false;
		}
	}
	/*模板信息发送*/
	/**
	array(
			'first'=>array('value'=>urlencode('尊敬的用户，您的买车订单已提交成功！'),"color"=>"#173177"),
			'keyword1'=>array('value'=>urlencode('1.6T 2007款 起亚狮跑'),"color"=>"#173177"),
			'keyword2'=>array('value'=>urlencode('2015-6-29 13:00'),"color"=>"#173177"),
			'keyword3'=>array('value'=>urlencode('14765432154'),"color"=>"#173177"),
			'keyword4'=>array('value'=>urlencode('一汽大众白石桥4S店'),"color"=>"#173177"),
			'remark'=>array('value'=>urlencode('请到店点击本消息打开预约详情页面，并展示二维码或条形码进行验证。'),"color"=>"#173177")
			)
	*/
	public function sendTextT($openid,$url_back,$data){
		$ACCESS_TOKEN=$this->access_token;
		$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$ACCESS_TOKEN}";
		if($this->type=='dealer'){
			$template_id = WEIXIN_DEALER_TEMPLATE_1;
		}else{
			$template_id = WEIXIN_TEMPLATE_1;
		}
		
		$send = array(
		'touser'=>$openid,
		'template_id'=>$template_id,
		'url'=>$url_back,
		'data'=>$data
		);
		$r=$this->mycurl($url,urldecode(json_encode($send)));
		
		//记录消息发送失败LOG日志
		if($r['errcode'] != 0){
			Yii::log("发送失败消息LOG access_token：$ACCESS_TOKEN	post_data：".json_encode($send)."	result：$r \r\n", 'warning', 'weixin');
		}
		
		return $r;
	}

   /**获取收到的信息*/
   public function getMsg(){
   		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
		if (!empty($postStr)){
            $this->postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		}
		return $this->postObj;
   }

	/*获取媒体列表*/
	public function getMedialist(){
		$ACCESS_TOKEN=$this->access_token;
		$url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$ACCESS_TOKEN}";
		$data = array(
			'type'=>'news',
			'offset'=>'0',
			'count'=>'1'
		);		
		$res	=	$this->mycurl($url,urldecode(json_encode($data)));
		$result = json_decode($res, true);
		return $result;
	}

/**发送文字**/
    public function makeText($contentStr){ 
	$fromUsername = $this->postObj->FromUserName;
	$toUsername = $this->postObj->ToUserName;
	$time = time();
	$textTpl = "<xml>
				<ToUserName><![CDATA[$fromUsername]]></ToUserName>
				<FromUserName><![CDATA[$toUsername]]></FromUserName>
				<CreateTime>$time</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>";             
		$msgType = "text";
		$resultStr = sprintf($textTpl, $msgType, $contentStr);
		echo $resultStr;
    }
/***发送图文***/

    public function makeNews($newsData=array())
    {
        $CreateTime = time();
        $FuncFlag = $this->setFlag ? 1 : 0;
		$fromUsername = $this->postObj->FromUserName;
        $toUsername = $this->postObj->ToUserName;
        $newTplHeader = "<xml>
            <ToUserName><![CDATA[$fromUsername]]></ToUserName>
            <FromUserName><![CDATA[$toUsername]]></FromUserName>
            <CreateTime>{$CreateTime}</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <ArticleCount>%s</ArticleCount><Articles>";
        $newTplItem = "<item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>";
        $newTplFoot = "</Articles>
            <FuncFlag>%s</FuncFlag>
            </xml>";
        $Content = '';
        $itemsCount = count($newsData['items']);
        $itemsCount = $itemsCount < 10 ? $itemsCount : 10;//微信公众平台图文回复的消息一次最多10条
        if ($itemsCount) {
            foreach ($newsData['items'] as $key => $item) {
                if ($key<=9) {
                    $Content .= sprintf($newTplItem,$item['title'],$item['description'],$item['picurl'],$item['url']);
                }
            }
        }
        $header = sprintf($newTplHeader,$newsData['content'],$itemsCount);
        $footer = sprintf($newTplFoot,$FuncFlag);
        echo $header . $Content . $footer;
    }



  /****************创建微信菜单***********************/
  /*
  data 格式
	$data = array(
		0=>array(
			'name'=>'优惠活动',
			'sub_button'=>array(
				0=>array(
					'type'=>'view',
					'name'=>'邯郸•值得约',
					'url'=>'http://www.5dcar.com/subject/mhandanyue/'
				),
			)
		),
		1=>array(
			'type'=>'view',
			'name'=>'我要买车',
			'url'=>'http://dealer.m.5dcar.com/34517/'
		),
	);
  */
   public function createMemu($data){
   /******urlencode使用，把中文转一下，以免 json_encode使用变码****/
	foreach($data as $k=>$one){
		if($one['sub_button']){
			foreach($one['sub_button'] as $k2=>$one1){
				$one['sub_button'][$k2]['name']=urlencode($one1['name']);
				if($one1['url']){
					$one['sub_button'][$k2]['url']=urlencode($one1['url']);
				}
			}

			$data[$k]['sub_button']=$one['sub_button'];
		}
		$data[$k]['name']=urlencode($one['name']);
		if($one['url']){
			$data[$k]['url']=urlencode($one['url']); 
		}
	}
	 /************提交**********/
	 $data=array( "button"=>$data);
     $ACCESS_TOKEN=$this->access_token;
     $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$ACCESS_TOKEN}";
	 $data=urldecode(json_encode($data));
	 $content=$this->mycurl($url,$data);
	 return $content;
   }
   
   /****************删除微信菜单***************/
    public function MemuStop(){
		$ACCESS_TOKEN=$this->access_token;
		$url="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$ACCESS_TOKEN}";
		$content=$this->mycurl($url);
		return $content;
	}
  
    /****************生成带参数的二维码***********************/
   public function qrcode($data){
     $ACCESS_TOKEN=$this->access_token;

     $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$ACCESS_TOKEN}";
	 $content=$this->mycurl($url,$data);
	 $data=json_decode($content,true);

	 $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$data['ticket'];
	 return $url;
   }
  
  
  /***********获取用户信息************/
   public function UserInfo($openid){
    $ACCESS_TOKEN=$this->access_token;
    //$openid="out9GuIuJBlBlZNLnP9TCRfAo8pk";
    $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$openid}&lang=zh_CN";
    $content=$this->mycurl($url);
	$data=json_decode($content,true);
	return $data;
   }
   /**************获取用户信息列表***************/
  public function UserInfoList(){
	  $ACCESS_TOKEN=$this->access_token;
	  $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$ACCESS_TOKEN;
	  $content = $this->mycurl($url);
	  $data=json_decode($content,true);
	  return $data;
  }
  
  /**********授权登录后得到Code,用code获取网页授权access_token*********/
  public function OAuthAccessToken($code){
	$appid = $this->appid;
	$appsecret = $this->appsecret;
	$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
	$content=$this->mycurl($url);
	$data=json_decode($content,true);
	return $data;
  }

  
  /***************发送客服信息******************/
  public function sendText($openid,$text){
    $ACCESS_TOKEN=$this->access_token;
    $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$ACCESS_TOKEN}";
	$data=array(
	'touser'=>$openid,
	'msgtype'=>"text",
	'text'   =>array('content'=>urlencode($text)),
	);
	$r=$this->mycurl($url,urldecode(json_encode($data)));
	return $r;
  }
   /*********上传媒体TYPE分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）*****/
    public function uploadM($file,$type){
		$file=ROOT_PATH.$file;//"/Uploads/sucai/20140905/54095f7898331.jpg";
		$ACCESS_TOKEN=$this->access_token;
		$url="http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$ACCESS_TOKEN}&type={$type}";
		$data=array('media'=>"@".$file);
		$r=$this->mycurl($url,$data);
		$data=json_decode($r,true);
	    return $data;
	}
   
     /***************发送图片、语音、视频******************/
    public function sendMedia($type,$MediaId,$OPENID){
	  $data='{"touser":"'.$OPENID.'","msgtype":"'.$type.'","'.$type.'":{ "media_id":"'.$MediaId.'"}}';
	  $ACCESS_TOKEN=$this->access_token;
      $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$ACCESS_TOKEN}";
	  $r=$this->mycurl($url,$data);
	  $r=json_decode($r,true);
	  return $r;
	}

 
  /***********群发接口,发送多图文openidArray*****************/
	public function groupSend_news($openidArray,$data){
	    $ACCESS_TOKEN=$this->access_token;
	  	$url="https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token={$ACCESS_TOKEN}";
        $r=$this->mycurl($url,urldecode(json_encode($data)));
		$r=json_decode($r,true);
		if(!$r['media_id']) return $r;
		$touser=$openidArray?'"touser": ['.$openidArray.'],':"";
		$send='{'.$touser.'"mpnews":{"media_id":"'.$r['media_id'].'"},"msgtype":"mpnews"}';
		$url2=$openidArray?"https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$ACCESS_TOKEN}":"https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$ACCESS_TOKEN}";
		$r2=$this->mycurl($url2,$send);
		$r2=json_decode($r2,true);
		return $r2;
	}
   
	
	/***群发接口,发送多文本****/
	public function groupSend_text($openidArray,$data){
	    $ACCESS_TOKEN=$this->access_token;
		$touser=$openidArray?'"touser": ['.$openidArray.'],':"";
		$send='{'.$touser.'"msgtype": "text", "text": { "content": "'.$data.'"}}';
	    $url2=$openidArray?"https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$ACCESS_TOKEN}":"https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$ACCESS_TOKEN}";	
	    $r2=$this->mycurl($url2,$send);
	    return json_decode($r2,true);
      
	}
	/***群发接口,发送视频****/
	public function groupSend_video($openidArray,$data){
	 $ACCESS_TOKEN=$this->access_token;
	 $url= "https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo?access_token={$ACCESS_TOKEN}";
	 $row=$data;
	 $data['title']=urlencode($data['title']);
	 $data['description']=urlencode($data['description']);
	 $r=$this->mycurl($url,urldecode(json_encode($data)));
	 $r=json_decode($r,true);
	 $touser=$openidArray?'"touser": ['.$openidArray.'],':"";
	 $send='{'. $touser.'
		   "video":{
			  "media_id":"'.$r['media_id'].'",
			  "title":"'.$row['title'].'",
			  "description":"'.$row['description'].'"
		   },
		   "msgtype":"video"
		  }';
	 $url2=$openidArray?"https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$ACCESS_TOKEN}":"https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$ACCESS_TOKEN}";
	 $r2=$this->mycurl($url2,$send);
	 return json_decode($r2,true);
	 
	}
	
	/***群发接口 发图片，声音****/
	public function groupSend_media($openidArray,$data){
	 $ACCESS_TOKEN=$this->access_token;
	 $touser=$openidArray?'"touser": ['.$openidArray.'],':"";
	 $send='{'.$touser.'
		   "'.$data['type'].'":{
			  "media_id":"'.$data['media_id'].'",
		   },
		   "msgtype":"'.$data['type'].'"
		  }';
	$url2=$openidArray?"https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token={$ACCESS_TOKEN}":"https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$ACCESS_TOKEN}";
	 $r2=$this->mycurl($url2,$send);
	 return json_decode($r2,true);
	}
	
    /***************
	     发送图文消息
	         $data=array(array(
	          title":"Happy Day",
             "description":"Is Really A Happy Day",
             "url":"URL",
             "picurl":"PIC_URL"
			 ));
	******************/
  public function sendNews($openid,$data){
    $ACCESS_TOKEN=$this->access_token;
	foreach($data as $k=>$one){
	 $data[$k]['title']=urlencode($one['title']);
	 $data[$k]['description']=urlencode($one['description']);
	}
    $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$ACCESS_TOKEN}";
	$data=array(
	'touser'=>$openid,
	'msgtype'=>"news",
	'news'   =>array('articles'=>$data),
	);
	$r=$this->mycurl($url,urldecode(json_encode($data)));
	return $r;
  }
  
 
  /***$url 地址 ￥post_file 提交的内容*/
  public function mycurl($url,$post_file=''){
        //$cookie_file =SCRIPT_ROOT;   
		$agent= 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0';
	//   $header="Content-Type:text/plain";////定义content-type为plain
		$header="Content-Type:text/html;charset=UTF-8";////定义content-type为plain
		$ch=curl_init(); /////初始化一个CURL对象
		curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:81.18.18.18', 'CLIENT-IP:81.18.18.18'));  //构造IP  
	 // curl_setopt($ch, CURLOPT_HEADER, 1);  
	   // curl_setopt($ch, CURLOPT_HEADER, true);//返回header
		// curl_setopt($ch, CURLOPT_NOBODY,true);
	    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); ///设置不输出在浏览器上
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);//50秒超时
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		/******提交********/
		if($post_file){
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_file);  ////传递一个作为HTTP "POST"操作的所有数据的字符
		}
		//curl_setopt($ch, CURLOPT_COOKIEFILE, SCRIPT_ROOT);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
		/***认证**/
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	   //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true); ;
       //curl_setopt($ch,CURLOPT_CAINFO,ROOT_PATH.'/cacert.pem');
		$content = curl_exec($ch);
		if(curl_errno($ch)){//出错则显示错误信息
           return curl_error($ch);
		   return;
		   //exit();
		 }
        return $content;

    }
			
}

