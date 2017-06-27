<?php
namespace App\Log\src;
use Illuminate\Log\Writer;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonLogger;

class ShortLog
{
    private $log;
    private $path;
    private $logger;
    private $formatter;

    public function __construct()
    {
        $path = storage_path() . '/logs/' . date('Y-m-d', time());
        if (!file_exists($path)) {
            $old_mask = umask(0);
            if (!mkdir($path, 02770, true)) {
                Log::info("创建" . date('Y-m-d', time()) . "日志目录失败");
                return FALSE;
            }
            umask($old_mask);
        }
        $this->path = $path;
        // 创建Logger实例
        $this->logger = new MonLogger('short_logger');

        $format = "[%datetime%] : %message%\n";
        $this->formatter = new LineFormatter($format);
    }


    public function info($message, $type = '')
    {
        if(env('SHORT_LOG_CLOSE')){
            return;
        }
        $logStreamHandler = new StreamHandler($this->path . "/$type-info-".date('Y-m-d', time()).".log", MonLogger::INFO,true,0777);
        $logStreamHandler->setFormatter($this->formatter);
        $this->logger->pushHandler($logStreamHandler);
        $this->logger->addInfo($message);
    }
}