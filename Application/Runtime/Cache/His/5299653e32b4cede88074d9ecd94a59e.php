<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <link href="/Upload/groupPic/favicon.ico" rel="shortcut icon">
    <title><?php echo C('TITLE');?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="/Public/his/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Public/his/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/Public/his/vendor/linearicons/style.css">
    <link rel="stylesheet" href="/Public/his/vendor/chartist/css/chartist-custom.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="/Public/his/css/main.css?<?php echo time();?>">
    <!-- FOR DEMO PURPOSES ONLY. You should remove this in your project -->
    <link rel="stylesheet" href="/Public/his/css/demo.css?<?php echo time();?>">
    <!-- public -->
    <link rel="stylesheet" href="/Public/his/css/public.css?<?php echo time();?>">

    <!-- ICONS >
    <link rel="apple-touch-icon" sizes="76x76" href="/Public/his/img/apple-icon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="__PUBLIC_ROBOT__/img/favicon.png"-->
    <link rel="stylesheet" type="text/css" href="/Public/his/vendor/datetimepicker/jquery.datetimepicker.css"/>

    <script src="/Public/his/vendor/jquery/jquery.min.js"></script>
    <script src="/Public/his/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="/Public/his/vendor/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/Public/his/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="/Public/his/vendor/chartist/js/chartist.min.js"></script>
    <script src="/Public/his/scripts/klorofil-common.js"></script>
    <script src="/Public/his/vendor/datetimepicker/jquery.datetimepicker.js"></script>
    <script src="/Public/his/js/public.js?<?php echo time();?>"></script>
    <script src="/Public/his/js/check.form.js?<?php echo time();?>"></script>
    <script src="/Public/his/vendor/layer/layer.js"></script>
    <!--<script src="/Public/his/js/echarts.min.js"></script>-->


</head>
<body>


<!-- WRAPPER -->
    <div id="wrapper">
<!-- NAVBAR -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="brand">
        <a href="index.html"><img src="/Public/his/img/logo-dark.png" alt="Klorofil Logo" class="img-responsive logo"></a>
    </div>
    <div class="container-fluid">
        <div id="navbar-menu">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <?php if($_SESSION['user_info']['p_id']==0){?>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php if (!empty($_SESSION['user_relate']['picture'])){?>
                            <img src="<?php echo C('UPLOAD_DOCTOR').$_SESSION['user_relate']['picture'];?>" class="img-circle" alt="诊所图片">
                            <?php }else{?>
                            <img src="<?php echo C('UPLOAD_DOCTOR').'doctor_def.jpg';?>">
                            <?php }?>
                            <span><?php echo $_SESSION['user_relate']['owner_name'];?></span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <?php }else{?>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php if (!empty($_SESSION['user_relate']['picture'])){?>
                            <img src="<?php echo C('UPLOAD_DOCTOR').$_SESSION['user_relate']['picture'];?>" class="img-circle" alt="诊所图片">
                            <?php }else{?>
                            <img src="<?php echo C('UPLOAD_DOCTOR').'doctor_def.jpg';?>">
                            <?php }?>
                            <span><?php echo $_SESSION['user_relate']['true_name']?></span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <?php }?>
                    <ul class="dropdown-menu">
                        <li class="revisePwd"><a href="javascript:void(0)"><i class="lnr lnr-exit"></i> <span>修改密码</span></a></li>
                        <li><a href="/Login/logout"><i class="lnr lnr-exit"></i> <span>退出登录</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- END NAVBAR -->
<!-- LEFT SIDEBAR -->
<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <ul class="nav" id="ulApp">
                <li><a href="<?php echo U('/Index/index');?>" class="index"><i class="lnr lnr-home"></i> <span>首页</span></a></li>
                <?php if($_SESSION['user_info']['p_id']!=0):?>
                <li><a href="<?php echo U('/Index/editPersonal');?>" class="personal"><i class="fa fa-user"></i> <span>个人资料</span></a></li>
                <?php endif;?>
            </ul>
        </nav>
    </div>
</div>
<!-- END LEFT SIDEBAR -->

<!-- MAIN -->
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <!-- OVERVIEW -->
                <div class="hospCloudList clearfix hospitalShow">

                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END MAIN -->
</div>
<script>
    var is_checking = 0;
    //选项卡切换
    $(document).ready(function(){
        $(".index").addClass('active').siblings('li').removeClass('active').closest('#ulApp');
        $('#ulApp').find('> li').eq($(this).index()).addClass('active').siblings('li').removeClass('active');
    });
    //修改密码弹框
    $(document).on('click', '.revisePwd', function () {
        //$('#revisePwdBomb').fadeIn();
        //iframe层
        layer.open({
            type: 2,
            title: '修改密码',
            shadeClose: true,
            shade: 0.8,
            area: ['500px','400px'],
            content: '/Index/editPassword' //iframe的url
        });

    });

    //加载页面
    $(document).ready(function(){
        $.post("<?php echo U('/Index/index');?>",
            function(data){
                if (data.status == 'success') {
                    if (data.msg == '') {
                        window.location.href = data.data.url;
                    } else {
                        if (data.data) {
                            var html = '';
                            html += '<a class="cloudBox hospitalBox" href="/index.php/index/base_index?hospital_id='+data.data.hid+'">' +
                                '<div class="topLi"></div>' +
                                '<img class="hospLogo" src="'+(data.data.picture?'/Upload/personal/'+data.data.picture:'/Upload/groupPic/hospital.jpg')+'" >'+
                                '<div class="hospName">'+data.data.hospital_name+'</div>' +
                                '</a>';
                            $('.hospitalShow').html(html);
                        } else {
                            remindBox(data.data.msg);
                        }
                    }
                } else {
                    var html = '';
                    html += '<a class="cloudBox hospitalBox" href="/index.php/index/base_index?hospital_id='+data.fields.hospitalLists.hid+'">' +
                        '<div class="topLi"></div>' +
                        '<img class="hospLogo" src="'+(data.fields.hospitalLists.picture?'/Upload/personal/'+data.fields.hospitalLists.picture:'/Upload/groupPic/hospital.jpg')+'" >'+
                        '<div class="hospName">'+data.fields.hospitalLists.hospital_name+'</div>' +
                        '</a>';
                    $('.hospitalShow').html(html);
                        var content = '<img src="'+data.fields.url+'" style="width:292px;height:273px;margin:16px 10px 4px 12px;">';
                        layer.open({
                            title:data.fields.msg,
                            type: 1,
                            //skin: 'layui-layer-rim', //加上边框
                            area: ['315px','355px'], //宽高
                            content: content,
                            cancel: function(index, layero){
                                is_checking = 0;
                            }
                        });
                        is_checking = 1;
                    setTimeout(check_bind,3000);
                }

            },
            'json')

    });

    function check_bind() {
        if(is_checking==0){
            return false;
        }
        $.post("<?php echo U('/Index/index');?>",
            function(data){
                if (data.status == 'success') {
                    if (data.msg == '') {
                        window.location.href = data.data.url;
                    } else {
                        if (data.data) {
                            var html = '';
                            html += '<a class="cloudBox hospitalBox" href="/index.php/index/base_index?hospital_id='+data.data.hid+'">' +
                                '<div class="topLi"></div>' +
                                '<img class="hospLogo" src="'+(data.data.picture?'/Upload/personal/'+data.data.picture:'/Upload/groupPic/hospital.jpg')+'" >'+
                                '<div class="hospName">'+data.data.hospital_name+'</div>' +
                                '</a>';
                            $('.hospitalShow').html(html);
                        } else {
                            remindBox(data.data.msg);
                        }
                    }
                    layer.close();
                } else {
                      setTimeout(check_bind,3000);
                }
            },
            'json')

    }
</script>

<!-- END WRAPPER -->

<script type="text/javascript">
    if(parent.endLoad){
        parent.endLoad();
    }
</script>
</body>
</html>