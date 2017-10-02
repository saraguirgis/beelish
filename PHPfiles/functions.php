<?php

/* Display product in calendar/menu view */

function display_meal($mealID) {
	echo "<td style=\"width: 20%; height: 100px; vertical-align: top;\"";	
	//put product data into variable
	$_product = wc_get_product($mealID);
	
	//CHECK TIMING AND DISPLAY PRODUCT INFO ACCORDINGLY:
	$changedeadline = Check_Timing($mealID);

//	USE THIS TO CLEAR THE TIMING_KEY VARIABLE WHEN NEEDED	
//	update_post_meta($mealID, 'timing_key', 'ontime');
	
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

function remove_expired_meal_from_cart($mealID) {
	global $woocommerce;
	
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item  ) {
            // Check to see if IDs match
            if( $cart_item['product_id'] == $mealID || $cart_item['product_id'] == ($mealID + 1) || $cart_item['product_id'] == ($mealID + 2) ) {
                //Remove it from cart
				WC()->cart->remove_cart_item($cart_item_key);
				echo "Item " . $mealID . " was removed from the cart." . PHP_EOL;
			} else {
				echo "Item not found in cart." . PHP_EOL;
			}
    }
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

					
?>