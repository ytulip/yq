<!DOCTYPE HTML>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/weui.css?v={{env('VERSION')}}"/>
<link rel="stylesheet" href="/css/style.css?v={{env('VERSION')}}"/>
<title></title>
<style>
    .page-center{position: absolute;top:110px;left:0;right: 0;}
    .weui-navbar__item:after{border: none;}
    .weui-navbar__item{background-color: #099ac7;color:#ffffff;}
</style>
<body>
<div class="page msg_success js_show">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">操作成功</h2>
            <p class="weui-msg__desc">您的好友已成功邀请您注册！<a href="/activity">去看看</a></p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                {{--<a href="javascript:history.back();" class="weui-btn weui-btn_primary">推荐操作</a>--}}
                {{--<a href="javascript:history.back();" class="weui-btn weui-btn_default">辅助操作</a>--}}
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    {{--<a href="javascript:void(0);" class="weui-footer__link">底部链接文本</a>--}}
                </p>
                {{--<p class="weui-footer__text">Copyright © 2008-2016 weui.io</p>--}}
            </div>
        </div>
    </div>
</div>
</body>
</html>