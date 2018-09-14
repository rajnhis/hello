<?php
/**
 * Add the field to the checkout testing file
 **/
add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field( $checkout ) {	
	global $woocommerce;
    foreach ( $woocommerce->cart->cart_contents as $product ) {
        //$product is a array, and 10-pack is the slug of the attribute name
        print_r($product);
		echo $product['variation_id'];
		//WC()->cart->add_to_cart( $product['product_id'], 1, $product['variation_id'], $variation='false' );
    }
	
	echo '<div class="input_fields_wrap">
    <button class="add_field_button">Add More Fields</button>
    <div><input type="text" name="my_field_name[]"></div>
</div>';
	
	echo '<div id="my_custom_checkout_field"><h3>'.__('My Field').'</h3>';
				
	/**
	 * Output the field. This is for 1.4.
	 *
	 * To make it compatible with 1.3 use $checkout->checkout_form_field instead:
	 
	 $checkout->checkout_form_field( 'my_field_name', array( 
	 	'type' 			=> 'text', 
	 	'class' 		=> array('my-field-class orm-row-wide'), 
	 	'label' 		=> __('Fill in this field'), 
	 	'placeholder' 	=> __('Enter a number'),
	 	));
	 **/
	woocommerce_form_field( 'my_field_name', array( 
		'type' 			=> 'text', 
		'class' 		=> array('my-field-class orm-row-wide'), 
		'label' 		=> __('Fill in this field'), 
		'placeholder' 	=> __('Enter a number'),
		), $checkout->get_value( 'my_field_name' ));
	
	echo '</div>';
	
	/**
	 * Optional Javascript to limit the field to a country. This one shows for italy only.
	 **/
	?>
	<script type="text/javascript">
		$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><input type="text" name="my_field_name[]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
	</script>
	<?php
}
/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
function my_custom_checkout_field_process() {
	global $woocommerce;
	
	// Check if set, if its not set add an error. This one is only requite for companies
	if ($_POST['billing_company'])
		if (!$_POST['my_field_name']) 
			$woocommerce->add_error( __('Please enter your XXX.') );
}
/**
 * Update the user meta with field value
 **/
add_action('woocommerce_checkout_update_user_meta', 'my_custom_checkout_field_update_user_meta');
function my_custom_checkout_field_update_user_meta( $user_id ) {
	if ($user_id && $_POST['my_field_name']) update_user_meta( $user_id, 'my_field_name', esc_attr($_POST['my_field_name']) );
}
/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');
function my_custom_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['my_field_name']) update_post_meta( $order_id, 'My Field', esc_attr($_POST['my_field_name']));
}
/**
 * Add the field to order emails
 **/
add_filter('woocommerce_email_order_meta_keys', 'my_custom_checkout_field_order_meta_keys');
function my_custom_checkout_field_order_meta_keys( $keys ) {
	$keys[] = 'My Field';
	return $keys;
}