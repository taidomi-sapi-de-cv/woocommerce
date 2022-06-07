<?php
namespace Domitai\Base;
require_once dirname(dirname(dirname(__FILE__)))."/vendor/autoload.php";
use Ahc\Jwt\JWT;

class DomitaiApi{
    public function __construct() {
        $this->BaseApiUrl = "https://domitai.com/api";
    }
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
        $postFieldsString = wp_json_encode($postFields);
        $args = array(
            "body" => $postFieldsString,
            "headers" => array(
                "Content-Type" => "application/json"
            )
        );
        $response =  wp_remote_post( $this->BaseApiUrl."/pos", $args );
        $todo = json_decode(wp_remote_retrieve_body($response), true);
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
