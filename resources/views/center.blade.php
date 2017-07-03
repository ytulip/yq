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
<div class="container" style="background-color: #eeeeee">
    <div class="weui-tab">
        <p style="line-height: 36px;background-color:#37b3d9;color:#ffffff;padding-left: 26px;font-size: 14px; ">
            ID:&nbsp;&nbsp;{{\Illuminate\Support\Facades\Auth::id()}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;上级:&nbsp;&nbsp;{{\Illuminate\Support\Facades\Auth::user()->parent_id}}
        </p>
        <div class="weui-navbar" style="margin-top: 36px;">
            <div class="weui-navbar__item">
                余额<br/>
                {{\Illuminate\Support\Facades\Auth::user()->charge}}
            </div>
            <div class="weui-navbar__item">
                提成收益<br/>
                {{\Illuminate\Support\Facades\Auth::user()->extract}}
            </div>
            <div class="weui-navbar__item" onclick="location.href='/withdraw'">
                <a>提现</a><br/>
            </div>
        </div>
    </div>

    <div class="page-center">
        <a class="weui-cell weui-cell_access js_item" data-id="progress" href="/group" style="background-color: #ffffff;">
            <div class="weui-cell__bd">
                <p>我的下级会员</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access js_item" data-id="progress" href="/deck/card.html" style="background-color: #ffffff;">
            <div class="weui-cell__bd">
                <p>游戏</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
    </div>
    @include('tabbar')</div>
</body>
</html>