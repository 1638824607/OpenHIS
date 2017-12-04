// 询问弹框
function promptBox(text, fn) {
    html = '<div class="bombBox" id="promptBox">\
            <div class="promptBox bombContent">\
                <p class="promptTxt">'+ text +'</P>\
                <div class="promptBtn">\
                    <a class="btn3 btn btn-primary determine">确定</a>\
                    <a class="btn3 btn btn-default cancel">取消</a>\
                </div>\
            </div>\
            <a><div class="bombMask"></div></a>\
        </div>';
    $('body').append(html).css('overflow', 'hidden');
    // 点击确定按钮
    $('.determine').one('click', function(event) {
        if (typeof(fn) == "function") {
            fn();
        };
        $(this).closest('.bombBox').remove();
    });
    // 取消或者关闭
    $('.determine, .cancel, .bombBox .bombMask').one('click', function(event) {
        $(this).closest('#promptBox').remove();
        $('body').removeAttr('style');
    });
}
// 询问输入弹框
function promptInputBox(tit,txt, fn) {
    var title = tit;
    var text = txt;
    if (!tit) {
        title = '输入内容';
    }
    if (!txt) {
        text = '';
    }

    html = '<div class="bombBox" id="promptInputBox">\
            <div class="promptBox bombContent">\
                <div class="bombTit">' + title + '</div>\
                <textarea class="bombTextarea" rows="5">'+ text +'</textarea>\
                <div class="promptBtn">\
                    <a class="btn3 btn btn-primary determine">确定</a>\
                    <a class="btn3 btn btn-default cancel">取消</a>\
                </div>\
            </div>\
            <a><div class="bombMask"></div></a>\
        </div>';
    $('body').append(html).css('overflow', 'hidden');
    // 点击确定按钮
    $('.determine').one('click', function(event) {
        $(this).closest('.bombBox').hide();
        if (typeof(fn) == "function") {
            fn();
        };
        $(this).closest('.bombBox').remove();
    });
    // 取消或者关闭
    $('#promptInputBox .determine, #promptInputBox .cancel, #promptInputBox .bombMask').one('click', function(event) {
        $(this).closest('#promptInputBox').remove();
        $('body').removeAttr('style');
    });
}
// 图片查看弹框
function pictureBox(url) {
	html = '<div class="bombBox" id="pictureBox">\
        <div class="pictureBox bombContent">\
            <img src="'+ url +'">\
        </div>\
        <a><div class="bombMask"></div></a>\
    </div>';
	$('body').append(html).css('overflow', 'hidden');
    // 取消或者关闭
    $('#pictureBox *').one('click', function(event) {
        $(this).closest('#pictureBox').remove();
        $('body').removeAttr('style');
    });
}
// 提示弹框
function remindBox(text) {
    html = '<div class="bombBox" id="remindBomb">\
        <div class="remindBox">'+ text +'</div></div>';
    $('body').append(html);
    setTimeout(function () {
        $('#remindBomb').animate({opacity: '0'},1000);
    },1500);
    setTimeout(function () {
        $('#remindBomb').remove();
    },2500);
}
// 加载弹框
function loadBox(text) {
    html = '<div class="bombBox" id="loadingBomb">\
    <div class="bombContent loadingBox pd10 ftc fz18" style=" padding-bottom:90px;">'+ text +'</div>\
        <div class="bombMask whiteBg ">'+  +'</div>\
    </div>';
    $('body').append(html);
}

function closeLoadBox() {
    $('#loadingBomb').remove();
}

// 时间格式过滤
function timeToDate(time) {
    var y = time.getFullYear(),
        m = time.getMonth() + 1,
        d = time.getDate();
    return y + "-" + (m < 10 ? "0" + m : m) + "-" + (d < 10 ? "0" + d : d) + " " + time.toTimeString().substr(0, 8);
}