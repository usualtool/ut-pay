<?php
namespace usualtool\Pay;
/**
 * 旧式 SDK 文件装载门面：$rel 相对包 src/ 目录
 * Load::Sdk("Alipay/sdk/service/AlipayTradeService.php")
 */
class Load{
    public static function Sdk($rel){
        $path=str_replace(array("/","\\"),DIRECTORY_SEPARATOR,$rel);
        if(strpos($path,"..")!==false){ return false; }
        $file=__DIR__.DIRECTORY_SEPARATOR.$path;
        if(!is_file($file)){ return false; }
        require_once $file;
        return true;
    }
    /* 包内某 SDK 文件的绝对路径（供 Stream/证书等场景） */
    public static function Path($rel){
        $path=str_replace(array("/","\\"),DIRECTORY_SEPARATOR,$rel);
        if(strpos($path,"..")!==false){ return ""; }
        $file=__DIR__.DIRECTORY_SEPARATOR.$path;
        return is_file($file) ? $file : "";
    }
}