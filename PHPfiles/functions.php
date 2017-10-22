<?php
include "Constants.php";
include "BusinessConfigs.php";
include "ChildDetails.php";

$current_user = wp_get_current_user();
$children = getChildArray();

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
		
		if ( ! session_id() ) {
			session_name( 'LIVE_ORDERS_SESSION' );
		}

		WC()->session->set( 'selectedChildId' , $_POST['childIdDropDown']);
	}

	global $children;
	$selectedChildId = WC()->session->get('selectedChildId') ?: 1;

	// validate that selected child id is a valid child
	if ($children[$selectedChildId] == NULL || !$children[$selectedChildId]->isValid()) {
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
/***************** NO LONGER USED *****************/ 
function meal_already_bought($mealID) {
		$current_user = wp_get_current_user();
		if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, "$mealID" ) ) 
			return TRUE;
}
/***************** NO LONGER USED *****************/






function names_meal_ordered_for($mealID) {
    
    // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1, //($numberposts ( int ) optional – Total number of posts to retrieve. Is an alias of $posts_per_page in WP_Query. Accepts -1 for all. Default 5.
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => 'shop_order', // WC orders post type
        'post_status' => array( 'wc-processing', 'wc-completed' ) // Only orders with status "completed" or "processing"
    ) );

	$PurchasedForNames = array();
		
    foreach ( $customer_orders as $customer_order ) {
        // Updated compatibility with WooCommerce 3+
        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        $order = wc_get_order( $customer_order );

		
        // Iterating through each current customer products bought in the order
        foreach ($order->get_items() as $item) {
            // WC 3+ compatibility
            if ( version_compare( WC_VERSION, '3.0', '<' ) ) 
                $product_id = $item['product_id'];
            else
                $product_id = $item->get_product_id();
			
            // If the mealID matches to the product ID in the order, add the name of the child to the array
            if ( $product_id == $mealID ) {
                $PurchasedForNames[] = $item['For'];
			}
        }
    }
	
    // return the names of the children for which this meal has been purchased
    return $PurchasedForNames;
}




/* See if the product is in the cart. */

function meal_in_cart($mealID) {
    foreach(WC()->cart->get_cart() as $key => $val ) {
        $_product = $val['data'];
		$selectedChildId = getSelectedChildId();

        if("$mealID" == $_product->id && $selectedChildId == $val['Child_ID'] ) {
            return true;
        }
    }
    return false;
}


/* Remove a meal from the cart because it's ordering period has expired. */
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
		//Commented out this line since for multiple children ordering, we now want meals that are already bought to still be able to be purchased.
		//} elseif ( meal_already_bought(get_the_ID()) || meal_in_cart(get_the_ID()) ) {
		} elseif ( meal_in_cart(get_the_ID()) ) {
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
		global $woocommerce;
		$cart_url = $woocommerce->cart->get_cart_url();
		echo '<font color="orange"><em><b>This meal is in your cart for the selected child.  <br><a href="' . get_permalink('205') . '">Add another meal</a> or <a href="' . $cart_url . '">check out</a> to complete your order.</b></em></font>';
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
		
		global $children;
		$selectedChildId = getSelectedChildId();
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



/* Remove Order Notes from checkout field in Woocommerce
 * source https://clicknathan.com/web-design/remove-order-notes-woocommerce/
 */

	add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 1);

	add_filter( 'woocommerce_checkout_fields' , 'alter_woocommerce_checkout_fields' );

	function alter_woocommerce_checkout_fields( $fields ) {
		 //removing order comments with first filter so that "additional information" heading can be removed
		 //unset($fields['order']['order_comments']);
		 unset($fields['billing']['billing_company']);
		 return $fields;
	}


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
	$cart_url = $woocommerce->cart->get_cart_url();
	$return_to  = get_permalink(205);
	$message    = sprintf('<a href="%s" class="button wc-forwards">%s</a> %s', $return_to, __('Add another meal', 'woocommerce'), 
					__('Product successfully added to your cart.  Add another meal or <a href=\"' . $cart_url . '\"><b>' . 'check out</a></b> to complete your order.', 'woocommerce') );
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


/***************ADD CUSTOM META TO CART & ORDER ITEMS ****************************************	
/**
 * @sourcecode    https://iconicwp.com/blog/add-custom-cart-item-data-woocommerce/
 *
 * Add ChildID to cart item
 * @param array $cart_item_data
 * @param int   $product_id
 * @param int   $variation_id
 *
 * @return array
 */

 function iconic_add_childid_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
    global $children;
	$childid = getSelectedChildId();
 	 
    $cart_item_data['Child_ID'] = $childid;
 
    return $cart_item_data;
 }
 
 add_filter( 'woocommerce_add_cart_item_data', 'iconic_add_childid_to_cart_item', 10, 3 );


 /**
 * Display childID in the cart.
 *
 * @param string $product_name
 * @param array  $cart_item
 * @param string $cart_item_key
 *
 * @return string
 */
function iconic_display_child_id_cart( $product_name, $cart_item, $cart_item_key ) {
    if ( empty( $cart_item['Child_ID'] ) ) {
        return $product_name;
	}
	
	global $children;
	$cartChildId = $cart_item['Child_ID'];

    return sprintf( '%s <p><strong>%s</strong>: %s</p>', $product_name, __( 'For', 'iconic' ), $children[$cartChildId]->getChildShortDisplayName() );
}
 
add_filter( 'woocommerce_cart_item_name', 'iconic_display_child_id_cart', 10, 3 );


/**
 * Add child name & class to order items.
 *
 * @param WC_Order_Item_Product $item
 * @param string                $cart_item_key
 * @param array                 $values
 * @param WC_Order              $order
 */
 
function iconic_add_childid_to_order_items( $item, $cart_item_key, $values, $order ) {
	global $children;
	
	// For order items, we will not add the child id as the meta data.  The actual child and class name should
	// be added so that the user can show previous orders correctly even after updating current account info
	// and children names

	$orderChildId = $values['Child_ID'];
	$childName = $children[$orderChildId]->getChildShortDisplayName();
 
    $item->add_meta_data( __( 'For', 'iconic' ), $children[$orderChildId]->getChildShortDisplayName() );
}
 
add_action( 'woocommerce_checkout_create_order_line_item', 'iconic_add_childid_to_order_items', 10, 4 );	




/**
 * Always redirect shop page to order page
 *
 * Source: https://stackoverflow.com/questions/40382876/forwarding-woocommerce-shop-page-to-another-page-on-a-site
 */

 function custom_shop_page_redirect() {
    if( is_shop() ){
        wp_redirect( get_permalink(205) );
        exit();
    }
 }

add_action( 'template_redirect', 'custom_shop_page_redirect' );


?>
