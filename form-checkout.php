<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">

				<?php $service_id = 192;
					$duration_type = get_field('annual_durationly', $service_id);
					if($duration_type == 'Ετήσιο'){
						$duration_string = 'Έτη';
					}else{
						$duration_string = 'Μήνες';
					}
				//update_field('save_service_id', $service_id);
				
				?>	
				<!-- <form action="<?php //echo esc_url( wc_get_checkout_url() ); ?>" method="post"> -->


					<label for="select_duration">Επιλέξτε Διάρκεια <?php echo '('.$duration_string.')'; ?>:</label>
					
					<select name="select_duration" id="select_duration" >
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					</select>
				<!-- </form>	 -->
				
				
				
				<?php  
				
				if ( isset(WC()->session) && ! WC()->session->has_session() ) {
					WC()->session->set_customer_session_cookie( true );
				}
							

				// Save custom session data as order meta data
				$session_array = WC()->session->get( 'custom_p' ); // Get custom data from session
				$session_service_id = $session_array['service_id'];
				//$session_service_duration = $session_array['quantity'];

				echo "<script>console.log('session_service_id: " . $session_service_id ."' );</script>";
				
				// if ( isset($data['service_id']) ) {
				// 	$order->update_meta_data( '_service_id', $data['service_id'] );
				// }
				
				// WC()->session->__unset( 'custom_data' ); // Remove session variable
			
				//update_field('service_id', $service_id);
				?>	

			<div class="col-1">

				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
	
	
	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<?php
// $duration = setduration();
			
// // Set the session data
// WC()->session->set( 'service_duration', $duration  );

// // Save custom session data as order meta data
// $session_service_duration = WC()->session->get( 'service_duration' ); 
?>

<script>
function setduration(){
	var duration;
	var selected = document.getElementById("select_duration");
	duration = selected.options[selected.selectedIndex].text;
	//console.log(duration);
	return duration;
}
</script>	