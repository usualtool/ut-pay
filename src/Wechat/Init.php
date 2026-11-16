<?php
namespace usualtool\Pay\Wechat;
/**
 * 微信支付配置注入：define 全局常量后装载包内 WxPayConfig
 * $set 为 payment 模块从 cms_pay 读出的行（wx_appid/wx_mchid/wx_key/wx_secert/wx_notify_url）
 * 证书使用包内 cert/ 目录；$certDir 传入时覆盖
 */
class Init{
    public static function Start($set,$certDir=""){
        if(!defined("wx_appid")){ define("wx_appid",$set["wx_appid"] ?? ""); }
        if(!defined("wx_mchid")){ define("wx_mchid",$set["wx_mchid"] ?? ""); }
        if(!defined("wx_key")){ define("wx_key",$set["wx_key"] ?? ""); }
        if(!defined("wx_secert")){ define("wx_secert",$set["wx_secert"] ?? ""); }
        if(!defined("wx_notify_url")){ define("wx_notify_url",$set["wx_notify_url"] ?? ""); }
        $cert=rtrim($certDir!="" ? $certDir : __DIR__.DIRECTORY_SEPARATOR."cert",DIRECTORY_SEPARATOR);
        if(!defined("ssl_cert_path")){ define("ssl_cert_path",$cert.DIRECTORY_SEPARATOR."apiclient_cert.pem"); }
        if(!defined("ssl_key_path")){ define("ssl_key_path",$cert.DIRECTORY_SEPARATOR."apiclient_key.pem"); }
        if(!class_exists("WxPayConfig")){
            \usualtool\Pay\Load::Sdk("Wechat/lib/WxPay.Config.php");
        }
        return true;
    }
}