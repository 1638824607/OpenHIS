<?php
/**
 *  功能测试
 * User: wsl
 * Date: 2017-10-24
 */
namespace His\Controller;
#use Common\Controller\Ba;

use MatthiasMullie\Minify;

class TestController extends HisBaseController
{
    protected $_userInfo; //用户信息包括当前医院id
    protected $member_model;

    public function _initialize()
    {
        /*if(APP_DEBUG)C("SHOW_PAGE_TRACE",true);#页面调试窗


        C('TITLE',"测试");

        if(APP_DEBUG){
            trace($this->_userInfo,'用户信息');
            #trace($this->hospitalInfo,'医院信息');
        }*/
    }

    /**
     * 首页
     * @Author   wsl
     * @DateTime 2017-10-24
     */
    public function index()
    {

        #https://github.com/matthiasmullie/minify
       # phpinfo();exit;
        $path = THINK_PATH.'Library/Vendor';
        require_once $path . '/minify/src/Minify.php';
        require_once $path . '/minify/src/CSS.php';
        require_once $path . '/minify/src/JS.php';
        require_once $path . '/minify/src/Exception.php';
        require_once $path . '/minify/src/Exceptions/BasicException.php';
        require_once $path . '/minify/src/Exceptions/FileImportException.php';
        require_once $path . '/minify/src/Exceptions/IOException.php';
        require_once $path . '/path-converter/src/ConverterInterface.php';
        require_once $path . '/path-converter/src/Converter.php';


        $type = I('get.type','css');

        switch ($type) {
            case 'css':
                $cache_file = WEB_ROOT_PATH.'Public/his/css/minify.css';

                if(file_exists($cache_file)){
                    header('Location: /Public/his/css/minify.css');

                    exit;
                    #$content = file_get_contents($cache_file);
                    #exit($content);
                }

                header('Content-Type: text/css; charset=UTF-8');

                $minifier = new Minify\CSS(WEB_ROOT_PATH.'Public/his/css/bootstrap.min.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/css/demo.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/font-awesome/css/font-awesome.min.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/linearicons/style.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/css/main.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/css/demo.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/css/public.css');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/datetimepicker/jquery.datetimepicker.css');

                echo $minifier->minify($cache_file);

                break;

            case 'js':

                $cache_file = WEB_ROOT_PATH.'Public/his/js/minify.js';

                if(file_exists($cache_file)){
                    header('Location: /Public/his/js/minify.js');
                    exit;
                    #$content = file_get_contents($cache_file);
                    #exit($content);
                }
                header('Content-Type: text/javascript; charset=UTF-8');

                $minifier = new Minify\JS(WEB_ROOT_PATH.'Public/his/vendor/jquery/jquery.min.js');

                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/bootstrap/js/bootstrap.min.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/jquery-slimscroll/jquery.slimscroll.min.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/chartist/js/chartist.min.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/scripts/klorofil-common.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/datetimepicker/jquery.datetimepicker.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/js/public.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/js/check.form.js');
                #$minifier->add(WEB_ROOT_PATH.'Public/his/js/echarts.min.js');
                $minifier->add(WEB_ROOT_PATH.'Public/his/vendor/layer/layer.js');

                echo $minifier->minify($cache_file);
                break;
            case 'update':
                $cache_file = WEB_ROOT_PATH.'Public/his/js/minify.js';

                if(file_exists($cache_file))unlink($cache_file);

                $cache_file = WEB_ROOT_PATH.'Public/his/css/minify.css';

                if(file_exists($cache_file))unlink($cache_file);

                echo 'ok';

        }



        exit;

        /*
        Available methods, for both CSS & JS minifier, are:

__construct( overload paths )

        The object constructor accepts 0, 1 or multiple paths of files, or even complete CSS/JS content, that should be minified. All CSS/JS passed along, will be combined into 1 minified file.

use MatthiasMullie\Minify;
$minifier = new Minify\JS($path1, $path2);
add($path,  overload paths )

This is roughly equivalent to the constructor.

    $minifier->add($path3);
$minifier->add($js);
minify($path)

This will minify the files' content, save the result to $path and return the resulting content. If the $path parameter is omitted, the result will not be written anywhere.

CAUTION: If you have CSS with relative paths (to imports, images, ...), you should always specify a target path! Then those relative paths will be adjusted in accordance with the new path.

$minifier->minify('/target/path.js');
gzip($path, $level)

Minifies and optionally saves to a file, just like minify(), but it also gzencode()s the minified content.

$minifier->gzip('/target/path.js');
setMaxImportSize($size) (CSS only)

The CSS minifier will automatically embed referenced files (like images, fonts, ...) into the minified CSS, so they don't have to be fetched over multiple connections.

    However, for really large files, it's likely better to load them separately (as it would increase the CSS load time if they were included.)

This method allows the max size of files to import into the minified CSS to be set (in kB). The default size is 5.

$minifier->setMaxImportSize(10);
setImportExtensions($extensions) (CSS only)

The CSS minifier will automatically embed referenced files (like images, fonts, ...) into minified CSS, so they don't have to be fetched over multiple connections.

    This methods allows the type of files to be specified, along with their data:mime type.

    The default embedded file types are gif, png, jpg, jpeg, svg & woff.

    $extensions = array(
        'gif' => 'data:image/gif',
        'png' => 'data:image/png',
    );

$minifier->setImportExtensions($extensions);
        */


        $redis = $this->getRedis();

        $redis->set('wsl','324234');

        echo 'wsl:',$redis->get('wsl'),"\n\n";



    }

    public function cli()
    {
        $msg = 'cli mode '.date('Y-m-d H:i:s');
        $this->log($msg);
        echo 'ok:',$msg,"\n";
    }

    protected function log($data)
    {
        $str = is_string($data)?$data:json_decode($data);

        $filename=TEMP_PATH.'cli_log.txt';

        $handle=fopen($filename,"a+");

        fwrite($handle,$str."\n\n");

        fclose($handle);

    }

}
?>