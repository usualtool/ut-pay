<?php
namespace usualtool\Pay\Paypal;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
/**
 * PayPal 网页支付流程封装：模块不再直接引用 PayPal SDK 类
 */
class Factory{
    /* 创建订单并返回 approval 跳转链接，失败返回 "" */
    public static function Order($paypal,$amount,$unit,$posnum,$returnUrl,$cancelUrl){
        $shipping=0.00;
        $total=$amount+$shipping;
        $payer=new Payer();
        $payer->setPaymentMethod('paypal');
        $item=new Item();
        $item->setName("Pay Order:".$posnum)
             ->setCurrency($unit)
             ->setQuantity(1)
             ->setPrice($amount);
        $itemList=new ItemList();
        $itemList->setItems([$item]);
        $details=new Details();
        $details->setShipping($shipping)
                ->setSubtotal($amount);
        $amountObj=new Amount();
        $amountObj->setCurrency($unit)
                  ->setTotal($total)
                  ->setDetails($details);
        $transaction=new Transaction();
        $transaction->setAmount($amountObj)
                    ->setItemList($itemList)
                    ->setDescription("Pay ".$posnum)
                    ->setInvoiceNumber($posnum);
        $redirectUrls=new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)->setCancelUrl($cancelUrl);
        $payment=new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions([$transaction]);
        try {
            $payment->create($paypal);
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            return "";
        }
        return $payment->getApprovalLink();
    }
    /* 执行支付，成功返回 ["paynum"=>..,"posnum"=>..,"amount"=>..]，失败返回 false */
    public static function Execute($paypal,$paymentID,$payerID){
        try {
            $payment = Payment::get($paymentID,$paypal);
            $execute = new PaymentExecution();
            $execute->setPayerId($payerID);
            $result = $payment->execute($execute,$paypal);
            return array(
                "paynum" => $result->transactions[0]->related_resources[0]->sale->id,
                "posnum" => $result->transactions[0]->invoice_number,
                "amount" => $result->transactions[0]->amount->total
            );
        } catch (\Exception $e) {
            return false;
        }
    }
}