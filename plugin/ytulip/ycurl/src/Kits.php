<?php
namespace Ytulip\Ycurl;
class Kits{
    /**
     * @param $dateStr
     * @return days
     */
    static public function daysToNow($dateStr){
        $second1 = strtotime(date('Y-m-d'));
        $second2 = strtotime($dateStr);
        return ($second1 - $second2) / 86400;
    }

    /**
     * 微信的钱是以分为单位的，实际在系统中的钱是以元为单位的，返回一个整数
     * @param $fee
     * @return float
     */
    static public function wxFee($fee){
        $flag = env('WECHAT_PAY_TEST',false);
        if($flag){
            return 1;
        }else{
            $fee = $fee * 100;
            return round($fee);
        }
    }

    static public function padId($id)
    {
        return str_pad($id,9,0,STR_PAD_LEFT);
    }
}