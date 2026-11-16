<?php
namespace usualtool\Pay\Alipay;
/**
 * 支付宝 AopSdk 装载与配置组装
 */
class Sdk{
    private static $loaded=false;
    public static function Load($set){
        if(!self::$loaded){
            \usualtool\Pay\Load::Sdk("Alipay/sdk/AopSdk.php");
            self::$loaded=true;
        }
        return array(
            'app_id' => $set["ali_appid"] ?? "",
            'merchant_private_key' => urldecode($set["ali_private_key"] ?? ""),
            'notify_url' => $set["ali_notify_url"] ?? "",
            'return_url' => $set["ali_return_url"] ?? "",
            'charset' => "UTF-8",
            'sign_type' => "RSA2",
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            'alipay_public_key' => urldecode($set["ali_public_key"] ?? "")
        );
    }
}