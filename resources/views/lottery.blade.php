<!DOCTYPE HTML>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/weui.css?v={{env('VERSION')}}"/>
<link rel="stylesheet" href="/css/style.css?v={{env('VERSION')}}"/>
<title></title>
<style>
    .weui-navbar__item.weui-bar__item_on{background-color: #37b3d9;padding: 0;}
    .lottery_disable{background-color: #9ED99D !important;}
</style>
<body>
<div class="container">
    <div class="weui-navbar" style="height: 44px;">
        <div class="weui-navbar__item weui-bar__item_on">
           <p style="line-height: 44px;color: #ffffff;"><span>ID:&nbsp;&nbsp;{{\Illuminate\Support\Facades\Auth::id()}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            余额:<span id="user-charge">{{\Illuminate\Support\Facades\Auth::user()->charge}}</span>

            <a  style="vertical-align: middle;margin-left: 60px;" href="/charge" class="weui-btn weui-btn_mini weui-btn_warn">充值</a>
            </p>
        </div>
    </div>
    <div class="lottery-wrap">
        <div class="g-lottery-box">
            <div class="g-lottery-img">
                <a class="playbtn" href="javascript:;" title="开始抽奖"></a>
            </div>
        </div>
        <div class="" style="text-align: center;" id="lottery-level">
        @foreach(\Illuminate\Support\Facades\DB::table('lottery_config')->select(['id','remark'])->get() as $key=>$item)
            <a href="javascript:changeLottery({{$key}});"  class="weui-btn weui-btn_mini weui-btn_primary @if($key==0) lottery_disable @endif">{{$item->remark}}</a>
            @endforeach
        </div>
    </div>
    @include('tabbar')
</div>
</body>
<script src="js/jquery-1.8.3.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="js/jquery.rotate.min.js"></script>
<script>

    var pageConfig = {
        cash:{{\Illuminate\Support\Facades\Auth::user()->charge}},
        lotteryConfig:{!!\Illuminate\Support\Facades\DB::table('lottery_config')->select(['id','cost'])->get()!!}
    }

    var currentLotteryIndex = 0;
    var lotteryRequestStatus = 0;//0请求完成，1请求中
    {{--var lotteryConfig = {{}};--}}

    function changeLottery(key) {
        if(lotteryRequestStatus || (key == currentLotteryIndex))
        {
            return;
        }
        //换背景
        currentLotteryIndex = key;
        $('.lottery_disable').removeClass('lottery_disable');
        $('#lottery-level a:nth-child('+ (key + 1) + ')').addClass('lottery_disable');
        $('.g-lottery-img').css({
            "background":"url(/img/lottery"+key+".png) no-repeat",
            "backgroundSize":"255px 255px"
        });
    }

    $(function() {
        var $btn = $('.playbtn');


        $btn.click(function() {
            if(lotteryRequestStatus) return; // 如果在执行就退出
            lotteryRequestStatus = true; // 标志为 在执行

            if(pageConfig.cash - pageConfig.lotteryConfig[currentLotteryIndex].cost < 0) { //当抽奖次数为0的时候执行
                alert("余额不足！");
                lotteryRequestStatus = false;
            } else { //还有次数就执行
                lotteryRequestData = false;
                roateLottery();
                //发送抽奖请求
                $.post('/activity-do',{lottery_id:pageConfig.lotteryConfig[currentLotteryIndex].id},function(data){
                        lotteryRequestData =  data;
                        //lotteryRequestStatus = 0;
                },'json').error(function(){
                    lotteryRequestData = {status:false,data:"网络异常！"}
                    //lotteryRequestStatus = 0;
                });
            }
        });


        function roateLottery() {
            if(lotteryRequestData){
                if(!lotteryRequestData.status){
                    alert(lotteryRequestData.data);
                    lotteryRequestStatus = 0;
                    return;
                }

                $btn.rotate({
                    angle: 0,
                    animateTo: (lotteryRequestData.data.bingo * 60) + 30,
                    callback: function () {
                        alert("恭喜您抽中" + lotteryRequestData.data.bingo_cash + '币');
                        pageConfig.cash = lotteryRequestData.data.charge;
                        document.querySelector('#user-charge').innerHTML = pageConfig.cash;
                        $btn.rotate({
                            angle: 0,
                            animateTo: 0,
                            callback:function(){
                            }
                        });
                        lotteryRequestStatus = 0;
                    }
                });

                return;
            }

            $btn.rotate({
                angle: 0,
                //duration: 4000, //旋转时间
                animateTo: 360, //让它根据得出来的结果加上1440度旋转
                callback: function () {
                    roateLottery();
                }
            });}

    });
</script>
</html>