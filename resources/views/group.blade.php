<!DOCTYPE HTML>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/weui.css?v={{env('VERSION')}}"/>
<link rel="stylesheet" href="/css/style.css?v={{env('VERSION')}}"/>
<title></title>
<style>
    .charge-show{background-color: #ffffff;padding: 4px;}
    .weui-cells{margin-top: 0;}
    .user-list{display: none;}
    .user-list-show{display: block;}
</style>
<body>
<div class="weui-tab">
    <div class="weui-navbar">
        <div class="weui-navbar__item weui-bar__item_on" data-nth="1">
            一级会员({{count($parent)}})
        </div>
        <div class="weui-navbar__item" data-nth="2">
            二级会员({{count($indirect)}})
        </div>
        <div class="weui-navbar__item" data-nth="3">
            三级会员({{count($further)}})
        </div>
    </div>
    <div class="weui-tab__panel">
        <div class="weui-cells">
            <div class="weui-cell user-list user-list-show">
                @foreach(\Illuminate\Support\Facades\DB::table('users')->where('parent_id',\Illuminate\Support\Facades\Auth::id())->get() as $item)
                <div class="weui-cell__hd"></div>
                <div class="weui-cell__bd">
                    <p>ID:&nbsp;&nbsp;{{$item->id}}</p>
                </div>
                    @endforeach
            </div>

            <div class="weui-cell user-list">
                @foreach(\Illuminate\Support\Facades\DB::table('users')->where('indirect_id',\Illuminate\Support\Facades\Auth::id())->get() as $item)
                    <div class="weui-cell__hd"></div>
                    <div class="weui-cell__bd">
                        <p>ID:{{$item->id}}</p>
                    </div>
                @endforeach
            </div>

            <div class="weui-cell user-list">
                @foreach(\Illuminate\Support\Facades\DB::table('users')->where('further_id',\Illuminate\Support\Facades\Auth::id())->get() as $item)
                    <div class="weui-cell__hd"></div>
                    <div class="weui-cell__bd">
                        <p>ID:{{$item->id}}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

</div>
</body>
<script src="js/jquery-1.8.3.min.js" type="text/javascript" charset="utf-8"></script>
<script>
    $(function () {
        $('.weui-navbar__item').click(function () {
            $('.weui-bar__item_on').removeClass('weui-bar__item_on');
            $(this).addClass('weui-bar__item_on');

            $('.user-list-show').removeClass('user-list-show');
            $('.user-list:nth-child('+$(this).attr('data-nth')+')').addClass('user-list-show');
        });
    });
</script>
</html>