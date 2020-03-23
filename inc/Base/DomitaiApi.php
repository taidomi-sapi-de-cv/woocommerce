<?php
namespace Inc\Base;
require_once dirname(dirname(dirname(__FILE__)))."/vendor/autoload.php";
use Ahc\Jwt\JWT;

class DomitaiApi{

    public function generateJWT($key,$secret_key){
        $jwt = new JWT($secret_key);
        $accessToken = $jwt->encode([
            'nonce'  => time(),
            'iat'    => time(),
            'exp'    => time() + (60*60),
            'key'    => $key,
        ]);
        return $accessToken;
    }

    public function domitaiPay($token,$order,$slug,$isTestnetActive){
        $venta = json_decode($order);
        $postFields = array(
                            "slug" => $slug,
                            "currency" => $isTestnetActive == 'yes'?"MXNt":'MXN',
                            "amount" => $venta->total,
                            "customer_data" => array(
                                "first_name" => $venta->billing->first_name,
                                "last_name" => $venta->billing->last_name,
                                "email" => $venta->billing->email,
                                "orderid" => $order->id
                            ),
                            "generateQR" => true);
        $postFieldsString = json_encode($postFields);
        //print_r($postFields);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://domitai.com/api/pos");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            "Authorization: bearer ".$token,
            "Content-Type: application/json"
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $todo = json_decode($response,true);
        return $todo;
    }
}
