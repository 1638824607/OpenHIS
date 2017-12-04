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
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="brand">
        <img src="/Public/his/img/logo.png" style="width: 40px;height: 40px; display: inline-block"/>
        <a style="font-size:16px"><?php echo $_SESSION['user_relate']['hospital_name']?></a>
    </div>
    <div class="container-fluid">
        <div class="navbar-btn">
            <button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i></button>
        </div>

        <div id="navbar-menu">
            <ul class="nav navbar-nav navbar-right">

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="lnr lnr-question-circle"></i> <span>帮助中心</span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="/Upload/specifications.pdf" target="_blank"><i class="fa fa-address-book-o"></i>使用手册</a></li>
                    </ul>
                </li>

                <?php if($_SESSION['user_info']['p_id']==0){?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php if (!empty($_SESSION['user_relate']['picture'])){?>
                        <img src="<?php echo C('UPLOAD_DOCTOR').$_SESSION['user_relate']['picture'];?>" class="img-circle" alt="诊所图片">
                        <?php }else{?>
                        <img src="<?php echo C('UPLOAD_DOCTOR').'doctor_def.jpg';?>">
                        <?php }?>
                        <span><?php echo $_SESSION['user_relate']['owner_name'];?></span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li class="revisePwd"><a href="javascript:void(0)"><i class="lnr lnr-exit"></i> <span>修改密码</span></a></li>
                        <li><a href="/Login/logout"><i class="lnr lnr-exit"></i> <span>退出登录</span></a></li>

                    </ul>
                </li>
                <?php }else{?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php if (!empty($_SESSION['user_relate']['picture'])){?>
                        <img src="<?php echo C('UPLOAD_DOCTOR').$_SESSION['user_relate']['picture'];?>" class="img-circle" alt="医生图片" />
                        <?php }else{?>
                        <img src="<?php echo C('UPLOAD_DOCTOR').'doctor_def.jpg';?>">
                        <?php }?>
                        <span><?php echo $_SESSION['user_relate']['true_name']?></span> <i class="icon-submenu lnr lnr-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li class="revisePwd"><a href="javascript:void(0)"><i class="lnr lnr-exit"></i> <span>修改密码</span></a></li>
                        <li><a href="/Index/index"><i class="lnr lnr-exit"></i> <span>返回首页</span></a></li>
                        <li><a href="/Login/logout"><i class="lnr lnr-exit"></i> <span>退出登录</span></a></li>

                    </ul>
                </li>
                <?php }?>

            </ul>
        </div>
        <nav class="webNav">
            <?php if(is_array($menu)): foreach($menu as $key=>$v): ?><a class="btn btn-primary dzm-menu" data-id="<?php echo ($v["id"]); ?>"  href="javascript:void(0)" ><?php echo ($v["title"]); ?></a><?php endforeach; endif; ?>
        </nav>
    </div>
</nav>
<!-- END NAVBAR -->
<!-- LEFT SIDEBAR -->
<div id="sidebar-nav" class="sidebar">
    <div class="sidebar-scroll">
        <nav>
            <ul class="nav" id="menu-nav">
                <?php if(is_array($menu1)): foreach($menu1 as $key=>$v): ?><li class="li-menu"><a href="<?php echo U($v[menu_name]);?>" class="menu" id="<?php echo ($v["id"]); ?>" p-id="<?php echo ($v["pid"]); ?>" target="main"><i class="fa fa-medkit"></i> <span><?php echo ($v["title"]); ?></span></a></li><?php endforeach; endif; ?>
            </ul>
        </nav>
    </div>
</div>
<!-- END LEFT SIDEBAR -->
<!--主体右部分-->
<iframe src="" class="main" name="main" id="main" frameborder="0" scrolling="yes" onload="this.height=100"></iframe>
</div>
<script type="text/javascript">
    var load_idx ;
    var isHospital=<?php echo $isHospital;?>;
    $(function() {
        $(".dzm-menu").click(function () {

            $('#menu-nav').html('');
            var id =$(this).attr('data-id');
            $(this).attr('class','btn btn-primary dzm-menu');
            $(this).siblings().attr('class','btn btn-default dzm-menu');
            var html = "";
            $.post('<?php echo U("His/index/getMenuByPid");?>',{'pid':id},function (data) {
                     $.each(data , function (key,val) {
                         html += '<li class="li-menu">';
                         html +='<a href="/index.php/'+val.menu_name +'"'+ "class='menu'  target='main'>"+"<i class='"+val.icon+"'></i> <span>"+val.title+"</span></a>";
                         html += "</li>";
                     });
                $('#menu-nav').append(html);

                var item = $('#menu-nav').find('.menu:first');//.attr('href');
                item.addClass('active');
                pageLoad();
                $("#main").attr('src', item.attr('href'));
            })

        });
        if(isHospital==1){
             $(".dzm-menu").eq(3).trigger('click');
        }else{
            $(".dzm-menu").first().trigger("click");
        }

        //左侧导航
        $(document).on('click','#menu-nav li a',function (data) {

            pageLoad();
            $(this).addClass('active').closest('li').siblings('li').find('a').removeClass('active');
        })
        //修改密码弹框
        $(document).on('click', '.revisePwd', function () {

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


    });

    //iframe高度自适应
    function reinitIframe(){
        var iframe = document.getElementById("main");
        try{
            var bHeight = iframe.contentWindow.document.body.scrollHeight;
            var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
            var height = Math.max(bHeight, dHeight);
            iframe.height = height;
            //console.log(height);
        }catch (ex){}
    }
    //window.setInterval("reinitIframe()", 200);
  function pageLoad() {
      load_idx = layer.load(2,{shade: [0.65, '#FFF']});
  }
  function endLoad() {
        layer.close(load_idx);
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