<?php
namespace usualtool\Pay\Paypal;
/**
 * PayPal SDK 装载与 ApiContext 构造
 * $set 为 payment 模块从 cms_pay 读出的行（pp_mod/pp_clientid/pp_secret）
 */
class Sdk{
    private static $loaded=false;
    public static function Start($set,$siteUrl=""){
        if(!self::$loaded){
            \usualtool\Pay\Load::Sdk("Paypal/lib/autoload.php");
            self::$loaded=true;
        }
        if(!defined("SITE_URL") && $siteUrl!=""){ define("SITE_URL",$siteUrl); }
        return new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential($set["pp_clientid"] ?? "",$set["pp_secret"] ?? "")
        );
    }
}