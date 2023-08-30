<?php
/**
 * Email Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 5.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//getting fields values
$text_align = is_rtl() ? 'right' : 'left';
$address    = $order->get_formatted_billing_address();
$shipping   = $order->get_formatted_shipping_address();

$timol_apod = get_post_meta($order->id, '_billing_timologio', true);
$afm = get_post_meta($order->id, '_billing_afm', true);
$eponimia = get_post_meta($order->id, '_billing_eponimia', true);
$dou = get_post_meta($order->id, '_billing_doy', true);
$name = get_post_meta($order->id, '_billing_name', true);


?><table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
	<tr>
		<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
			<h2><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h2>

			<address class="address">

				<!-- stoixeia pelath -->
			<?php 
			if($timol_apod=='0'){
				echo "Τιμολόγιο"; echo "<br>";
				echo $eponimia ; echo "<br>";
				echo "Α.Φ.Μ.:"." ". $afm ; echo "<br>";
				echo $dou ; echo "<br>";
			}elseif($timol_apod=='1'){
				echo "Απόδειξη"; echo "<br>";
				echo $name; echo "<br>";
			}
						
			?>  
				<?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', 'woocommerce' ) ); ?>
				
				<?php if ( $order->get_billing_phone() ) : ?>
					<br/><?php echo "Τ:"." "?><a style="color:#000000;" href="tel:<?php echo $order->get_billing_phone()?>"><?php echo $order->get_billing_phone()?></a>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
					<br/><?php echo "E:"." "?><a style="color:#000000;" href="tel:<?php echo $order->get_billing_email()?>"><?php echo $order->get_billing_email()?></a>
				<?php endif; ?>
				
			</address>
		</td>
		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping ) : ?>
			<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;" valign="top" width="50%">
				<h2><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>

				<address class="address">
					<?php echo wp_kses_post( $shipping ); ?>
					<?php if ( $order->get_shipping_phone() ) : ?>
						<br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); ?>
					<?php endif; ?>
				</address>
			</td>
			
		<?php endif; ?>
	</tr>
</table>
