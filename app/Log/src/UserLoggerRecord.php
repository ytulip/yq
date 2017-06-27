<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/9
 * Time: 15:37
 */

namespace App\Log\src;


use App\Log\Facades\Logger;
use App\Model\Base\UsersLogModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Request;

class UserLoggerRecord
{
    private $usersLogModel;
    public function __construct(){
        $this->usersLogModel = new UsersLogModel();
    }
    public function record(){
        if(Auth::check()){
            $user_id = Auth::id();
            $user_name = Auth::user()->realname;
        }else{
            $user_id = '';
            $user_name = '';
        }
        $service_ip = $this->get_client_ip();
 //       $service_ip = '202.105.104.82';
        $action = Request::url();
        $created_at = date('Y-m-d H:i:s',time());
        $updated_at = date('Y-m-d H:i:s',time());
        $type = isset ($_SERVER['HTTP_X_WAP_PROFILE'])?'WX':'PC';
        $log = array(
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'service_ip'=>$service_ip,
            'action'=>$action,
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'type'=>$type,
      //      'address' =>$this->get_ip_address($service_ip),
        );
        $this->usersLogModel->insert_log($log);

    }

    function get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
    //根据IP获取城市
    function get_ip_address($queryIP){
        $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1='.$queryIP;
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        $result = curl_exec($ch);
        $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
        curl_close($ch);
        preg_match("@<span>(.*)</span></p>@iU",$result,$ipArray);
        $loc = $ipArray[1];
        return $loc;
    }
}