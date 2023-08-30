<?php
/* enqueue scripts and style from parent theme */
add_action( 'wp_enqueue_scripts', 'twentytwentyone_styles');
function twentytwentyone_styles() {
	$parent_style = 'twenty-twenty-one-style';
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css',
    array($parent_style), wp_get_theme()->get('Version') );
}

//myfunctions
function create_customers() {
 
    register_post_type( 'wp_costumers',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Πελάτες' ),
                'singular_name' => __( 'Πελάτης' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'customers'),
            'show_in_rest' => true,
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_customers' );

function create_services() {
 
    register_post_type( 'wp_services',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Υπηρεσίες' ),
                'singular_name' => __( 'Υπηρεσία' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'services'),
            'show_in_rest' => true,
        )							
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_services' );


function create_services_categories() {
	
	register_taxonomy('services_categories', 
		'wp_services',
		array("hierarchical" => true,
			"label" => "Services Categories",
			"singular_label" => "Service Category",
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'service_cat', 'with_front' => false ),
			'public' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'_builtin' => false,
			'show_in_nav_menus' => true,
			'show_in_quick_edit'=>true,
			'show_admin_column'=>true,
			'meta_box_cb'=>true,
			'show_in_rest'=>true
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_services_categories' );

function create_hosting_packages() {

    register_post_type( 'packets_hosting',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Πακέτα Hosting' ),
                'singular_name' => __( 'Πακέτο Hosting' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'hosting'),
            'show_in_rest' => true,
 
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_hosting_packages' );
	
function get_services() {  
	
	$args = array(  
        'post_type' => 'wp_services',
		'post_status'=>'publish',
        'posts_per_page' => 9, 
        'orderby' => 'title', 
        'order' => 'ASC', 
    );			
    $loop =new WP_Query( $args ); 
	
	return $loop;	
}

function get_hosting() {  

	$args = array(  
        'post_type' => 'packets_hosting',
		'post_status'=>'publish',
        'posts_per_page' => 9, 
        'orderby' => 'title', 
        'order' => 'ASC', 
    );		
    $loop =new WP_Query( $args ); 
	
	return $loop;	
}

//Add the Post ID column 
function misha_add_column( $columns ){
	$columns['misha_post_id_clmn'] = 'ID'; // $columns['Column ID'] = 'Column Title';
	return $columns;
}
add_filter('manage_posts_columns', 'misha_add_column', 5);

//gia na fainetai to id se sthlh
function misha_column_content( $column, $id ){
	if( $column === 'misha_post_id_clmn')
		echo $id;
}
add_action('manage_posts_custom_column', 'misha_column_content', 5, 2);

function cron_send_services_notifications(){	

	$services_posts = get_services();
	$today = new DateTime('10-03-2022 00:00:00');  
	$mail_info = [];


	while ( $services_posts->have_posts() ) : $services_posts->the_post();
		$email_30_days = get_field('email_30_days'); 
		$email_5_days = get_field('email_5_days');
		$email_3_days = get_field('email_3_days');
		$expired = get_field('expired');
		$annual_monthly = get_field('annual_monthly');
		$expiration_date = get_field('expiration_date');
		$temp_string = $expiration_date.' 00:00:00';
		$expdate = new DateTime($temp_string);		
		
		$affiliate_email = '';
		$notify = get_field('notify_affiliate');
		$affiliate_email = get_field('affiliate');  //get affiliate value as email	
 
		//compare dates
		$diff = $today->diff($expdate)->format("%r %a");
		$title = get_the_title();

		//get customer email
		$customer_obj= get_field('customer');
		$customer_email = '';
		if( $customer_obj ){
			$customer_email = get_field('email', $customer_obj->ID);          
		}

		//create checkout link
		$post = get_post();		
		$post_id = $post->ID;
		//echo $post_id;
		//echo '<br>';	
		$serv_cost = get_field('cost');
		$service_id = $post->ID;
		$service_category_id = get_the_terms( $post_id, 'services_categories' )[0]->term_id; //getting post category id
		//echo $service_category_id;
		//echo '<br>';

		//selecting the producy id
		if($service_category_id == '32'){
			$productid = 420;
		}
		elseif($service_category_id == '33'){
			$productid = 421;		
		}
		elseif($service_category_id == '35'){
			$productid = 422;		
		}
		elseif($service_category_id == '34'){
			$productid = 456;	
		}

		//create checkout link: https://wordpress.designerd.gr/checkout/?add-to-cart=420&custom_p=350&quantity=1&link_referral=1&service_id=192
		$link = 'https://wordpress.designerd.gr/checkout?';
		$link .= 'add-to-cart='.$productid.'custom_p='.$serv_cost.'&quantity=1&link_referral=1&service_id='.$service_id;
		

		$send = 0;	
		$text = '';
		$text_to_affiliate = '';
		$text_to_admin = '';
		$subject = '';
		$renew_button = '<a href="'.$link.'">Ανανέωση</a>';
		
		//send email check
		if($annual_monthly=="Ετήσιο"){
			// echo 'Annual';
			// echo '<br>';
			if($diff<=30 && !$email_30_days){ 
				$send = 1;
				
				$text = "send email 30! Annual"."\r\n".$link;
				$subject = 'Payment reminder Annual-30';
				$text_to_admin = "send email 30! Annual , to Admin";
				//send_mail($customer_email,$text,$subject);
				update_field('email_30_days', 1);

				//send email to affiliate
				if($affiliate_email){
					if($notify){
						$text_to_affiliate = "send email 30! Annual , to affiliate";
						echo $text_to_affiliate;
						//send_mail($affiliate_email,$text_to_affiliate,$subject);
					}
				}
			}
			elseif($diff<=5 && !$email_5_days){ 
				$send = 1;
				$text = "send email 5! Annual"."\r\n".$link;
				$subject = 'Payment reminder Annual-5';
				$text_to_admin = "send email 5! Annual , to Admin";
				//send_mail($customer_email,$text,$subject);
				update_field('email_5_days', 1);

				//send email to affiliate
				if($affiliate_email){
					if($notify){
						$text_to_affiliate = "send email 5! Annual , to affiliate";
						echo $text_to_affiliate;
						//send_mail($affiliate_email,$text_to_affiliate,$subject);
					}
				}
			}              
			elseif($diff<=0 && !$expired){ 
				$send = 1;
				$text = "send email expired! Annual"."\r\n".$link;
				$subject = 'Payment reminder Annual-0';
				$text_to_admin = "send email expired! Annual , to Admin";
				//send_mail($customer_email,$text,$subject);
				update_field('expired', 1);

				//send email to affiliate
				if($affiliate_email){
					if($notify){
						$text_to_affiliate = "send email expired! Annual , to affiliate";
						echo $text_to_affiliate;
						//send_mail($affiliate_email,$text_to_affiliate,$subject);
					}
				}
			}else{
				// echo 'No Emails to be sent.';
			}         
		}
		elseif($annual_monthly=="Μηνιαίο"){
			// echo 'Monthly';
			// echo '<br>';
			if($diff<=3 && !$email_3_days){ 
				$send = 1;
				$text = "send email 3! Monthly"."\r\n".$link;
				$subject = 'Payment reminder Monthly-3';
				$text_to_admin = "send email 3! Monthly , to Admin";
				//send_mail($customer_email,$text,$subject);
				update_field('email_3_days', 1);

				//send email to affiliate
				if($affiliate_email){
					if($notify){
						$text_to_affiliate = "send email 3! Monthly , to affiliate";
						echo $text_to_affiliate;
						//send_mail($affiliate_email,$text_to_affiliate,$subject);
					}
				}
			}             
			elseif($diff<=0 && !$expired){ 
				$send = 1;
				$text = "send email expired! Monthly"."\r\n".$link;
				$subject = 'Payment reminder Monthly-0';
				$text_to_admin = "send email expired! Monthly , to Admin";
				//send_mail($customer_email,$text,$subject);
				update_field('expired', 1);

				//send email to affiliate
				if($affiliate_email){
					if($notify){
						$text_to_affiliate = "send email expired! Monthly , to affiliate";
						echo $text_to_affiliate;
						//send_mail($affiliate_email,$text_to_affiliate,$subject);
					}
				}
			}else{
				// echo 'No Emails to be sent.';
			}
		}else{
			// echo 'annual/monthly not set';
		}	
		$temp_customer_array = [];
		$temp_customer_array['email'] = $customer_email;
		$temp_customer_array['text'] = $text;
		$temp_customer_array['subject'] = $subject;
		$temp_customer_array['renew_button'] = $renew_button;


		array_push($mail_info,$temp_customer_array);
		
		//echo $expiration_date."   ";

		//renew_service($serviceid, $start_date, $duration);
	endwhile;

	//var_dump($mail_info);
	//echo '<br>';	
	pending_emails($mail_info);
	
}

function send_mail($to_email, $message, $subject, $renew_button){
	$message = '<p>'.$message.'</p>';
	$message .= $renew_button;
	echo $to_email;
	echo '<br>';
	echo $message;
	echo '<br>';
	echo $subject;
	echo '<br>';
	// $headers = 'From: info@designerd.gr' . "\r\n" .
	// 	'X-Mailer: PHP/' . phpversion();

	// mail($to_email, $subject, $message, $headers);
}

function pending_emails($mail_info_array){
	foreach ($mail_info_array as $customer_array) {
		send_mail($customer_array['email'],$customer_array['text'],$customer_array['subject'],$temp_customer_array['renew_button']);
	}
}

function renew_service($serviceid, $start_date, $duration){
	echo 'start renew';
	echo '<br>';
	$email_30_days = get_field('email_30_days', $serviceid); 
	echo "30-".$email_30_days;
	echo '<br>';
	$email_5_days = get_field('email_5_days', $serviceid);
	echo "5-".$email_5_days;
	echo '<br>';
	$email_3_days = get_field('email_3_days', $serviceid);
	echo "3-".$email_3_days;
	echo '<br>';
	$expired = get_field('expired', $serviceid);
	echo "0-".$expired;
	$annual_monthly = get_field('annual_monthly', $serviceid);
	$expiration_date = get_field('expiration_date', $serviceid);
	//$start_dateA= get_field('start_date', $serviceid);
	// $services_updade = get_field('services_updade', $serviceid);
	// $temp_string = $expiration_date.' 00:00:00';
	// $expdate = new DateTime($temp_string);
	// $date='';
	$start_date = new DateTime($start_date);
	//echo '<br>';
	//var_dump($start_date);
	
		if($annual_monthly=="Μηνιαίο"){
			echo '<br>';
			echo 'manual';
			$duration_str= 'P'.$duration.'M';
			echo '<br>';
			echo $duration_str;
			$end_date = $start_date->add(new DateInterval($duration_str));
			var_dump($end_date);
			echo '<br>';
			echo $end_date->format('Y-m-d') . "\n";								
			update_field('start_date', $start_date);
			echo '<br> ';
			echo'start';
			the_field('start_date');
			echo '<br> ';
			update_field('expiration_date', $end_date->format('d-m-Y'));
			echo '<br>';
			echo 'exp';
			the_field('expiration_date');
		}
		elseif($annual_monthly=="Ετήσιο"){
			echo '<br>';
			echo 'yearly';
			$duration_str= 'P'.$duration.'Y';
			$end_date = $start_date->add(new DateInterval($duration_str));
			//echo '<br>';
			//echo $end_date->format('d-m-Y') . "\n";
			update_field('start_date', $start_date);
			echo '<br> ';
			echo'start:';
			the_field('start_date');
			echo '<br>';
			echo 'exp:';
			update_field('expiration_date', $end_date->format('d-m-Y'));
			the_field('expiration_date');
			echo '<br>';
		}
	
		//echo $expiration_date."   ";
		
		//reset flags
		if($email_30_days){
			update_field('email_30_days', 0, $serviceid);
		}
		if($email_5_days){
			update_field('email_5_days', 0, $serviceid);
		}
		if($email_3_days){
			update_field('email_3_days', 0, $serviceid);
		}
		if($expired){
			update_field('expired', 0, $serviceid);
		}	
	
}

//add_action( 'send_mail', 'cron_send_services_notifications' );

//page access check
function page_access($developer = 0){
	if( is_user_logged_in() ){	
		$user = wp_get_current_user();		
		$allowed_admin = array('administrator');
		$allowed_dev = array('developer');

		//if role is admin give access
		if( array_intersect($allowed_admin, $user->roles ) ) {
			return true;
		}else if(array_intersect($allowed_dev, $user->roles) && $developer ){
			return true;
		}	
	} else{
		//
	}	
	return false;  
}

//header menu
function get_menu($currentslug){

	$user = wp_get_current_user();
	$roles = $user->roles[0]; 
	// $rol = 
	$menuitems = array(
		'customersinfo' => array(
			'label' => 'Πελάτες',
			'user_role' => 'administrator'
		),
		'servicesinfo' => array(
			'label' => 'Υπηρεσίες',
			'user_role' => 'administrator, developer'
		)
	);
	
	$html = '';
	foreach ($menuitems as $slug => $itemarray) {
		
		if (strpos($itemarray['user_role'], $roles) !== false) {
			$html .= '<a class="';
			if($currentslug == $slug){
				$html .= 'activemenu';
			}
			$html .= '" href="https://wordpress.designerd.gr/'.$slug.'/">'.$itemarray['label'].'</a>';
		}else {
			$html = "Δεν έχετε πρόσβαση στην σελίδα "."\r";
		}
		
		
	}
	return $html;
}

//change text 'add to cart' to 'Αγορά'
add_filter( 'woocommerce_loop_add_to_cart_link', 'misha_add_to_cart_text_1' );

function misha_add_to_cart_text_1( $add_to_cart_html ) {
	return str_replace( 'Add to cart', 'Αγορά', $add_to_cart_html );
}

//buy product button text
add_filter( 'woocommerce_product_single_add_to_cart_text', 'misha_add_to_cart_text_2' );
function misha_add_to_cart_text_2( $product ){
	return 'Αγορά';
}

// //redirect to checkout page after add to cart 
// add_filter( 'woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect_checkout' ,5);
// function custom_add_to_cart_redirect_checkout( $url ) {
//     return wc_get_checkout_url();
// }

// Remove fields from checkout form
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_first_name']);
	unset($fields['billing']['billing_last_name']);

    return $fields;
}

//rename fields
add_filter( 'woocommerce_default_address_fields' , 'rename_checkout_fields');

function rename_checkout_fields( $fields ) {

    $fields['address_1']['label'] = 'Διεύθυνση';
	$fields['address_1']['placeholder'] = '';
	$fields['address_2']['placeholder'] = '';
	$fields['city']['label'] = 'Πόλη';
	$fields['state']['label'] = 'Νομός';
	$fields['postcode']['label'] = 'Τ.Κ.';
	$fields['country']['label'] = 'Χώρα';
	
    return $fields;
}

//place order button text change
add_filter( 'woocommerce_order_button_text', 'misha_custom_button_text' );
 
function misha_custom_button_text( $button_text ) {
   return 'ΑΠΟΣΤΟΛΗ ΠΑΡΑΓΓΕΛΙΑΣ'; 
}

//Removes Additional Information Checkout Page
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

//add_invoice_fields
add_filter( 'woocommerce_checkout_fields', 'add_timologio_fields' );  
function add_timologio_fields($fields) {

	// $fields['billing']['billing_diarkeia'] = array(
	// 	'label'       => __( 'Επιλέξτε διάρκεια:', 'woocommerce' ),
	// 	'required'    => true,
	// 	'type'        => 'select',
	// 	'class'       => array( 'form-row-wide' ),
	// 	//'clear'       => true,		
	// 	'options'     => array(
	// 		'0' => 'Μηνιαίο',
	// 		'1' => 'Ετήσιο'
	// 	),'default' => '0',  //setting default value  
		
	// ); 
	
	$fields['billing']['billing_timologio'] = array(
		'label'       => __( 'Θέλετε τιμολόγιο ή απόδειξη;', 'woocommerce' ),
		'required'    => true,
		'type'        => 'radio',
		'class'       => array( 'form-row-wide invoice-select' ),
		//'clear'       => true,		
		'options'     => array(
			'0' => 'Τιμολόγιο',
			'1' => 'Απόδειξη'
		),'default' => '0',  //setting default value  
		
	);   
	  
	$fields['billing']['billing_afm'] = array(
		'label'       => __( 'Α.Φ.Μ.', 'woocommerce' ),
		'placeholder' => _x( 'Πληκτρολογήστε το 9ψηφιο ΑΦΜ σας', 'placeholder', 'woocommerce' ),
		'required'    => true,
		'type'        => 'text',
		'class'       => array( 'form-row-wide invoice-field invoice-afm-field ' ),
		'clear'       => true,
		'id'	=> 'afm_field',
	);

	$fields['billing']['billing_eponimia'] = array(
		'label'       => __( 'Επωνυμία', 'woocommerce' ),
		'required'    => true,
		'type'        => 'text',
		'class'       => array( 'form-row-wide invoice-field invoice-eponimia-field' ),
		'clear'       => true,
	);

	$fields['billing']['billing_doy'] = array(
		'label'       => __( 'Δ.Ο.Υ.', 'woocommerce' ),
		'required'    => true,
		'type'        => 'text',
		'class'       => array( 'form-row-wide invoice-field invoice-doy-field' ),
		'clear'       => true,
	);

	$fields['billing']['billing_name'] = array(
		'label'       => __( 'Ονοματεπώνυμο', 'woocommerce' ),
		'required'    => true,
		'type'        => 'text',
		'class'       => array( 'form-row-wide invoice-name-field' ),
		'clear'       => true,
	);
		
	return $fields;
} 


//change fields order
add_filter( 'woocommerce_checkout_fields', 'misha_order_fields' );

function misha_order_fields( $checkout_fields ) {
	//$checkout_fields['billing']['billing_diarkeia']['priority'] = 0;
	$checkout_fields['billing']['billing_timologio']['priority'] = 1;
	$checkout_fields['billing']['billing_name']['priority'] = 2;
	$checkout_fields['billing']['billing_afm']['priority'] = 3;
	$checkout_fields['billing']['billing_eponimia']['priority'] = 4;
//	$checkout_fields['billing']['billing_taxis']['priority'] = 5;
	$checkout_fields['billing']['billing_doy']['priority'] = 6;
	$checkout_fields['billing']['billing_address_1']['priority'] = 7;
	$checkout_fields['billing']['billing_address_2']['priority'] = 8;
	$checkout_fields['billing']['billing_city']['priority'] = 9;
	$checkout_fields['billing']['billing_state']['priority'] = 10;
	$checkout_fields['billing']['billing_postcode']['priority'] = 11;
	$checkout_fields['billing']['billing_country']['priority'] = 12;

	$checkout_fields['billing']['billing_state']['required'] = true;


	return $checkout_fields;
}

//remove autocomplete values
add_filter( 'woocommerce_checkout_get_value', 'bks_remove_values', 10, 2 );
function bks_remove_values( $value, $input ) {
    $item_to_set_null = array(
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_postcode',
            'billing_state',
            'billing_email',
            'billing_phone',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_city',
            'shipping_postcode',
            'shipping_state',
			'billing_name',
			'billing_afm',
			'billing_eponimia',
			'billing_dou',
        ); // All the fields in this array will be set as empty string, add or remove as required.

    if (in_array($input, $item_to_set_null)) {
        $value = '';
    }

    return $value;
}

//remove 'optional' text
add_filter( 'woocommerce_form_field' , 'elex_remove_checkout_optional_text', 10, 4 );
function elex_remove_checkout_optional_text( $field, $key, $args, $value ) {
if( is_checkout() && ! is_wc_endpoint_url() ) {
$optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
$field = str_replace( $optional, '', $field );
}
return $field;
} 

//Saving custom checkout fields meta data
add_action( 'woocommerce_checkout_create_order', function( $order, $data ) {

    $custom_fields = array(
        'billing_timologio',
        'billing_afm',
		'billing_eponimia',
		'billing_doy',
		'billing_name',
		'billing_diarkeia',
		'select_duration',
    );

    foreach ( $custom_fields as $field_name ) {
        if ( isset( $data[ $field_name ] ) ) {
            $meta_key = '_' . $field_name;
            $field_value = $data[ $field_name ]; 
            $order->update_meta_data( $meta_key, $field_value );
        }
    }
}, 10, 2 );

//second way
// add_action( 'woocommerce_checkout_update_order_meta', 'custom_checkout_fields_update_order_meta' );
// function custom_checkout_fields_update_order_meta( $order_id ) {
//     update_post_meta( $order_id, 'billing_timologio', sanitize_text_field( $_POST['billing_timologio'] ) );
//     update_post_meta( $order_id, 'billing_afm', sanitize_text_field( $_POST['billing_afm'] ) );
//     update_post_meta( $order_id, 'billing_eponimia', sanitize_text_field( $_POST['billing_eponimia'] ) );
//     update_post_meta( $order_id, 'billing_doy', sanitize_text_field( $_POST['billing_doy'] ) );
//     update_post_meta( $order_id, 'billing_name', sanitize_text_field( $_POST['billing_name'] ) );
// }
// if (!empty($_POST['billing']['billing_timologio'])) {
// 	update_post_meta($order_id, 'billing_timologio', esc_attr($_POST['billing']['billing_timologio']));
// 	}
// }

//showing order details on orders info wp
add_action('woocommerce_admin_order_data_after_billing_address', 'my_custom_billing_fields_display_admin_order_meta', 10, 1);

function my_custom_billing_fields_display_admin_order_meta($order) {
echo '<p><strong>' . __('diarkeia') . ':</strong><br> ' . get_post_meta($order->id, '_select_duration', true) . '</p>';
echo '<p><strong>' . __('timologio') . ':</strong><br> ' . get_post_meta($order->id, '_billing_timologio', true) . '</p>';
echo '<p><strong>' . __('afm') . ':</strong><br> ' . get_post_meta($order->id, '_billing_afm', true) . '</p>';
echo '<p><strong>' . __('eponimia') . ':</strong><br> ' . get_post_meta($order->id, '_billing_eponimia', true) . '</p>';
echo '<p><strong>' . __('doy') . ':</strong><br> ' . get_post_meta($order->id, '_billing_doy', true) . '</p>';
echo '<p><strong>' . __('name') . ':</strong><br> ' . get_post_meta($order->id, '_billing_name', true) . '</p>';
}

// get and set the custom product price in WC_Session
add_action( 'init', 'get_custom_product_price_set_to_session' );
function get_custom_product_price_set_to_session() {
    // Check that there is a 'custom_p' GET variable
    if( isset($_GET['add-to-cart']) && isset($_GET['custom_p']) && isset($_GET['link_referral']) && isset($_GET['service_id']) && isset($_GET['quantity']) && $_GET['custom_p'] > 0 && $_GET['add-to-cart'] > 0 && $_GET['link_referral'] && $_GET['service_id'] > 0 && $_GET['quantity'] > 0 ) {
        // Enable customer WC_Session (needed on first add to cart)
        if ( ! WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie( true );
        }
        // Set the product_id and the custom price in WC_Session variable
        WC()->session->set('custom_p', [
            'id'    => (int) wc_clean($_GET['add-to-cart']),
            'price' => (float) wc_clean($_GET['custom_p']),
			'link_referral' => 1,
			'service_id'    => (int) wc_clean($_GET['service_id']),
			'quantity'    => (int) wc_clean($_GET['quantity']),
        ]);
		//var_dump(WC()->session->get('custom_p')) ;
    }
	
}

// Change product price from WC_Session data
add_filter('woocommerce_product_get_price', 'custom_product_price', 900, 2 );
add_filter('woocommerce_product_get_regular_price', 'custom_product_price', 900, 2 );
add_filter('woocommerce_product_variation_get_price', 'custom_product_price', 900, 2 );
add_filter('woocommerce_product_variation_get_regular_price', 'custom_product_price', 900, 2 );
function custom_product_price( $price, $product ) {
	
	if( isset(WC()->session) && WC()->session->get('custom_p')['link_referral'] ){
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
		if ( ($data = WC()->session->get('custom_p') ) && $product->get_id() == $data['id'] ) {
			$price = $data['price'];
		}
	}
    
    return $price;
}

//clear cart via url  =>  https://wordpress.designerd.gr/shop?clear-cart
// add_action( 'init', 'woocommerce_clear_cart_url' );
// function woocommerce_clear_cart_url() {
// 	if ( isset( $_GET['clear-cart'] ) ) {
// 		global $woocommerce;
// 		$woocommerce->cart->empty_cart();
// 	}
// }

add_filter( 'woocommerce_add_to_cart_validation', 'custom_empty_cart', 10, 3 );
function custom_empty_cart( $passed, $product_id, $quantity ) {
    if( isset(WC()->session) && WC()->session->get('custom_p')['link_referral'] ){
		if( !WC()->cart->is_empty()  ){
			WC()->cart->empty_cart();
		}
	}
    return $passed;
}

add_action( 'woocommerce_order_status_completed', 'order_completed', 10, 1 );
function order_completed($order_id){//, $order na dw to hook
	//error_log('order completed');
	$service_id = get_field('service_id',$order_id);   
	$expiration_date = get_field('expiration_date',$service_id);
	$temp_string = $expiration_date.' 00:00:00';
	$expdate = new DateTime($temp_string);
	$expdate = $expdate->format('d-m-Y');

	$order = wc_get_order( $order_id );
	$completed_order_date = $order->get_date_completed();
	$completed_date = $completed_order_date->format('d-m-Y');
	
	echo 'service_id:'.$service_id;
	echo '<br>';
	echo 'completed_date:'.$completed_date;
	echo '<br>';
	echo 'exp_date:'.$expdate;
	echo '<br>';

	if($completed_date <= $expdate){
		$start_date = $expdate;
		echo 'start_date => expdate:'.$start_date;
	}
	
	elseif($completed_date > $expdate){
		$start_date = $completed_date;
		echo 'start_date => completed_date:'.$start_date;
	}
	echo '<br>';						
	// Get and Loop Over Order Items
	foreach ( $order->get_items() as $item_id => $item ) {
		//$product_id = $item->get_product_id();
		//$total = $item->get_total();
		$duration = $item->get_quantity();
		renew_service($service_id, $start_date, $duration);
	}	
}

?>





