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

    <div id="charges">
        <a href="javascript:changeCash(5,1)" class="weui-btn weui-btn_mini weui-btn_primary cash_disable">5元</a>
        <a href="javascript:changeCash(10,2);" class="weui-btn weui-btn_mini weui-btn_primary">10元</a>
        <a href="javascript:changeCash(50,3);" class="weui-btn weui-btn_mini weui-btn_primary">50元</a>
        <a href="javascript:changeCash(100,4);" class="weui-btn weui-btn_mini weui-btn_primary">100元</a>
    </div>

    <div><a href="/makebill?price=5" class="weui-btn weui-btn_primary" id="make_bill">确认</a></div>

</div>
</body>
<script src="js/jquery-1.8.3.min.js" type="text/javascript" charset="utf-8"></script>
<script>
    function changeCash(price,nth) {
        $('.cash_disable').removeClass('cash_disable');
        $('#charges a:nth-child('+nth+')').addClass('cash_disable');
        $('#make_bill').attr('href',"/makebill?price=" + price);
    }
</script>
</html>