<?php

namespace App\Http\Controllers;

use App\Facades\WechatCallbackFacade;
use App\Log\src\Logger;
use App\Model\Account;
use App\Model\Charge;
use App\Model\LotteryConfig;
use App\Model\User;
use App\Util\QrCodeCreater;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Ytulip\Ycurl\Kits;

class IndexController extends Controller
{
    public function showLottery()
    {
        if(!Auth::check()) {
            $openid = WechatCallbackFacade::getOpenidInThisUrlDealWithError(function () {
                App::abort('404');
            });
            //如果没有用户则创建用户
            $user = User::where('openid',$openid)->first();
            if(!$user) {
                $user = new User();
                $user->openid = $openid;
                $user->name = $openid;
                $user->save();
            }
            Auth::loginUsingId($user->id);
        }


        return view('lottery');
    }

    public function userInfo()
    {
        if(!Auth::check())
        {
            return json_encode(['status'=>false,'error'=>'用户信息丢失']);
        }

        $user = User::find(Auth::id());
        return json_encode(['status'=>true,'data'=>$user->toArray()]);
    }

    public function doLottery()
    {
        $rules = [
            'lottery_id'=>'required|exists:lottery_config,id'
        ];
        $currentDatetime = date('Y-m-d H:i:s');
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
            ['cash'=>$lottery->cost,'user_id'=>Auth::id(),'type'=>2,'category'=>3,'created_at'=>$currentDatetime,'updated_at'=>$currentDatetime],
            ['cash'=>$bingo->price,'user_id'=>Auth::id(),'type'=>1,'category'=>4,'created_at'=>$currentDatetime,'updated_at'=>$currentDatetime],
        ]);

        return json_encode(['status'=>true,'data'=>['charge'=>User::find(Auth::id())->charge,'bingo'=>$cursor,'bingo_cash'=>$bingo->price]]);
    }

    private function loginByOpenId()
    {
    }

    public function login()
    {
        if(App::environment('local')) {
            Auth::loginUsingId(Request::get('id'));
            return 123;
        }
    }

    public function loginout()
    {
        Session::flush();
        return 456;
    }

    /**
     * 微信充值回调
     */
    public function wechatPayBack()
    {

        Log::info(Request::getContent());
        $request = Request::getContent();
        $wxObj = simplexml_load_string($request, 'SimpleXMLElement', LIBXML_NOCDATA);

        if( $wxObj->result_code != "SUCCESS"){
            return;
        }

        $tradeNo = $wxObj->out_trade_no;
        $currentDatetime = date('Y-m-d H:i:s');
        //计算提成
        //$tradeNo = Request::input('trade_no');
        $charge = Charge::find($tradeNo);
        if($charge && $charge->status == 1)
        {
            return "SUCCESS";
        }

        $charge->status = 1;
        $charge->save();
        $user = User::find($charge->user_id);
        $user->increment('charge',$charge->price);
        $accountArr[] = ['cash'=>$charge->price,'user_id'=>Auth::id(),'type'=>1,'category'=>1,'created_at'=>$currentDatetime,'updated_at'=>$currentDatetime,'remark'=>$charge->id];;
        if($user->parent_id)
        {
            $parent = User::find($user->parent_id);
            $parent->increment('charge',$charge->price * 0.1);
            $parent->increment('extract',$charge->price * 0.1);
            $accountArr[] =  ['cash'=>$charge->price * 0.1,'user_id'=>Auth::id(),'type'=>1,'category'=>2,'created_at'=>$currentDatetime,'updated_at'=>$currentDatetime,'remark'=>$charge->id];
        }

        if($user->indrect_id)
        {
            $parent = User::find($user->indrect_id);
            $parent->increment('charge',$charge->price * 0.02);
            $parent->increment('extract',$charge->price * 0.02);
            $accountArr[] =  ['cash'=>$charge->price * 0.02,'user_id'=>Auth::id(),'type'=>1,'category'=>2,'created_at'=>$currentDatetime,'updated_at'=>$currentDatetime,'remark'=>$charge->id];
        }

        if($user->further_id)
        {
            $parent = User::find($user->further_id);
            $parent->increment('charge',$charge->price * 0.004);
            $parent->increment('extract',$charge->price * 0.004);
            $accountArr[] =  ['cash'=>$charge->price * 0.004,'user_id'=>Auth::id(),'type'=>1,'category'=>2,'created_at'=>$currentDatetime,'updated_at'=>$currentDatetime,'remark'=>$charge->id];
        }

        if($accountArr)
        {
            Account::insert($accountArr);
        }

        return "SUCCESS";
    }

    public function myFriend()
    {
        $userId = Request::input('id');

        $openId = WechatCallbackFacade::getOpenidInThisUrlDealWithError(function(){
            dd('无法获得用户信息！');
        });
        $user = User::where(['openid'=>$openId])->first();
        if($user){
            dd('用户已注册');
        }

        $referrer = User::find($userId);
        if(!$referrer) {
            dd('无效链接！');
        }

        $newUser = new User();
        $newUser->openid = $openId;
        $newUser->parent_id = $referrer->id;
        $newUser->indirect_id = $referrer->parent_id;
        $newUser->further_id = $referrer->indirect_id;
        $newUser->save();
        return view('callfriendsuccess');
    }

    public function callFriend()
    {
        $userId = Auth::id();
        $path = $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'] . "/myfriend?id=" . $userId;
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

    public function makeBill()
    {
        $pirce = intval(Request::input('price'));
        if(!in_array($pirce,[5,10,50,100])){
            dd('无效金额');
        }

        if(!Auth::check()) {
            dd('登录信息丢失');
        }
        $charge = new Charge();
        $charge->user_id = Auth::id();
        $charge->price = $pirce;
        $charge->save();
        return Redirect::to('/pay?trade_no=' . $charge->id);
    }


    public function makeWithdraw()
    {
        if(!Auth::check()) {
            return json_encode(['status'=>false,'data'=>'登录信息丢失!']);
        }

        $pirce = intval(Request::input('price'));
        if(!($pirce > 0  && (Auth::user()->charge  > $pirce) && $pirce <= 200))
        {
            return json_encode(['status'=>false,'data'=>'无效金额!']);
        }

        DB::beginTransaction();

        $user = User::find(Auth::id());
        $user->decrement('charge',$pirce);

        $account = new Account();
        $account->cash = $pirce;
        $account->type = 2;
        $account->user_id = Auth::id();
        $account->category = 2;
        $account->save();

        $charge = new Charge();
        $charge->user_id = Auth::id();
        $charge->price = $pirce;
        $charge->type = 2;
        $charge->save();

        //TODO:发送微信红包
        $data = [];
        $data['nonce_str']=WechatCallbackFacade::rand_num();
        $data['mch_id']= env('MECHID');//商户号
        $data['mch_billno']= $charge->id;
        $data['wxappid']=env('WECHAT_APPID');//公众账号appid
        $data['send_name']='匿名';
        $data['re_openid']= $user->openid;//用户openid
        $data['total_amount']=Kits::wxFee($charge->price);
        $data['total_num']=1;
        $data['wishing']='提现';
        $data['client_ip']= env('RED_ENVELOPE_IP');
        $data['act_name']='提现功能';
        $data['remark']='速收';
        $key=env('MECHKEY');
        $data['sign'] = WechatCallbackFacade::getSign($data,$key);
        $res = $this->cashBonus($data);
        $res = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);

        if(isset($res->result_code) && $res->result_code == "SUCCESS" ) {
            DB::commit();
            return json_encode(['status'=>true,'data'=>"申请提现" . $pirce]);
        } else {
            DB::rollback();
            return json_encode(['status'=>false,'data'=>"申请异常"]);
        }

//        if(!in_array($pirce,[5,10,50,100])){
//            dd('无效金额');
//        }
//
//        if(!Auth::check()) {
//            dd('登录信息丢失');
//        }
//        $charge = new Charge();
//        $charge->user_id = Auth::id();
//        $charge->price = $pirce;
//        $charge->save();
//        return Redirect::to('/pay?trade_no=' . $charge->id);
    }

    public function pay()
    {
        $tradeNo = Request::get('trade_no');
        $charge = Charge::find($tradeNo);
        if($charge->status)
        {
            dd('请勿重复支付');
        }

        $openId = Auth::user()->openid;
        if($openId){
            require_once base_path() . "/plugin/wechatpay/lib/WxPay.Api.php";
            require_once base_path() . "/plugin/wechatpay/example/WxPay.JsApiPay.php";
            $tools = new \JsApiPay();
            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("充值金币" . intval($charge->price) ."个");
            $input->SetAttach("充值金币" . intval($charge->price) ."个");
            $input->SetOut_trade_no($tradeNo);//这个订单号是特殊的
            $input->SetTotal_fee(Kits::wxFee($charge->price)); //钱是以分计的
            $input->SetTime_start(date("YmdHis"));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://yq.zhuyan.me/back");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = \WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
        }else{
            $jsApiParameters = '{}';
        }
        return view('pay')->with('jsApiParameters',$jsApiParameters);
    }

    public function doCard()
    {
        $this->validate(Request::all(),['money'=>'min:0']);

        if( !Auth::check() )
        {
            return json_encode(['status'=>false,'data'=>'登录信息丢失']);
        }

        $user = User::find(Auth::id());
        $charge = $user->charge;
        $money = Request::input('money');
        $user->charge = $money;
        $user->save();
        $account = new Account();
        $diff = ($charge - $money);
        if(($diff = ($charge - $money)) < 0)
        {
            $account->type = 1;
        } else {
            $account->type = 2;
        }

        $account->cash = abs($charge - $money);
        $account->user_id = Auth::id();
        $account->category = 5;
        $account->remark = $charge . ':' .$money;
        $account->save();
        return json_encode(['status'=>true,'data'=>$user->charge]);
    }

    public function withdraw()
    {
        return view('withdraw');
    }

    public function setMenu()
    {
        WechatCallbackFacade::setMenu();
    }

    //现金红包接口
    private function cashBonus($data){
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
        $xml = "<xml>
                <sign><![CDATA[%s]]></sign>
                <mch_billno><![CDATA[%s]]></mch_billno>
                <mch_id><![CDATA[%s]]></mch_id>
                <wxappid><![CDATA[%s]]></wxappid>
                <send_name><![CDATA[%s]]></send_name>
                <re_openid><![CDATA[%s]]></re_openid>
                <total_amount><![CDATA[%s]]></total_amount>
                <total_num><![CDATA[%s]]></total_num>
                <wishing><![CDATA[%s]]></wishing>
                <client_ip><![CDATA[%s]]></client_ip>
                <act_name><![CDATA[%s]]></act_name>
                <remark><![CDATA[%s]]></remark>
                <nonce_str><![CDATA[%s]]></nonce_str>
                </xml>";
        $result = sprintf($xml,$data['sign'],$data['mch_billno'],$data['mch_id'],$data['wxappid'],$data['send_name'],$data['re_openid'],$data['total_amount'],$data['total_num'],$data['wishing'],$data['client_ip'],$data['act_name'],$data['remark'],$data['nonce_str']);
        Log::info($url.'参数:'.$result);
        $res = WechatCallbackFacade::curl_post_ssl($url,$result);
        Log::info('响应：'.$res);
        return $res;
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
