<?php  
/**
 * @name curl.class.php
 * @desc curl操作类
 * @author malixiao
 * @createtime 2013-3-5 14:58
 * @updatetime
 * $curl_obj = new Curl('http://www.demo.com/');
 * 基本使用方法
 * $curl_obj->init();
 * $curl_obj->setOptions(array("type"=>"post", "fields"=>$fields, "return"=>1, "onerror"=>1));
 * $result = $curl_obj->getResult();
 * 另外还提供了两种快速使用方法
 * 1.post方式
 * $result = $curl_obj->post(array('param' => 'value'));
 * 2.get方法
 * $result = $curl_obj->get();
 * 
 */
namespace Org\Nx;

class Curl
{
  /**
  * 当前curl对话
  * @var resource
  * @access private
  */
  private $ch;
  
  /**
  * 当前发送的地址
  *
  * @var string
  * @access private
  */
  private $url;
  
  /**
  * 调试信息
  *
  * @var string
  * @access private
  */
  private $debug; 
  
  /**
  * 返回是否包括header头
  *
  * @var integer
  * @access private
  */
  private $header = 0; 
  
  /**
  * 构造函数
  * @return void
  * @access public
  */
  public function __construct($url)
  {
      $this->url = $url;
  }
  
  /**
  * 设定返回是否包括header 头
  * @param integer $header 0或1,1包括，0不包括
  * @return void 
  * @access public
  */
  public function setHeader($header = 0)
  {
      $this->header = $header;
  }
  
  
  /**
  * 初始化curl对话
  * @param string $url 当前发送的地址
  * @return boolean
  * @access private
  */
  private function init()
  {
      $this->ch = @curl_init();
      if (!$this->ch)
      {
          return false;
      }
      $this->basic();
      return true;
  }
  
  /** 
  * 基本选项
  *
  * @return void
  * @access private
  */
  private function basic()
  {
      curl_setopt($this->ch, CURLOPT_URL, $this->url);
      curl_setopt($this->ch, CURLOPT_HEADER, $this->header);
  }

  /**
  * 设置选项
  *
  * @return void
  * @access public
  */
  public function setOptions($options = array())
  {
      if (is_array($options))
      {
          foreach ($options as $key=>$value)    
          {
              $this->$key = $value;    
          }
      }
      //如果HTTP返回大于300, 是否显示错误
      if (isset($this->onerror) && $this->onerror)
      {
          curl_setopt($this->ch, CURLOPT_FAILONERROR, 1);    
      }
      
      //是否有返回值
      if (isset($this->return) && $this->return == true && !isset($this->file)) 
      {
          curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
      }
      
      //HTTP 认证
      if (isset($this->username) && $this->username != "") 
      {
          curl_setopt($this->ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
      }
      
      //SSL 检查
      if (isset($this->sslVersion)) 
      {
          curl_setopt($this->ch, CURLOPT_SSLVERSION, $this->sslVersion);
      }
      if (isset($this->sslCert)) 
      {
          curl_setopt($this->ch, CURLOPT_SSLCERT, $this->sslCert);
      }
      if (isset($this->sslCertPasswd)) 
      {
          curl_setopt($this->ch, CURLOPT_SSLCERTPASSWD, $this->sslCertPasswd);
      }
      
      //代理服务器
      if (isset($this->proxy))
      {
          curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
      }
      if (isset($this->proxyUser) || isset($this->proxyPassword)) 
      {
          curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, "{$this->proxyUser}:{$this->proxyPassword}");
      }
      
      //传输类型
      if (isset($this->type)) 
      {
          switch (strtolower($this->type)) 
          {
              case "post":
                  curl_setopt($this->ch, CURLOPT_POST, 1);
                  break;
              case "put":
                  curl_setopt($this->ch, CURLOPT_PUT, 1);
                  break;
          }
      }        
      
      //上传相关
      if (isset($this->file)) 
      {
          if (!isset($this->filesize)) 
          {
              $this->filesize = filesize($this->file);
          }
          curl_setopt($this->ch, CURLOPT_INFILE, $this->file);
          curl_setopt($this->ch, CURLOPT_INFILESIZE, $this->filesize);
          curl_setopt($this->ch, CURLOPT_UPLOAD, 1);
      }
      
      //数据发送
      if (isset($this->fields)) 
      {
          if (!is_array($this->fields))
          {
              if (!isset($this->type))
              {
                  $this->type = "post";
                  curl_setopt($this->ch, CURLOPT_POST, 1);
              }
              curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->fields);
          }
          else 
          {
              if (!empty($this->fields))
              {
                  $p = array();
                  foreach ($this->fields as $key=>$value)
                  {
                      $p[] = $key . "=" . urlencode($value);
                  }
                  if (!isset($this->type))
                  {
                      $this->type = "post";
                      curl_setopt($this->ch, CURLOPT_POST, 1);
                  }
                  curl_setopt($this->ch, CURLOPT_POSTFIELDS, implode("&", $p));
              }        
          }
      }
      
      
      //错误相关
      if (isset($this->progress) && $this->progress == true) 
      {
          curl_setopt($this->ch, CURLOPT_PROGRESS, 1);
      }
      if (isset($this->verbose) && $this->verbose == true) 
      {
          curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
      }
      if (isset($this->mute) && !$this->mute) 
      {
          curl_setopt($this->ch, CURLOPT_MUTE, 0);
      }

      //其它相关
      if (isset($this->followLocation))
      {
          curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
      }
      if (isset($this->timeout) && $this->timeout>0)
      {
          curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);    
      }
      else
      {
          curl_setopt($this->ch, CURLOPT_TIMEOUT, 20);
      }
      if (isset($this->connecttimeout) && $this->connecttimeout>0)
      {
          curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);    
      }
      else
      {
          curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
      }
      if (isset($this->userAgent)) 
      {
          curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
      }
      
      //cookie 相关
      if (isset($this->cookie)) 
      {
          $cookieData = "";
          foreach ($this->cookie as $name => $value) 
          {
              $cookieData .= urlencode($name) . "=" . urlencode($value) . ";";
          }
          curl_setopt($this->ch, CURLOPT_COOKIE, $cookieData);
      }
      
      //http 头
      if (isset($this->httpHeaders)) 
      {
          curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpHeaders );
      }
  }    
      
  /**
  * 设置返回结果选项，并返回结果
  * @return string 返回结果
  * @access public
  */
  public function getResult()
  {
      $result = curl_exec($this->ch);
      return $result;
  }
  
  /**
  * 关闭当前curl对话
  * @return void
  * @access public
  */
  public function close()
  {
      @curl_close($this->ch);    
  }
  
  /**
  * 得到对话中产生的错误描述
  * @return string 错误描述
  * @access public
  */
  public function getError()
  {
      return curl_error($this->ch);    
  }
  
  /**
  * 得到对话中产生的错误号
  * @return integer 错误号
  * @access public
  */
  public function getErrno()
  {
      return curl_errno($this->ch);    
  }
  
  /**
  * 中断执行，并输出错误信息
  * @param string $msg 错误信息
  * @return void
  * @access private
  */
  private function halt($msg)
  {
      $message = "\n<br>信息:{$msg}";
      $message .= "\n<br>错误号:".$this->getErrno();
      $message .= "\n<br>错误:".$this->getError();
      echo $message;
      exit;
  }    
  
  /**
  * 调试信息
  * 
  * @return void
  * @access private
  */
  private function debug()
  {
      $message .= "\n<br>错误号:".$this->getErrno();
      $message .= "\n<br>错误:".$this->getError();
      $this->debug = $message;
  }    
  
  /**
  * 获得以POST方式发送的结果
  * @param array/string $fields 发送的数据
  * @return string 返回的结果
  * @access public
  */
  public function post($fields = array())
  {
      $re = $this->init();
      if ($re){
          $this->setOptions(array("type"=>"post", "fields"=>$fields, "return"=>1, "onerror"=>1));
          $result = $this->getResult();
          //$this->close();
          return $result;
      }else{
          return "";
      }
  }
  
  /**
  * 获得以GET方式发送的结果
  * @return string 返回的结果
  * @access public
  */
  public function get()
  {
      $re = $this->init();
      if ($re){
          $this->setOptions(array("return"=>1, "onerror"=>1));
          $result = $this->getResult();
          $this->close();
          return $result; 
      }else{
          return "";
      }
  }
  
  /**
  * 静态调用，获得以COOKIE方式发送的结果
  * @param string $url 发送的地址
  * @param array/string $fields 发送的数据
  * @return string 返回的结果
  * @access public
  */
  public function cookie($fields = array())
  {
      $re = $this->init();
      if ($re){
          $this->setOptions(array("cookie"=>$fields, "return"=>1, "onerror"=>1));
          $result = $this->getResult();
          $this->close();
          return $result; 
      }else{
          return "";
      }
  }
}
?>