<?php
defined( 'ABSPATH' ) || exit;
$language = explode("_",get_locale());
?>
<style>
 .selected{
	 background:grey;
	 color:white;
	 font-weight:bold;
 }
 .buttonCoin{
	border-radius:10px;
	border:none;
	outline:none;
	position:relative;
	padding: 0 30px;
    height: 30px;
 }
 .classP{
	font-size:40px;
	padding:0;
	margin:0
 }
 .imgConfirmation{
	 width:200px;
	 height:200px;
	 background:transparent!important;
	 border:none!important;
 }
 .statusClass{
	font-weight:bold;
 }
 .procesando{
	 color:orange;
 }
 .completado{
	 color:green;
 }
 .cancelado{
	 color:red;
 }
 .qrClass{
	 text-align:center;
 }
 .addressPay{
	 cursor:pointer;
 }
 .addressPay:hover{
	 background:#DBFFDB
 }
 .imageGeneral {
	 padding:0!important;
	display:initial!important;
	background:transparent!important;
	border:none!important;
 }
 .icons_coins{
	 width:20px!important;
	 position: absolute;
     left: 5px;
     top: 5px;
 }
</style>
<div class="woocommerce-order">
 	<input type="hidden" id="language" value="<?=$language[0]?>"/>
	<?php if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
		<input type="hidden" id="orderid" value="<?=$order->get_id()?>"/>
		<input type="hidden" id="statusOrder" value="<?=$order->status?>"/>
		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
   <?php if($order->get_meta("_qr_image")){ ?>
		<div id="content_qr">
			<h3><?=$language[0] == "es"?"Seleccionar la criptomoneda para pagar:":"Select the crypto-currency to pay:"?></h3>
			<br>
			<?php if($order->status == 'on-hold' or $order->status == 'pending'){ ?>
				<div style="text-align:center">
					<?php foreach($order->get_meta("_qr_image") as $i => $coin): ?>
						<button class="buttonCoin"><?=$coin['currency']?> <img class="imageGeneral icons_coins" src="/imgs/<?=$coin['currency'].'.png'?>" /> <?=$coin['label']=='Label Undefined'?'':'- '.$coin['label']?></button>
					<?php endforeach; ?>
				</div>

				<div>
					<?php foreach($order->get_meta("_qr_image") as $i => $coin): ?>
						<div class="qrClass" style="display:none">
							<br>
							<img class="imageGeneral" src="/imgs/<?=$coin['currency'].'.png'?>" />
							<h5><?=$coin['currency']?> <?=$coin['label']=='Label Undefined'?'':'- '.$coin['label']?></h5>
							<br>
							<h5><?=$language[0] == "es"?"Cantidad equivalente:":"Equivalent quantity:"?></h5>
							<h3>$ <?=$coin['amount']?></h3>
							<br><br>
							<img style="display:initial" src="<?=$coin['qr']?>" alt="No se puede mostrar qr"/>
							<br><br>
							<a href="<?=$coin['uri']?>"><?=$language[0] == "es"?"Abrir wallet":"Open Wallet"?></a>
							<br><br>
							<input style="width:100%;text-align:center" class="addressPay" type="text" value="<?=$coin['address']?>" readonly/>
						</div>
					<?php endforeach; ?>
				</div>
			<?php }elseif($order->status == 'processing'){ ?>
				<div>
				    <img class="imgConfirmation" src="/imgs/confirmacion.png" />
					<p class="classP"><?=$language[0] == "es"? "Su pago ya fue recibido, se le notificara por correo cuando sea validado":"Your payment has been received, you will be notified by mail when it is validated"?></p>
					<span class="statusClass procesando"><?= $language[0] == "es" ? "PROCESANDO PAGO":"PROCESSING PAYMENT" ?></span>
				</div>
			<?php }elseif($order->status == "cancelled"){ ?>
				<div>
					<img class="imgConfirmation" src="/imgs/error.png" />
					<p class="classP"><?=$language[0] == "es"?"Lo sentimos, su pago ha sido rechazado. Su dinero sera devuelto pronto a su wallet":"Sorry, your payment has been refused. Your money will be returned to your wallet soon"?></p>
					<span class="statusClass cancelado"><?=$language[0] == "es" ? "VENTA CANCELADA":"SALE CANCELLED"?></span>
				</div>
			<?php }elseif($order->status == "completed"){ ?>
				<div>
					<img class="imgConfirmation" src="/imgs/confirmacion.png" />
					<p class="classP"><?=$language[0] == "es" ?"Pago aceptado. ¡GRACIAS POR SU COMPRA!":"Payment accepted. THANK YOU FOR YOUR PURCHASE!"?></p>
					<span class="statusClass completado"><?= $language[0] == "es" ? "VENTA COMPLETADA":"SALE COMPLETED." ?></span>
				</div>
			<?php } ?>
		</div>
   <?php } ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>
</div>
<?php if($order->get_meta("_qr_image")){ ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
	var inputAddress = document.querySelectorAll(".addressPay");
	var languagePage = document.getElementById("language").value;
	inputAddress.forEach(element => {
		element.addEventListener("focus",() => {
			let selBox = document.createElement('textarea');
			selBox.style.position = 'fixed';
			selBox.style.left = '0';
			selBox.style.top = '0';
			selBox.style.opacity = '0';
			selBox.value = element.value;
			document.body.appendChild(selBox);
			selBox.focus();
			selBox.select();
			document.execCommand('copy');
			document.body.removeChild(selBox);
			Swal.fire({
				type:"success",
				title:languagePage == "es" ? "Copiado":"Copied",
				timer:1500,
				showConfirmButton:false
			})
		})
	})


	var buttons = document.getElementsByClassName('buttonCoin');
	for( let i = 0;i<buttons.length;i++){
		buttons[i].addEventListener("click",() => {
			let classQr = document.getElementsByClassName("qrClass");
			let count = 0;
			for (let qr of classQr ) {
				qr.setAttribute("style","display:none");
				buttons[count].classList.remove("selected");
				count++;
			}
			classQr[i].removeAttribute("style");
			buttons[i].classList.add("selected");
		});
	}
	let count = 0;
	var id = document.getElementById("orderid").value;
	function deleteChild(parentNode,text,status){
		let hijos = parentNode.childNodes;
		parentNode.querySelectorAll('*').forEach(n => n.remove());
		let newDiv = document.createElement("div");
		let texto = document.createTextNode(text);

		let messageStatus = "";
		let img = document.createElement("img");
		img.classList.add("imgConfirmation");

		if(status === "PROCESANDO" || status === "COMPLETADO") img.setAttribute("src","/imgs/confirmacion.png");
		else img.setAttribite("src","/imgs/error.png");

		if (status === "PROCESANDO" ) messageStatus = languagePage == "es"? "PROCESANDO PAGO":"PROCESSING PAYMENT";
		else if (status === "COMPLETADO" ) messageStatus =languagePage == "es" ? "VENTA COMPLETADA":"SALE COMPLETED.";
		else if ( status === "CANCELADO" ) messageStatus = languagePage == "es"?"VENTA CANCELADA":"SALE CANCELLED";


		let spanStatus = document.createElement("span");
		let statusText = document.createTextNode(messageStatus);
		spanStatus.classList.add("statusClass");
		spanStatus.classList.add(status.toLowerCase());
		spanStatus.append(statusText);

		let newParrafo = document.createElement("p");
		newParrafo.classList.add("classP");
		newParrafo.append(texto);

		newDiv.append(img);
		newDiv.append(newParrafo);
		newDiv.append(spanStatus);
		parentNode.append(newDiv);
	}
	let isChange = false;
	let statusOrder = document.getElementById("statusOrder").value;
	if(statusOrder == "cancelled" || statusOrder == "completed" ) isChange = true;

	setInterval(async () => {
		if(!isChange){
			let response = await fetch("/wp-json/wl/v1/status_order",{
				method:"POST",
				headers:{
					'Content-type':'application/x-www-form-urlencoded'
				},
				body:"id="+id
			});
			let content = await response.json();
			let contentQr = document.getElementById("content_qr");

			switch (content.status) {
				case 'processing':
					deleteChild(contentQr,language == "es"?"Su pago ya fue recibido, se le notificara por correo cuando sea validado":"Your payment has been received, you will be notified by mail when it is validated","PROCESANDO");
					isChange=true;
					break;
				case 'completed':
					deleteChild(contentQr,language == "es" ? "Pago aceptado. ¡GRACIAS POR SU COMPRA!":"Payment accepted. THANK YOU FOR YOUR PURCHASE!","COMPLETADO");
					isChange=true;
					break;
				case 'cancelled':
					deleteChild(contentQr,language == "es" ? "Lo sentimos, su pago ha sido rechazado. Su dinero sera devuelto pronto a su wallet":"Sorry, your payment has been refused. Your money will be returned to your wallet soon","CANCELADO");
					isChange=true;
					break;
			}
		}
	}, 10000);
</script>
<?php } ?>
