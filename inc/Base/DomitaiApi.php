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

    public function domitaiPay($order,$slug,$isTestnetActive){
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
            //"Authorization: bearer ".$token,
            "Content-Type: application/json"
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $todo = json_decode($response,true);
        return $todo;
    }

    public function languageTranslate($pType){
        $lenguage = explode("_",get_locale());
        $arrayTexts = array(
                            "es" => array(
                                "description" => "Permite realizar pagos con la criptomoneda de tu preferencia.",
                                "punto_venta" => "Punto de venta de domitai",
                                "testnet" => "Habilitar testnet",
                                "title" => "Titulo",
                                "description_field" => "Descripción"
                            ),
                            "en" => array(
                                "description" => "Allows you to make payments with the crypto currency of your choice.",
                                "punto_venta" => "Domitai's point of sale",
                                "title" => "Title",
                                "testnet" => "Enable Testnet",
                                "description_field" => "Description"
                            ));

        if(array_key_exists($lenguage[0],$arrayTexts)){
            if(array_key_exists($pType,$arrayTexts[$lenguage[0]])){
               return $arrayTexts[$lenguage[0]][$pType];
            }else return $arrayTexts['es'][$pType]; 
        }else return $arrayTexts['es'][$pType];

    }
}
