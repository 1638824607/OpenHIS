<?php
// +----------------------------------------------------------------------
// | 大宅门云诊所系统 [ version 1.0 ]  http://bbs.dzmtech.com
// +----------------------------------------------------------------------
// | Copyright (c) 2017 (北京天元九合科技有限公司) All rights reserved.
// +----------------------------------------------------------------------
// | Author: wsl
// +----------------------------------------------------------------------

namespace His\Controller;

use Common\Controller\PublicBaseController;
use Common\Model\WxmpModel;

include THINK_PATH . 'Library/Vendor/Phpqrcode/qrlib.php';

/**
 * 二维码显示功能
 * QrController
 * Author: wsl
 */
class QrController extends PublicBaseController
{

    public $hospitalInfo;

    /**
     * 显示登录二维码
     * Author: wsl
     */
    public function index()
    {

        $this->hospitalInfo = session('hospital_info');

        $type = I('get.type', '');
        $id = I('get.id');
        $size = I('get.size', 3);
        $margin = I('get.margin', 2);
        if (!$id) exit;
        $url = '';
        #根据不同类型，生成不同链接
        switch ($type) {
            case 'bindwx':
                is_numeric($id) && $id = $this->encrypt($id);
                $url = C('MAIN_SERVER_DOMAIN').'login/bindwx?hid=' . $this->hospitalInfo['uid'] . '&id=' . $id;
                break;
            case 'pay':
                $url = C('MAIN_SERVER_DOMAIN').'Pay/go?id=' . $id;
                break;
            default:
                $url = C('MAIN_SERVER_DOMAIN').'login/go?id=' . $id;
        }

        if ($url == '') exit;
        header('Content-Type: image/png');
        \QRcode::png($url, null, QR_ECLEVEL_H, $size, $margin);
        exit;
    }

    /**
     * 显示微信二维码
     * Author: wsl
     */
    public function wxqr()
    {
        $size = I('get.size', 3);
        $margin = I('get.margin', 2);
        $id = I('get.id');
        if (!$id) exit;
        $qr = M('Wx_group_info')->where("id='$id'")->find();
        if (!$qr) exit('id invalid');

        header('Content-Type: image/png');
        \QRcode::png($qr['url'], null, QR_ECLEVEL_H, $size, $margin);
        exit;
    }

}