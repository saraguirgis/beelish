<?php
include "Constants.php";
include "BusinessConfigs.php";
include "ChildDetails.php";

$current_user = wp_get_current_user();

function getChildArray() {
	global $current_user;
	$child1 = new ChildDetails(1, $current_user->child1_fname, $current_user->child1_lname, $current_user->child1_class);
	$child2 = new ChildDetails(2, $current_user->child2_fname, $current_user->child2_lname, $current_user->child2_class);
	$child3 = new ChildDetails(3, $current_user->child3_fname, $current_user->child3_lname, $current_user->child3_class);
	
	$children = array();

	if ($child1->isValid()) {
		$children[1] = $child1;
	}

	if ($child2->isValid()) {
		$children[2] = $child2;
	}

	if ($child3->isValid()) {
		$children[3] = $child3;
	}

	return $children;
}


function getSelectedChildId() {
	// update session only if a new post is submitted
	if (isset($_POST['childIdDropDown'])) {
		$_SESSION['selectedChildId'] = $_POST['childIdDropDown'];		
	}

	global $current_user;
	$child1FirstName = $current_user->child1_fname;
	$child1LastName  = $current_user->child1_lname;
	$child2FirstName = $current_user->child2_fname;
	$child2LastName  = $current_user->child2_lname;
	$child3FirstName = $current_user->child3_fname;
	$child3LastName  = $current_user->child3_lname;

	$selectedChildId = $_SESSION['selectedChildId'] ?: 1;
	
	// validate that selected child id is a valid child
	if (($selectedChildId == 2 && $child2FirstName == NULL && $child2LastName == NULL)
	|| ($selectedChildId == 3 && $child3FirstName == NULL && $child3LastName == NULL) ||
	$selectedChildId > 3) {
		$selectedChildId = 1;
	}

	return $selectedChildId;
}
	
/* See if the product was already purchased.
 * Based on...
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=20910
 * @author        Rodolfo Melogli
 */

function meal_already_bought($mealID) {
		$current_user = wp_get_current_user();
		if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, "$mealID" ) ) 
			return TRUE;
}



/* See if the product is in the cart. */

function meal_in_cart($mealID) {
    foreach(WC()->cart->get_cart() as $key => $val ) {
        $_product = $val['data'];
 
        if("$mealID" == $_product->id ) {
            Return TRUE;
        }
    }
    return false;
}


/* Remove a meal from the cart because it's ordering period has expired. */
//TODO: update method to dynamically find product's children
function remove_expired_meal_from_cart($mealID) {
	global $woocommerce;
	
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item  ) {
            // Check to see if IDs match
            if( $cart_item['product_id'] == $mealID ) {
                //Remove it from cart
				WC()->cart->remove_cart_item($cart_item_key);
				echo "<em style=\"color: red;\">This item was removed from the cart since the ordering deadline has expired.</em>" . PHP_EOL;
			}
    }
}	



/**
 * @snippet       Add a note under the price if the order is late
 * @visual product page hooks guide	https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/#
*/

	add_filter( 'woocommerce_after_add_to_cart_form','late_order_notice_price', 30);
	
	function late_order_notice_price() {
		if (get_post_meta( get_the_ID(), 'timing_key', True) == ProductOrderTiming::KindaLate &&
		  BusinessConfigs::LatePenaltyChargeInDollars != 0) {
			echo nl2br ( "<font color=\"#FF6600\">Note: $" . BusinessConfigs::LatePenaltyChargeInDollars . " has been added to the meal price for late orders</font>" );
		}
	}



/**
 * @snippet       Removes the add to cart button on products that were previously bought or currently in the cart
 * @visual product page hooks guide	https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/#
*/
	
	add_action ( 'woocommerce_before_single_product','remove_add_to_cart_if_bought', 10, 2);
	
	function remove_add_to_cart_if_bought() {	
		//remove from cart if ordering is expired
		$productTimingKey = get_post_meta(get_the_ID(),'timing_key',True);
		
		if ( $productTimingKey == ProductOrderTiming::TooLate ) {
			remove_expired_meal_from_cart(get_the_ID());
			
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		} elseif ( meal_already_bought(get_the_ID()) || meal_in_cart(get_the_ID()) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

			add_action( 'woocommerce_single_product_summary', 'print_no_dup_orders', 31 );
			add_action( 'woocommerce_after_shop_loop_item', 'print_no_dup_orders', 11 );
		} else {
			// remove extra product data on the product page
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		}
	
	}
	
	function print_no_dup_orders() {
		echo '<em>Meal already purchased or in cart.</em>';
	}	
	
	
	
	


/**
 * @snippet       Remove Variable Product Prices Everywhere
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/disable-variable-product-price-range-woocommerce/
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 2.4.7
 */
 
	add_filter( 'woocommerce_variable_sale_price_html', 'bbloomer_remove_variation_price', 10, 2 );
	add_filter( 'woocommerce_variable_price_html', 'bbloomer_remove_variation_price', 10, 2 );
	
	function bbloomer_remove_variation_price( $price ) {
		$price = '';
		return $price;
	}

	add_filter( 'woocommerce_before_single_product', 'getAndDisplayChildName', 1, 2 );
	
	function getAndDisplayChildName() {
		$selectedChildId = 1;
		if (isset($_SESSION['selectedChildId'])) {
			$selectedChildId = $_SESSION['selectedChildId'];
		}
		
		$children = getChildArray();
		echo "<h3>Ordering for " . $children[$selectedChildId]->getChildSelectionName() . "</h3>";
	}


/**
 * @snippet       Remove irrelvant product data from product page
 * @visual product page hooks guide	https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/#
 */
		 
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
	remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
	remove_action( 'woocommerce_review_meta', 'woocommerce_review_display_meta', 10 );
	remove_action( 'woocommerce_review_comment_text', 'woocommerce_review_display_comment_text', 10 );


	
/**
 * @snippet       Edit "successfully added to your cart"
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=494
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.0.5
 */
 
	add_filter( 'wc_add_to_cart_message_html', 'bbloomer_custom_add_to_cart_message' );
	 
	function bbloomer_custom_add_to_cart_message() {
	 
	global $woocommerce;
	$return_to  = get_permalink(205);
	$message    = sprintf('<a href="%s" class="button wc-forwards">%s</a> %s', $return_to, __('Back to Menu', 'woocommerce'), __('Product successfully added to your cart.', 'woocommerce') );
	return $message;
}


/**
 * @snippet       WooCommerce Add New Tab @ My Account
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=21253
 * @credits       https://github.com/woothemes/woocommerce/wiki/2.6-Tabbed-My-Account-page
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 2.6.7
 */
 
 
	// ------------------
	// 1. Register new endpoint to use for My Account page
	// Note: Resave Permalinks or it will give 404 error
	
	function bbloomer_add_user_profile_endpoint() {
		add_rewrite_endpoint( 'user_profile', EP_ROOT | EP_PAGES );
	}
	
	add_action( 'init', 'bbloomer_add_user_profile_endpoint' );
	
	
	// ------------------
	// 2. Add new query var
	
	function bbloomer_user_profile_query_vars( $vars ) {
		$vars[] = 'user_profile';
		return $vars;
	}
	
	add_filter( 'query_vars', 'bbloomer_user_profile_query_vars', 0 );
	
	
	// ------------------
	// 3. Insert the new endpoint into the My Account menu
	
	function bbloomer_add_user_profile_link_my_account( $items ) {
		$items['user_profile'] = 'My Profile';
		return $items;
	}
	
	add_filter( 'woocommerce_account_menu_items', 'bbloomer_add_user_profile_link_my_account' );
	
	
	// ------------------
	// 4. Add content to the new endpoint
	
	function bbloomer_user_profile_content() {
	echo '<h3>My Profile</h3>';
	echo do_shortcode( '[wpmem_profile]' );
	}
	
	add_action( 'woocommerce_account_user_profile_endpoint', 'bbloomer_user_profile_content' );


?>
