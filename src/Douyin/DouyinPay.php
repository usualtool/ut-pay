<?php
namespace usualtool\Pay\Douyin;
/**
 * 抖音小程序担保交易支付
 */
class DouyinPay{
    private $appid;
    private $salt;
    private $gate = "https://developer.toutiao.com/api/apps/ecpay/v1/";
    public function __construct($appid,$salt){
        $this->appid=trim((string)$appid);
        $this->salt=trim((string)$salt);
    }
    public function Ready(){
        return $this->appid!="" && $this->salt!="";
    }
    /* 
     * 官方签名规则
     */
    public function Sign($params){
        ksort($params);
        $str="";
        foreach($params as $k=>$v){
            if($k=="sign" || $k=="app_id" || $k=="other_settle_params" || $k=="thirdparty_id"){
                continue;
            }
            if(is_array($v)){
                $v=json_encode($v,JSON_UNESCAPED_UNICODE);
            }
            $str.=$v;
        }
        return md5($str.$this->salt);
    }
    /* 
     * 创建担保交易订单
     * $total_amount
     */
    public function CreateOrder($out_order_no,$total_amount,$subject,$body,$notify_url,$valid_time=600){
        $params=array(
            "app_id"=>$this->appid,
            "out_order_no"=>(string)$out_order_no,
            "total_amount"=>intval(round(((float)$total_amount)*100)),
            "subject"=>$subject,
            "body"=>$body,
            "valid_time"=>intval($valid_time),
            "notify_url"=>$notify_url
        );
        $params["sign"]=$this->Sign($params);
        $res=$this->Post($this->gate."create_order",json_encode($params,JSON_UNESCAPED_UNICODE));
        return json_decode($res,true);
    }
    /* 
     * 查单
     */
    public function QueryOrder($out_order_no){
        $params=array(
            "app_id"=>$this->appid,
            "out_order_no"=>(string)$out_order_no
        );
        $params["sign"]=$this->Sign($params);
        $res=$this->Post($this->gate."query_order",json_encode($params,JSON_UNESCAPED_UNICODE));
        return json_decode($res,true);
    }
    /* 
     * 回调验签
     */
    public function VerifyNotify($post){
        if(empty($post["sign"]) || empty($post["msg"])){
            return false;
        }
        $sign=$post["sign"];
        $params=$post;
        unset($params["sign"]);
        return hash_equals($this->Sign($params),(string)$sign);
    }
    private function Post($url,$data){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        $res=curl_exec($ch);
        if(PHP_VERSION_ID<80000) curl_close($ch);
        return $res;
    }
}