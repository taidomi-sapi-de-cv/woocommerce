<?php
/**
 * Plugin Name: WooDomitai
 * Description: Plugin que permite realizar pagos con diversas criptomonedas para woocommerce.
 * Author: Nodeschool
 * Version: 1.2.1
 * Text Domain: wc-gateway-offline
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Gateway-Offline
 * @author    Nodeschool Tabasco
 * @category  Admin
 *
 * This offline gateway forks the WooCommerce core "Cheque" payment gateway to create another offline payment method.
 */
defined( 'ABSPATH' ) or exit;
use Inc\Base\Activate;
use Inc\Base\Deactivate;

function activate_domitai_plugin(){
	Activate::activate();
}

function deactivate_domitai_plugin(){
   Deactivate::deactive();
}

register_activation_hook(__FILE__,'activate_domitai_plugin');
register_deactivation_hook(__FILE__,'deactivate_domitai_plugin');

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}
if(file_exists(dirname(__FILE__).'/vendor/autoload.php')){
	require_once dirname(__FILE__).'/vendor/autoload.php';
}
/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function wc_offline_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Gateway_Offline';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wc_offline_add_to_gateways' );
/**
 * Adds plugin page links
 * 
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_offline_gateway_plugin_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=offline_gateway' ) . '">' . __( 'Configure', 'wc-gateway-offline' ) . '</a>'
	);
	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_offline_gateway_plugin_links' );
/**
 * Offline Payment Gateway
 *
 * Provides an Offline Payment Gateway; mainly for testing purposes.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_Gateway_Offline
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		Nodeschool Tabasco
 */
add_action( 'plugins_loaded', 'wc_offline_gateway_init', 11 );
function wl_orders(){
	$data = file_get_contents('php://input');
	$data = json_decode($data,true);
	if($data and $data['customer_data']['orderid'] ){
		$orderid = $data['customer_data']['orderid'];
		$order = new WC_Order($orderid);
		if($order->status == "cancelled" or $order->status == "completed"){
			$messageStatus = $order->status == "cancelled"?'cancelada':'completada';
			return array("message" =>"Esta orden esta ".$messageStatus);
		}else{
			if($data['status'] == "payment_received") $order->update_status( 'processing');
			elseif($data['status'] == "payment_confirmed") $order->update_status( 'completed' );
			else $order->update_status( 'cancelled' );
			return array("message" => "Success","code" => 200);
		}
	}else{
		return array("message" => "Error al guardar","code" => 200);
	}
}

function wl_check_status(){
	if(isset($_POST['id'])){
		$orderid = $_POST['id'];
		$order = new WC_Order($orderid);
		return array("message" => "correcto","status" => $order->status);	;
	}else return array("message" => "Sin permisos para entrar");
}
add_action('rest_api_init',function(){
	register_rest_route( 'wl/v1','webhook',[
		'methods'=>'POST',
		'callback' => 'wl_orders'
	]);
});
add_action('rest_api_init',function(){
	register_rest_route( 'wl/v1','status_order',[
		'methods'=>'POST',
		'callback' => 'wl_check_status'
	]);
});
use Inc\Base\DomitaiApi;
function wc_offline_gateway_init() {

	class WC_Gateway_Offline extends WC_Payment_Gateway {
		// Constructor for the gateway.
		public function __construct() {
			$this->id                 = 'bitcoin_domitai';
			$this->icon               = apply_filters('woocommerce_offline_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'Woo-domitai', 'wc-gateway-offline' );
			$this->method_description = __( 'Permite realizar pagos con la criptomoneda de tu preferencia.', 'wc-gateway-offline' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = "<div style='text-align:center'><p>".$this->get_option( 'description' )."</p></div>";//$this->get_option( 'description' );
			//$this->instructions = $this->get_option( 'instructions', $this->instructions );
			$this->token = $this->get_option( 'token', $this->token );
			$this->token_secret = $this->get_option( 'token_secret',$this->token_secret);
			$this->punto_venta = $this->get_option( 'punto_venta', $this->punto_venta);
			$this->isTest = $this->get_option("isTest",$this->isTest);
		  
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			//add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
			
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
	
		//Initialize Gateway Settings Form Fields
		public function init_form_fields() {
			$this->form_fields =  array(
		  		'title' => array(
					'title'       => __( 'Title', 'wc-gateway-offline' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-offline' ),
					'default'     => __( 'Plugin domitai', 'wc-gateway-offline' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Description', 'wc-gateway-offline' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-gateway-offline' ),
					'default'     => __( 'Please remit payment to Store Name upon pickup or delivery.', 'wc-gateway-offline' ),
					'desc_tip'    => true,
				),
				'token' => array(
					'title'       => __( 'Domitai Key', 'wc-gateway-offline' ),
					'type'        => 'input',
					'description' => __( 'Token of domitai account.', 'wc-gateway-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'token_secret' => array(
					'title'       => __( 'Domitai Key Secret', 'wc-gateway-offline' ),
					'type'        => 'input',
					'description' => __( 'Secret token of domitai account.', 'wc-gateway-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'punto_venta' => array(
					'title'       => __( 'Punto de venta de domitai', 'wc-gateway-offline' ),
					'type'        => 'input',
					'description' => __( 'Punto de venta creado en tu aplicación de domitai', 'wc-gateway-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'isTest' => array(
					'title'       => __( 'Habilitar testnet', 'wc-gateway-offline' ),
					'type'        => 'checkbox',
					'description' => __( 'Se habilita para poder probar que el punto de venta esta correctamente configurado.', 'wc-gateway-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			);
		}
	
	
		 //Output for the order received page.
		 
		public function thankyou_page() {
			if ( $this->instructions ) {
				echo "<h5>".$this->instructions."</h5>";
			}
		}

		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}
	
		public function process_payment( $order_id ) {
			//echo $this->token;
			$order = wc_get_order( $order_id );
			$token = DomitaiApi::generateJWT($this->token,$this->token_secret);
			//Petición a la API de domitai
			$todo = DomitaiApi::domitaiPay($token,$order,$this->punto_venta,$this->isTest);
			$qr = $todo['payload']['accepted'];
			if(count($qr)>0){
				$oid = $todo['oid'];
				$order->update_meta_data("_qr_image",$qr);
				$order->update_meta_data("_oid",$oid);
				$order->save();
				// Mark as on-hold (we're awaiting the payment)
				//$order->update_status( 'on-hold', __( 'Awaiting offline payment', 'wc-gateway-offline' ) );
				// Reduce stock levels
				$order->reduce_order_stock();
				// Return thankyou redirect
				return array(
					'result' 	=> 'success',
					'redirect'	=> $this->get_return_url( $order )
				);
			}else{
				print_r($todo);
				return array(
					'result' 	=> 'failure',
					'messages'	=> "Ha ocurrido un error en la API",
					"obj" => $todo
				);
			}
		}

		function ruta_plantilla() {
			global $template;
			print_r($template);
		}
  }

  if( class_exists('Inc\\Init')){
	Inc\Init::register_services();
  }
}
