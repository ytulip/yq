<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/4
 * Time: 14:46
 */

namespace App\Log\src;


use Illuminate\Log\Writer;
use Illuminate\Support\Facades\Log;

class AdminLog
{

    private $log;
    public function __construct(){

    }

    public function useDailyFiles($method){
        $dirpath = storage_path().'/logs/'.date('Y-m-d',time());
        if (!file_exists($dirpath)) {
            $old_mask = umask(0);
            if (!mkdir($dirpath, 02770, true)) {
                Log::info("创建".date('Y-m-d',time())."日志目录失败");
                return FALSE;
            }
            umask($old_mask);
        }
        $this->log = new Writer(new \Monolog\Logger($method));
        $this->log->useDailyFiles($dirpath.'/laravel-'.php_sapi_name().'-'.$method.'.log',30);
    }

    public function error($message, array $context = []){
        $this->useDailyFiles(__FUNCTION__);
        $this->log->write(__FUNCTION__,$message,$context);
    }

    public function info($message, array $context = []){
        $this->useDailyFiles(__FUNCTION__);
        $this->log->write(__FUNCTION__,$message,$context);

    }

    public function debug($message, array $context = []){
        $this->useDailyFiles(__FUNCTION__);
        $this->log->write(__FUNCTION__,$message,$context);
    }
}