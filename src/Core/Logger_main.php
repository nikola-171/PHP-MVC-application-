<?php

namespace app\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/*klasa koja vrši logovanje svih aktivnosti unutar aplikacije
  imaćemo tri logera za tri vrste aktivnosti - warning, info i error
*/
class Logger_main{

    private static $logger_warning = null;
    private static $logger_info = null;
    private static $logger_error = null;

    public static function log_warning($sadrzaj){
        if(self::$logger_warning == null){
            self::$logger_warning = new Logger('main_logger');
            self::$logger_warning->pushHandler(new StreamHandler('F:\projekat\src\Logger\logger_warning.log', Logger::WARNING));
        }
        self::$logger_warning->warning($sadrzaj);
    }

    public static function log_info($sadrzaj){
        if(self::$logger_info == null){
            self::$logger_info = new Logger('main_logger');
            self::$logger_info->pushHandler(new StreamHandler('F:\projekat\src\Logger\logger_info.log', Logger::INFO));
        }
        self::$logger_info->info($sadrzaj);
    }

    public static function log_error($sadrzaj){
        if(self::$logger_error == null){
            self::$logger_error = new Logger('main_logger');
            self::$logger_error->pushHandler(new StreamHandler('F:\projekat\src\Logger\logger_error.log', Logger::ERROR));
        }
        self::$logger_error->error($sadrzaj);
    }
}