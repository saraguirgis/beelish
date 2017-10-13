<?php /* Template Name: CustomPageT1 */ 

//errors out because of declaring theme constants
	//include "Constants.php";
	include "TimeHelpers.php";

	//update_post_meta(509, 'timing_key', ProductOrderTiming::OnTime);

get_header();

$layout = onepress_get_layout();
?>

	<div id="content" class="site-content">

		<div class="page-header">
			<div class="container">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</div>
		</div>

		<?php echo onepress_breadcrumb(); ?>

		<div id="content-inside" class="container 
			<?php echo esc_attr( $layout ); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">

			
			
<?php


	global $woocommerce;
	
	
	$alreadyorderedforname = array();
	$alreadyorderedforname = names_meal_ordered_for('509');
	
	if ($alreadyorderedforname != NULL) {
		foreach ($alreadyorderedforname as $key => $val) {
			echo "Purchased for " . $val . "<BR />";
		}
	}		

	
	echo "this is the permalink for this page: " . get_permalink(257) . PHP_EOL;

	echo "Constant TableHeaderBGColor: " . ThemeConstants::TableCellHeaderBGColor . "<BR />";
	
	
	$_product = wc_get_product('170');
	$MealVariationIDs = $_product->get_visible_children();
	
	foreach ($MealVariationIDs as $CurrentVariationID) {

		echo "Variation ID: $CurrentVariationID<BR />";
		$_product = wc_get_product($CurrentVariationID);
		$RegPrice = $_product->get_regular_price();

		echo "->Current Price: $RegPrice<BR />";

		$lateprice = $RegPrice + 1;
		update_post_meta($CurrentVariationID, '_regular_price', $lateprice);
		wc_delete_product_transients( $CurrentVariationID );
	
		$_product = wc_get_product($CurrentVariationID);
		echo "->Updated Price: " . $_product->get_regular_price() . "<BR />";
}

/*
	//Get and set new price
		$var1_prodID = $mealID + 1;
		$var2_prodID = $mealID + 2;
          
		//Variant1 Update
		$_product = wc_get_product($var1_prodID);
		$RegPrice = $_product->get_regular_price();
		$lateprice = $RegPrice + 1;
		update_post_meta($var1_prodID, '_regular_price', $lateprice);
		wc_delete_product_transients( $var1_prodID );
			
		//Variant2 Update
		$_product = wc_get_product($var2_prodID);
		$RegPrice = $_product->get_regular_price();
		$lateprice = $RegPrice + 1;
		update_post_meta($var2_prodID, '_regular_price', $lateprice);
		wc_delete_product_transients( $var2_prodID );
		
		//Reset Product Variable
		$_product = wc_get_product($mealID);
*/


	$productSku = "2017-10-23";
	// echo "productSku = " . $productSku . " <BR/>";
	// echo "productsku timestamp = " . date("F j, Y, g:i a", strtotime($productSku)) . " <BR/>";
	
	$orderLateDateTime = getLateOrderDeadline(strtotime($productSku));
	$orderTooLateDateTime = getTooLateOrderDeadline(strtotime($productSku));

	if (time() > $orderTooLateDateTime->getTimestamp()) {
		echo "current time is larger than too late date <BR />";
	}

	function getTooLateOrderDeadline($deliveryTimestamp) {
		echo " <BR/>";
		echo "deliveryTimeStamp = " . date("F j, Y, g:i a", $deliveryTimestamp) . " <BR/>";
        
        $businessDaysToSubtract = BusinessConfigs::ChangesDeadlineInBusinessDays;

        $resultDeadlineTimestamp = $deliveryTimestamp;

        while ($businessDaysToSubtract > 0) {
			$resultDeadlineTimestamp = strtotime("yesterday", $resultDeadlineTimestamp);
			
			echo "subtract one day gets me to: " . date("F j, Y, g:i a", $resultDeadlineTimestamp) . " <BR/>";

            // only make it count if it was a business day
            if (!TimeHelpers::isWeekend($resultDeadlineTimestamp)
             && !TimeHelpers::isHoliday(BusinessConfigs::Holidays, $resultDeadlineTimestamp)) {
				$businessDaysToSubtract--;
				echo "this was a business day!<BR />";
            }
        }

        // set time to noon
        $resultDateTime = DateTime::createFromFormat('U', $resultDeadlineTimestamp);
		$resultDateTime->setTime(12, 00);
		
		echo "after setting the time, Order Too Late is: <b>" . $resultDateTime->format("F j, Y, g:i a") . "</b><BR/>";		

        return $resultDateTime;
	}
	
	function getLateOrderDeadline($deliveryTimestamp) {
		echo " <BR/>";
		echo "deliveryTimeStamp = " . date("F j, Y, g:i a", $deliveryTimestamp) . " <BR/>";

		$resultTimestamp = $deliveryTimestamp;
		// if Wed-Friday, go to the previous tuesday which would be in the current week
		if (date('w', $deliveryTimestamp) >= TimeHelpers::Wednesday) {
			$resultTimestamp = strtotime("last Tuesday", $deliveryTimestamp);
			echo "It's past tuesday.  Going to tuesday of current week gets me to: " . date("F j, Y, g:i a", $resultTimestamp) . " <BR/>";
		}
		
		// go to Tuesday of previous week
		$resultTimestamp = strtotime("last Tuesday", $resultTimestamp);
		echo "going to tuesday of previous week gets me to: " . date("F j, Y, g:i a", $resultTimestamp) . " <BR/>";

		// set time to noon
		$resultDateTime = DateTime::createFromFormat('U', $resultTimestamp);
		$resultDateTime->setTime(12, 00);

		echo "after setting the time, order late is : <b>" . $resultDateTime->format("F j, Y, g:i a") . "</b><BR/>";

		return $resultDateTime;
	}

	showChildShortName();

	function showChildShortName() {
		global $children;
		echo "ChildName: " . $children[1]->getChildShortDisplayName() . " <BR><BR><BR>";
	}

?>		
			
			
			
			

			
					
				<form action="action.php" method="post">
				 <p>Your name: <input type="text" name="name" /></p>
				 <p>Your age: <input type="text" name="age" /></p>
				 <p><input type="submit" /></p>
				</form>		
								
					
					
				</main><!-- #main -->
			</div><!-- #primary -->

            <?php if ( $layout != 'no-sidebar' ) { ?>
                <?php get_sidebar(); ?>
            <?php } ?>

		</div><!--#content-inside -->
	</div><!-- #content -->

<?php get_footer(); ?>	




	
	

