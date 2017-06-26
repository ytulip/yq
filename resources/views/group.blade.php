<!DOCTYPE HTML>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/weui.css?v={{env('VERSION')}}"/>
<link rel="stylesheet" href="/css/style.css?v={{env('VERSION')}}"/>
<title></title>
<style>
    .charge-show{background-color: #ffffff;padding: 4px;}
</style>
<body>
<div class="weui-tab">
    <div class="weui-navbar">
        <div class="weui-navbar__item weui-bar__item_on">
            一级会员({{count($parent)}})
        </div>
        <div class="weui-navbar__item">
            二级会员({{count($indirect)}})
        </div>
        <div class="weui-navbar__item">
            三级会员({{count($further)}})
        </div>
    </div>
    <div class="weui-tab__panel">

    </div>
</div>

</div>
</body>
</html>