<!DOCTYPE HTML>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/weui.css?v={{env('VERSION')}}"/>
<link rel="stylesheet" href="/css/style.css?v={{env('VERSION')}}"/>
<title></title>
<style>
    .charge-show{background-color: #ffffff;padding: 4px;}
    .cash_disable{background-color: #9ED99D !important;}
</style>
<body>
<div class="container" style="padding: 6px;background-color: #eeeeee;box-sizing: border-box;">
    <div class="charge-show">
        <p>账户余额</p>
        <p>￥<span>{{\Illuminate\Support\Facades\Auth::user()->charge}}</span>元</p>
    </div>

    <div class="weui-cell" style="background-color: #ffffff;margin-top: 10px;margin-bottom: 10px;">
        <div class="weui-cell__hd"><label class="weui-label">提现金额</label></div>
        <div class="weui-cell__bd">
            <input class="weui-input" type="number" id="price" pattern="\d*" placeholder="只能输入不超过200的整数">
        </div>
    </div>

    <div><a href="javascript:void(0);" class="weui-btn weui-btn_primary" id="make_withdraw">确认</a></div>


</div>
@include('tabbar')
</body>
<script src="js/jquery-1.8.3.min.js" type="text/javascript" charset="utf-8"></script>
<script>
    $(function(){
        $('#make_withdraw').click(function(){
            $.post('/do-withdraw',{price:$('#price').val()},function(data){
                alert(data.data);
                location.reload();
                return;
            },'json').error(function(){
                alert('网络异常！');
            });
        });
    });
</script>
</html>