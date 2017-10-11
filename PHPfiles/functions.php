<?php
include "Constants.php";
include "BusinessConfigs.php";

/* Display product in calendar menu view */

function display_meal($mealID) {
	echo "<td style=\"width: 20%; height: 100px; vertical-align: top;\"";	
	//put product data into variable
	$_product = wc_get_product($mealID);
	
	//CHECK TIMING AND DISPLAY PRODUCT INFO ACCORDINGLY:
	$changedeadline = Check_Timing($mealID);

	
	if (get_post_meta($mealID, 'timing_key', True) == 'toolate') {
		remove_expired_meal_from_cart($mealID);
		//display NO CLICK product info with background color
		echo nl2br ( " bgcolor=\"#D3D3D3\">" . $_product->get_image( array( 80, 128 ) ) . PHP_EOL . $_product->get_title() );
		echo nl2br ("<p><p style=\"text-align:center;\"><i> Ordering expired </i></p></p>");
	
	} elseif (get_post_meta($mealID, 'timing_key', True) == 'kindalate') {
		//display NO CLICK product info without background color and separate Order link to product
		echo nl2br ( ">" . $_product->get_image( array( 80, 128 ) ) . PHP_EOL . $_product->get_title() . "</a>" );
		If (meal_already_bought($mealID) || meal_in_cart($mealID)) {
			echo nl2br ( "<p><p style=\"text-align:center;\"><a href='" . $_product->get_permalink() . "' style=\"color: #9296A1;\"> View details</a></p></p>" );
		} else {
			echo nl2br ( "<p><p style=\"text-align:center;\"><a href='" . $_product->get_permalink() . "'><i class=\"fa fa-cutlery\" aria-hidden=\"true\"></i> Order</a><BR /></p>" .			
			"<p style=\"color: #FF6600;\"><i class=\"fa fa-clock-o fa-lg\" aria-hidden=\"true\"></i><i> Order last minute until <br>" . date('D, M d', $changedeadline) . " at noon </i></p></p>" );
		}
	} else {

		//display NO CLICK product info without background color and separate Order link to product
		echo nl2br ( ">" . $_product->get_image( array( 80, 128 ) ) . PHP_EOL . $_product->get_title() );
		If (meal_already_bought($mealID) || meal_in_cart($mealID)) {
			echo nl2br ( "<p><p style=\"text-align:center;\"><a href='" . $_product->get_permalink() . "' style=\"color: #9296A1;\"> View details</a></p></p>" );
		} else {
			echo nl2br ( "<p><p style=\"text-align:center;\"><a href='" . $_product->get_permalink() . "'><i class=\"fa fa-cutlery\" aria-hidden=\"true\"></i> Order</a></p></p>" );
		}
	}
		
	//DISPLAY PURCHASE NOTE: if meal is already bought, display that note
	if (meal_already_bought($mealID)) {
		$current_user = wp_get_current_user();
		if ($current_user->child1_name == NULL) {
			echo '<mark style="background-color:#95D79E;"><i class="fa fa-calendar-check-o" aria-hidden="true"></i><i> Meal ordered</i></mark>';
		} else {
			echo '<div class="user-bought"><i>&checkmark; ' . $current_user->child1_name . ' ordered this.</i></div>';
		}
	}
	
	//DISPLAY CART NOTE: if meal is in the cart, display that note
	if (meal_in_cart($mealID)) {
		echo '<mark><i class="fa fa-shopping-cart" aria-hidden="true"></i> Added to cart. Please <a href=\'https://www.beelish.com/checkout/\'><b>check out</b></a> to complete order.</mark>';
	}

	echo "</td>";
}
					
	

/* Check if too late to order (after noon two business days before delivery day) */

function Check_Timing($mealID) {
	//put product data into variable
	$_product = wc_get_product($mealID);

	//IF ALREADY TOO LATE, DON'T DO ANYTHING
	if (get_post_meta($mealID, 'timing_key', True) == 'toolate') {
		return;
	}
	
	if (get_post_meta($mealID, 'timing_key', True) == 'kindalate') {
		$changedeadline = Check_Timing_TooLateOnly($mealID);
	} else {
	//WHAT IS THE INITIAL ORDER DEADLINE?
	
		//sets default timezone
		date_default_timezone_set('America/Los_Angeles');
		
		//assign SKU (aka delivery date) to variable
		$mealdate = strtotime($_product->get_SKU());
		
		//find week of initial order deadline
		$deadlineweek = (date('W',$mealdate)-1);
				
		//set day of initial order deadline
		$deadline = new DateTime();
		$deadline->setISODate(date("Y"), $deadlineweek);
		
		//set time of initial order deadline
		$deadline->setTime(36, 00);

	//WHAT IS THE CHANGE WINDOW AND ARE WE WITHIN IT?

		//Account for weekends
		if (date('w',$mealdate) < 3) {
			$changedeadline = ($mealdate-302400);
		} else {
			$changedeadline = ($mealdate-129600);
		}
					
		//Set holidays
		$holidays = array("2017-11-23","2017-12-25","2017-12-24","2018-01-01");
					
		//Subtract a day for the holidays
		foreach($holidays as $holiday){
			$time_stamp=strtotime($holiday);
		
			//If the holiday doesn't fall in weekend, move back change deadline by a day
			if ($changedeadline <= $time_stamp && $time_stamp <= $mealdate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7) {
			$changedeadline = $changedeadline-86400;
			}
		}

	//IS RIGHT NOW BEFORE THE ORDER DEADLINES?
		if (new datetime() < $deadline) {
			$ontime = TRUE;
			
		} elseif (time() < $changedeadline) {
			//set custom variable to 'kindalate'
			update_post_meta($mealID, 'timing_key', 'kindalate');
			
			//Adjust qty available for parent product to 50 if it's more than 50
			if ($_product->get_stock_quantity() > 50) {
				wc_update_product_stock($mealID, 50);
				}
			
			//Get and set new price for each variation of the parent product
			$MealVariationIDs = $_product->get_visible_children();
		
			foreach ($MealVariationIDs as $CurrentVariationID) {
	
			$_product = wc_get_product($CurrentVariationID);
			$RegPrice = $_product->get_regular_price();
			$lateprice = $RegPrice + 1;
			update_post_meta($CurrentVariationID, '_regular_price', $lateprice);
			wc_delete_product_transients( $CurrentVariationID );
		
			}
		
		} else {
			update_post_meta($mealID, 'timing_key', 'toolate');
		}
	}

	return $changedeadline;
}


/* If it's already kinda late, check if it's now too late */

function Check_Timing_TooLateOnly($mealID) {
		//put product data into variable
		$_product = wc_get_product($mealID);
	
		//sets default timezone
		date_default_timezone_set('America/Los_Angeles');
		
		//assign SKU (aka delivery date) to variable
		$mealdate = strtotime($_product->get_SKU());
		
		//WHAT IS THE CHANGE WINDOW AND ARE WE WITHIN IT?
		
		//Account for weekends
		if (date('w',$mealdate) < 3) {
			$changedeadline = ($mealdate-302400);
		} else {
			$changedeadline = ($mealdate-129600);
		}
					
		//Set holidays
		$holidays = array("2017-11-23","2017-12-25","2017-12-24","2018-01-01");
					
		//Subtract a day for the holidays
		foreach($holidays as $holiday){
			$time_stamp=strtotime($holiday);
		
			//If the holiday doesn't fall in weekend, move back change deadline by a day
			if ($changedeadline <= $time_stamp && $time_stamp <= $mealdate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7) {
				$changedeadline = $changedeadline-86400;
			}
		}

	//IS RIGHT NOW BEFORE THE ORDER DEADLINES?
		if (time() > $changedeadline) {
			update_post_meta($mealID, 'timing_key', 'toolate');
		}
		
		return $changedeadline;
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
            if( $cart_item['product_id'] == $mealID || $cart_item['product_id'] == ($mealID + 1) || $cart_item['product_id'] == ($mealID + 2) ) {
                //Remove it from cart
				WC()->cart->remove_cart_item($cart_item_key);
				echo "This item was removed from the cart since the ordering deadline has expired." . PHP_EOL;
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
			echo nl2br ( "<font color=\"#FF6600\">Note: $" . BusinessConfigs::LatePenaltyChargeInDollars . " has been added for late orders</font>" );
		}
	}



/**
 * @snippet       Removes the add to cart button on products that were previously bought or currently in the cart
 * @visual product page hooks guide	https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/#
*/
	
	add_action ( 'woocommerce_before_single_product','remove_add_to_cart_if_bought');
	
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
