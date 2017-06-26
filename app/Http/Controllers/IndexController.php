<?php

namespace App\Http\Controllers;

use App\Model\Account;
use App\Model\LotteryConfig;
use App\Model\User;
use App\Util\QrCodeCreater;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class IndexController extends Controller
{
    public function showLottery()
    {
        return view('lottery');
    }

    public function doLottery()
    {
        $rules = [
            'lottery_id'=>'required|exists:lottery_config,id'
        ];
        $this->validate(Request::all(),$rules);
        $lotteryId = Request::input('lottery_id');

        if(!Auth::check())
        {
            return json_encode(['status'=>false,'data'=>'登录信息丢失！']);
        }

        $lottery = LotteryConfig::find($lotteryId);

        //尝试去扣款
        $withHold = DB::update("update users set charge = case when charge - ".$lottery->cost." >= 0 then charge - " . $lottery->cost ." else charge end where id = " . Auth::id());
        if( !$withHold ) {
            return json_encode(['status'=>false,'data'=>'余额不足,']);
        }

        //抽奖
        $lotteryConfig = json_decode($lottery->config);
        $proArr = [];
        foreach($lotteryConfig as $val)
        {
            $proArr[] = intval($val->percent * 100);
        }
        $cursor = $this->getRand($proArr);
        $bingo = $lotteryConfig[$cursor];

        $user = User::find(Auth::id());
//        var_dump($user->charge);
//        exit;
        $user->increment('charge',$bingo->price);

        Account::insert([
            ['cash'=>$lottery->cost,'user_id'=>Auth::id(),'type'=>2,'category'=>3],
            ['cash'=>$bingo->price,'user_id'=>Auth::id(),'type'=>1,'category'=>4],
        ]);

        return json_encode(['status'=>true,'data'=>['charge'=>User::find(Auth::id())->charge,'bingo'=>$cursor,'bingo_cash'=>$bingo->price]]);
    }

    private function loginByOpenId()
    {
    }

    public function login()
    {
        Auth::loginUsingId(Request::get('id'));
        return 123;
    }

    /**
     * 微信充值回调
     */
    public function wechatPayBack()
    {
        //计算提成
        
    }

    public function myFriend()
    {
        $userId = Request::input('id');

        $openId = '';
        $user = User::where(['openid'=>$openId])->first();
        if($user){
            dd('用户已注册');
        }

        $referrer = new User($userId);
        if(!$referrer) {
            dd('无效链接！');
        }

        $newUser = new User();
        $newUser->openid = $openId;
        $newUser->parent_id = $referrer->id;
        $newUser->save();
        return view('callfriendsuccess');
    }

    public function callFriend()
    {
        $userId = Auth::id();
        $path = env('HOST') . "/myfriend?id=" . $userId;
        $qrcode = QrCodeCreater::getQrCode([
            'format' => 'png',
            'issave' => false,
            'text' => $path,
            'tolerancelevel'=>'M'
        ]);
        //输出图片
        $data = [];
        $data['qrcode'] = "data:image/jpg;base64," . chunk_split(base64_encode($qrcode['data']));
        return view('friend',$data);
    }

    public function charge()
    {
        return view('charge');
    }

    public function center()
    {
        return view('center');
    }

    public function group()
    {
        $parent = DB::table('users')->where('parent_id',Auth::id())->get();
        $indirect = DB::table('users')->where('indirect_id',Auth::id())->get();
        $further = DB::table('users')->where('further_id',Auth::id())->get();
        return view('group',['parent'=>$parent,'indirect'=>$indirect,'further'=>$further]);
    }

    private function getRand($proArr) {
        $result = '';

        //概率数组的总概率精度
        $proSum = array_sum($proArr);

        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}
