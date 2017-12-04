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
    <!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container-fluid">
        <div class="panel clearfix pd10 mb20">
            <div class="fublBox mr10"><span>收费类型：</span>
                <select class="form-control form-itmeB" name="op_place">
                    <option value="">全部</option>
                    <option value="1">售药</option>
                    <option value="2">检查项目</option>
                    <option value="3">附加费用</option>
                    <option value="4">挂号</option>
                </select>
            </div>
            <div class="fublBox mr10"><span>收费状态：</span>
                <select class="form-control form-itmeB" name="status">
                    <option value="">全部</option>
                    <option value="0">未支付</option>
                    <option value="1">已支付</option>
                    <option value="4">已退款</option>
                </select>
            </div>
            <div class="fublBox mr10">
                <span>日期：</span>
                <input type="text" class="form-control form-itmeB dateTime startTime" name="starttime" placeholder=""><i
                    class="fa fa-calendar"></i>
            </div>
            <div class="fublBox mr10"><span class="mr10">-</span><input type="text"
                                                                        class="form-control form-itmeB dateTime endTime"
                                                                        name="endtime"
                                                                        placeholder=""><i class="fa fa-calendar"></i>
            </div>
            <button type="button" class="btn btn-primary search">查询</button>
            <button type="button"  class="btn btn-primary r export">导出</button>
        </div>

        <div class="row mb20">
            <div class="col-md-2 ftc flh64">
                <div class="yelloBg pd10 white">
                    <div class="height64 generalincome">0.00</div>
                    <div class="bwt height64 ">合计收入</div>
                </div>
            </div>
            <div class="col-md-10 ftc white">
                <div class="">
                    <div class="blueBg2 clearfix pdrl10 m_h70">
                        <div class="flh70 l mr60">
                            收支概况
                        </div>
                        <div class="flh34 l mr60">
                            <div class="chargeincome">0.00</div>
                            <div class="bwt">收费金额</div>
                        </div>
                        <div class="flh34 l mr60">
                            <div class="refundincome">0.00</div>
                            <div class="bwt">退费金额</div>
                        </div>
                    </div>
                    <div class="greenBg clearfix pdrl10 m_h70 mt8">
                        <div class="flh70 l mr60">
                            收入渠道
                        </div>
                        <div class="flh34 l mr60">
                            <div class="cashincome">0.00</div>
                            <div class="bwt">现金收入</div>
                        </div>
                        <div class="flh34 l mr60">
                            <div class="wechartincome">0.00</div>
                            <div class="bwt">微信收入</div>
                        </div>
                        <div class="flh34 l mr60">
                            <div class="alipayincome">0.00</div>
                            <div class="bwt">支付宝收入</div>
                        </div>
                        <div class="flh34 l mr60">
                            <div class="cashrefund">0.00</div>
                            <div class="bwt">现金退款</div>
                        </div>
                        <div class="flh34 l mr60">
                            <div class="wechartrefund">0.00</div>
                            <div class="bwt">微信退款</div>
                        </div>
                        <div class="flh34 l mr60">
                            <div class="alipayrefund">0.00</div>
                            <div class="bwt">支付宝退款</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel clearfix pd10">
            <div class="wb30 l">
                <div id="szEchar" class="mauto"></div>
            </div>
            <div class="wb30 l">
                <div id="srEchar" class="mauto"></div>
            </div>
            <div class="wb30 l">
                <div id="reEchar" class="mauto"></div>
            </div>
            <div>
                <table class="table table-striped ftc mt10">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>类型</th>
                        <th>状态</th>
                        <th>姓名</th>
                        <th>年龄</th>
                        <th>应收</th>
                        <th>现金</th>
                        <th>微信</th>
                        <th>支付宝</th>
                        <th>日期</th>
                        <th>收费员</th>
                    </tr>
                    </thead>
                    <tbody class="imcomestat_list_box">

                    </tbody>
                </table>
                <div class="paging mt20 mb20 ftc imcomestat_page_box">

                </div>
            </div>
        </div>

    </div>
</div>
<!-- END MAIN CONTENT -->
<script src="/Public/his/js/echarts.min.js"></script>
<script>
    var d = new Date();
    var today = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
    $('.dateTime').datetimepicker({
        lang: 'ch',
        timepicker: false,
        format: 'Y-m-d',
        validateOnBlur: false,
        maxDate:today
    });
    $(function () {
        var _incomestat_page = 1, _pagesize = 5;
        var op_place = '', status = '', starttime = '', endtime = '';
        //选项卡切换
        $(document).on('click', '.tabBtn > li', function () {
            $(this).addClass('on').siblings('li').removeClass('on').closest('.tabBtn');
            $(this).closest('.tabBtn').siblings('.tabBox').find('> li').eq($(this).index()).addClass('on').siblings('li').removeClass('on');
        });
        getIncomeList(_incomestat_page, _pagesize, op_place, status, starttime, endtime);
        getIncomeInfo(op_place, status, starttime, endtime);

        //获取统计信息
        function getIncomeInfo(op_place, status, starttime, endtime) {
            $.post("<?php echo U('/IncomeStat/getIncomeInfo');?>", {
                op_place: op_place,
                status: status,
                starttime: starttime,
                endtime: endtime
            }, function (e) {
                $('.generalincome').text(e.data.generalincome);
                $('.chargeincome').text(e.data.general_situation.chargeincome);
                $('.refundincome').text(e.data.general_situation.refundincome);
                $('.alipayincome').text(e.data.channel.alipayincome);
                $('.cashincome').text(e.data.channel.cashincome);
                $('.wechartincome').text(e.data.channel.wechartincome);
                $('.alipayrefund').text(e.data.channel.alipayrefundincome);
                $('.cashrefund').text(e.data.channel.cashrefundincome);
                $('.wechartrefund').text(e.data.channel.wechartrefundincome);
                if(e.data.general_situation.chargeincome || e.data.general_situation.refundincome || e.data.channel.cashincome || e.data.channel.wechartincome || e.data.channel.alipayincome || e.data.channel.cashrefundincome || e.data.channel.wechartrefundincome || e.data.channel.alipayrefundincome){
                    var szData = {
                        id: 'szEchar',
                        title: '收支概况',
                        classData: ['收费金额', '退费金额'],
                        conData: [
                            {value: e.data.general_situation.chargeincome, name: '收费金额'},
                            {value: e.data.general_situation.refundincome, name: '退费金额'}
                        ]
                    }
                    var srData = {
                        id: 'srEchar',
                        title: '收入渠道',
                        classData: ['现金收入', '微信收入', '支付宝收入'],
                        conData: [
                            {value: e.data.channel.cashincome, name: '现金收入'},
                            {value: e.data.channel.wechartincome, name: '微信收入'},
                            {value: e.data.channel.alipayincome, name: '支付宝收入'}
                        ]
                    }
                    var reData = {
                        id: 'reEchar',
                        title: '退款',
                        classData: ['现金退款', '微信退款', '支付宝退款'],
                        conData: [
                            {value: e.data.channel.cashrefundincome, name: '现金退款'},
                            {value: e.data.channel.wechartrefundincome, name: '微信退款'},
                            {value: e.data.channel.alipayrefundincome, name: '支付宝退款'}
                        ]
                    }
                    pieChart(szData);
                    pieChart(srData);
                    pieChart(reData);
                }
            }, 'json')
        }

        //获取列表信息
        function getIncomeList(page, pagesize, op_place, status, starttime, endtime) {
            $.post("<?php echo U('/IncomeStat/getIncomeList');?>", {
                        p: page,
                        pagesize: pagesize,
                        op_place: op_place,
                        status: status,
                        starttime: starttime,
                        endtime: endtime
                    },
                    function (e) {
                        if (e.status == 'success') {
                            if (e.data.list.length > 0) {
                                var str = '';
                                $.each(e.data.list, function (i, n) {
                                    str += " <tr><td>" + (Number(i) + 1) + "</td>";
                                    if (n.op_place == 1) {
                                        str += "<td>售药</td>";
                                    } else if (n.op_place == 2) {
                                        str += "<td>检查项目</td>";
                                    } else if (n.op_place == 3) {
                                        str += "<td>附加费用</td>";
                                    } else if (n.op_place == 4) {
                                        str += "<td>挂号</td>";
                                    }else {
                                        str += "<td>未知</td>";
                                    }

                                    if (n.status == 1) {
                                        str += "<td>已收费</td>";
                                    } else if (n.status == 0) {
                                        str += "<td>未支付</td>";
                                    } else if (n.status == 2) {
                                        str += "<td>确认收款</td>";
                                    } else if (n.status == 3) {
                                        str += "<td>申请退款</td>";
                                    }else if (n.status == 4) {
                                        str += "<td>已退款</td>";
                                    }else if (n.status == 5) {
                                        str += "<td>部分支付</td>";
                                    }else if (n.status == 6) {
                                        str += "<td>完成交易</td>";
                                    }else if (n.status == 7) {
                                        str += "<td>部分退款</td>";
                                    }
                                    str += "<td>" + n.name + "</td>";
                                    str += "<td>" + n.age + "</td>";
                                    str += "<td>" + n.amount + "</td>";
                                    str += "<td>" + n.xianjin + "</td>";
                                    str += "<td>" + n.wechart + "</td>";
                                    str += "<td>" + n.zhifubao + "</td>";
                                    str += "<td>" + n.addtime + "</td>";
                                    str += "<td>" + n.user_name + "</td>";
                                    str += "</tr>";
                                })
                                _incomestat_page = e.data.page;
                                $('.imcomestat_list_box').html(str);
                                if (e.data.pager_str.length > 0) {
                                    $('.imcomestat_page_box').html(e.data.pager_str);
                                } else {
                                    $('.imcomestat_page_box').html('');
                                }
                            } else {
                                $(".imcomestat_list_box").html('<tr><td colspan="11" height="30" align="center" class="f_red" >暂无数据</td></tr>');
                                $('.imcomestat_page_box').html('');
                            }
                        } else {
                            remindBox(e.msg);
                        }
                    }, 'json')
        }

        //列表分页
        $(".imcomestat_page_box").on('click', '.item', function () {
            var tag = $(this)[0].tagName.toLowerCase();
            if (tag == 'i') {
                if ($(this).hasClass('next')) {
                    _incomestat_page = parseInt(_incomestat_page) + 1;
                } else {
                    _incomestat_page = parseInt(_incomestat_page) - 1;
                }
            } else {
                _incomestat_page = parseInt($(this).html());
            }
            getIncomeList(_incomestat_page, _pagesize, op_place, status, starttime, endtime);
        });
        //搜索
        $(document).on("click", '.search', function () {
            op_place = $("select[name='op_place'] option:selected").val();
            status = $("select[name=status] option:selected").val();
            starttime = $("input[name='starttime']").val();
            endtime = $("input[name='endtime']").val();
            if (op_place || status || starttime || endtime) {
                _incomestat_page = 1;
            }
            getIncomeList(_incomestat_page, _pagesize, op_place, status, starttime, endtime);
            getIncomeInfo(op_place, status, starttime, endtime);
        })
        //导出
        $(document).on('click', '.export', function () {
            op_place = $("select[name='op_place'] option:selected").val();
            status = $("select[name=status] option:selected").val();
            starttime = $("input[name='starttime']").val();
            endtime = $("input[name='endtime']").val();
            str = "<?php echo U('/IncomeStat/export');?>/p/" + _incomestat_page + "/pagesize/" + _pagesize;
            if(op_place !== ''){
                str += "/op_place/"+op_place;
            }
            if(status !== ''){
                str += "/status/"+status;
            }
            if(starttime !== '' || endtime !== ''){
                if(starttime !== '' && endtime !== ''){
                    str += "/starttime/"+starttime+"/endtime/"+endtime;
                }else{
                    remindBox('开始时间和结束时间必须同时选择');return false;
                }
            }
            window.location.href = str;
        })

        /*饼状图*/
        function pieChart(data) {
            var dom = document.getElementById(data.id);
            var myChart = echarts.init(dom);
            var option = {
                title: {
                    text: data.title,
                    left: 'center',
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    y: 30,
                    data: data.classData
                },
                series: [
                    {
                        name: data.title,
                        type: 'pie',
                        radius: ['40%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: false
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: data.conData
                    }
                ]
            };
            myChart.resize({height: 300, width: 520});
            myChart.setOption(option, true);
        }

    });

</script>
<!-- END WRAPPER -->

<script type="text/javascript">
    if(parent.endLoad){
        parent.endLoad();
    }
</script>
</body>
</html>