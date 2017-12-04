<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="en" class="fullscreen-bg">

<head>
    <title>登陆</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="/Public/his/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Public/his/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/Public/his/vendor/linearicons/style.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="/Public/his/css/main.css">
    <!-- FOR DEMO PURPOSES ONLY. You should remove this in your project -->
    <link rel="stylesheet" href="/Public/his/css/demo.css">
    <!-- public -->
    <link rel="stylesheet" href="/Public/his/css/public.css">

    <link href="/Upload/groupPic/favicon.ico" rel="shortcut icon">

    <script src="/Public/his/vendor/jquery/jquery.min.js"></script>
    <script src="/Public/his/vendor/layer/layer.js"></script>
</head>

<body>
<!-- WRAPPER -->
<div id="wrapper">
    <div class="vertical-align-wrap">
        <div class="vertical-align-middle">
            <div class="auth-box ">
                <div class="left">
                    <div class="content">
                        <div class="header">
                            <div class="logo text-center">
                                <img src="/Public/his/img/logo_login.jpg" alt="dzm" width="350">
                                <!--<img src="/Public/his/img/manshi.png" alt="dzm" width="350">-->
                            </div>
                            <p class="lead">使用您的云诊所账号登录</p>
                        </div>
                        <form class="form-auth-small" action="index.php" id="form_login">
                            <ul class="tabBtn clearfix">
                                <li  <?php if($qr_img): ?>class="on"<?php endif; ?> ><span></span>扫码登陆</li>
                                <li  <?php if(!$qr_img): ?>class="on"<?php endif; ?> ><span></span>账户登录</li>
                            </ul>
                            <ul class="tabBox mt10" style="min-height: 186px;">
                                <li  <?php if($qr_img): ?>class="on"<?php endif; ?>>
                                    <div class="codeErm">
                                        <?php if($qr_img): ?><img src="<?php echo ($qr_img); ?>" />
                                            <p class="gray mt10">打开 手机微信  扫描二维码</p>
                                            <?php else: ?>
                                            <div>
                                                <h3 style="color: red;">未设置微信登录，无法使用</h3>
                                            </div><?php endif; ?>
                                        <div class="fz12 mt10 gray2">
                                            <span class="mr10"><i class="fa fa-pencil"></i> 免输入</span><span class="mr10"><i class="fa fa-bolt"></i> 更快</span><span class=""><i class="fa fa-shield"></i> 更安全</span>
                                        </div>
                                    </div>
                                </li>
                                <li <?php if(!$qr_img): ?>class="on"<?php endif; ?>>
                                    <div class="form-group">
                                        <label for="signin-email" class="control-label sr-only">手机号/邮箱</label>
                                        <input type="text" class="form-control"  name="n" id="signin-email" value="" placeholder="手机号/邮箱">
                                    </div>
                                    <div class="form-group">
                                        <label for="signin-password" class="control-label sr-only">密码</label>
                                        <input type="password" class="form-control"  name="p" id="signin-password" value="" placeholder="密码">
                                    </div>
                                    <div class="form-group clearfix">
                                        <label class="fancy-checkbox element-left">
                                           <input type="text" id="verify_code" value="" placeholder="验证码" style="height: 38px;" maxlength="10" />
                                        </label>
                                        <img src="/Login/createVerify" width="40%" onclick="this.src='/Login/createVerify?id='+Math.random()" id="vimg" />
                                    </div>
                                    <button type="button" id="btn_submit" class="btn btn-primary btn-lg btn-block" style="margin-top: inherit; padding: 3px;">登陆</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
                <div class="right">
                    <div class="overlay"></div>
                    <div class="content text">
                        <h1 class="heading"></h1>
                        <p></p>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<!-- END WRAPPER -->
<!--<?php echo ($qr_img_content); ?>-->
</body>

</html>

<script type="text/javascript">
    var enid='<?php echo ($enid); ?>';
    $(function () {
        //选项卡切换
        $('.tabBtn > li').click(function(){
            $(this).addClass('on').siblings('li').removeClass('on').closest('.tabBtn');
            $(this).closest('.tabBtn').siblings('.tabBox').find('> li').eq($(this).index()).addClass('on').siblings('li').removeClass('on');
        });

        if(enid.length>0)check_qr_scan(enid);

        $("#btn_submit").click(function () {
            var u = $("#signin-email").val();
            var p = $("#signin-password").val();
            var verify_code = $("#verify_code").val();

            if(u==''||u.length==0){
                layer.msg('请填写手机号或邮箱');
                return false;
            }

            if(p==''||p.length==0){
                layer.msg('请填写登录密码');
                return false;
            }

            if(verify_code==''||verify_code.length==0){
                layer.msg('请填写登录验证码');
                return false;
            }

            var f = $('input[type=hidden]').val();


            $.post('/login/userlogin',{u:u,p:p,verify_code:verify_code,__hash__:f},function (res) {
                if(res.status==0){
                    var d = res.data;
                    window.location.href='/login/enlogin?enuid='+d.enuid;
                }else if(res.status==5){
                    $("#vimg").attr('src','/Login/createVerify');
                    $("#verify_code").val('');
                    layer.msg(res.msg);
                }else{
                    layer.msg(res.msg);
                }
            },'json');

        });
    });

    function check_qr_scan(enid) {
        $.post('/login/check_scan',{enid:enid},function (res) {
            if(res.status==0){
                var d = res.data;
                if(d.status!=2){
                    setTimeout("check_qr_scan('"+d.enid+"')",3000);
                }else{
                    window.location.href='/login/enlogin?enuid='+d.enuid+'&enid='+d.enid;
                }
            }else{
                layer.msg(res.msg);
            }
        },'json');
    }
</script>