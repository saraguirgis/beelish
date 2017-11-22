<?php /* Template Name: CustomPageT1 */ 

date_default_timezone_set('America/Los_Angeles');

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

//test Tuesday deadline
$deliveryTimestamp = (strtotime('2017-11-30'));
echo '<br>var dump $deliveryTimestamp: ' . var_dump($deliveryTimestamp);
echo '<br>Day of the week of $deliveryTimestamp: ' . date( N , $deliveryTimestamp);

$deadlineTimestamp = strtotime("last Sunday", $deliveryTimestamp);

echo '<br>Last Sunday timestamp: ' . $deadlineTimestamp;
echo '<br>Last Sunday formatted as date: ' . date('l jS \of F Y h:i:s A, T', $deadlineTimestamp);

// set time to 9pm
//75600 = 60 * 21 * 60 (aka 9pm)
$deadlineTimestamp = $deadlineTimestamp + 75600;

echo '<br>$deadlineTimeStamp formatted as date: ' . date('l jS \of F Y h:i:s A', $deadlineTimestamp);

$resultDateTimeTest = new DateTime;
$resultDateTimeTest->setTimestamp($deadlineTimestamp);
echo '<br>';

var_dump($resultDateTimeTest);

echo '<br>';


//test IF FUNCTION
if (date( N , $deliveryTimestamp) == 2) {
	
	echo '<br>the date is a tuesday';

} else {
	
	echo '<br>the date is not a tuesday';
}			



//test displaying order info

$filters = array(
    'post_status' => 'any',
    'post_type' => 'shop_order',
    'posts_per_page' => 200,
    'paged' => 1,
    'orderby' => 'modified',
    'order' => 'ASC'
);

$loop = new WP_Query($filters);

while ($loop->have_posts()) {
    $loop->the_post();
    $order = new WC_Order($loop->post->ID);

    foreach ($order->get_items() as $key => $lineItem) {
		        //uncomment the following to see the full data
        //        echo '<pre>';
        //       print_r($lineItem);
        //        echo '</pre>';
        echo '<br>' . 'Product Name : ' . $lineItem['name'] . '<br>';
		echo 'Product ID : ' . $lineItem['product_id'] . '<br>';
		echo 'Qty : ' . $lineItem['qty'] . '<br>';
		echo 'Order ID : ' . $lineItem['order_id'] . '<br>';
        if ($lineItem['variation_id']) {
            echo 'Product Type : Variable Product' . '<br>';
        } else {
            echo 'Product Type : Simple Product' . '<br>';
		}
		//displays the formatted metadata
		wc_display_item_meta( $lineItem );
    }
}





// test post excerpts (short description for meals)
$_product = wc_get_product( '975' );
$shortdesc = $_product->post->post_excerpt;
echo $shortdesc . "<br><br>" ;

echo "This is the hover test: <a href=\"http://beelish.com/\" title=\"". $shortdesc . "\">link</a><br><br>";

echo "This is the current time: " . time() . "<br />";


//test other stuff...
	global $woocommerce;
	
	
	$alreadyorderedforname = array();
	$alreadyorderedforname = names_meal_ordered_for('509');
	
	if ($alreadyorderedforname != NULL) {
		foreach ($alreadyorderedforname as $key => $val) {
			echo "Purchased for " . $val . "<BR />";
		}
	}		

	
	echo "this is the permalink for page 257: " . get_permalink(257) . PHP_EOL;

	echo "<br>Constant TableHeaderBGColor: " . ThemeConstants::TableCellHeaderBGColor . "<BR />";


	$timezone = date_default_timezone_get();
	echo "The current server timezone is: " . $timezone;
	
	$date = date('m/d/Y h:i:s a', time());
	echo "<br>current time is: " . $date;




	$productSku = "2017-10-30";

	$resultTimestamp = strtotime("last Tuesday", $productSku);
	$resultTimeStampFormatted = date('m/d/Y h:i:s a', $resultTimestamp);

	echo "<br /><br /><br />";
	$theTime = time();
	echo "time is $theTime<br />";
	echo "resulttimestamp $resultTimestamp<br />";
	echo "resultTimeStampFormatted $resultTimeStampFormatted<br />";
	


	// echo "productSku = " . $productSku . " <BR/>";
	// echo "productsku timestamp = " . date("F j, Y, g:i a", strtotime($productSku)) . " <BR/>";
	
	$orderLateDateTime = getLateOrderDeadline(strtotime($productSku));
	$orderTooLateDateTime = getTooLateOrderDeadline(strtotime($productSku));

	$logPrefix = "[$productSku]: ";
	$currentTime = date('m/d/Y h:i:s a', time());
	$tooLateTime = $orderTooLateDateTime->format('m/d/Y h:i:s a');
	debug_to_console("$logPrefix Time Is: " . $currentTime);
	debug_to_console("$logPrefix orderTooLateDateTime->getTimestamp(): " . $tooLateTime);
	$tempIsTooLate = (time() > $orderTooLateDateTime->getTimestamp()) ? "true" : "false";
	debug_to_console("$logPrefix time > order too late is: $tempIsTooLate");


	if (time() > $orderTooLateDateTime->getTimestamp()) {
		echo "current time is larger than too late date <BR />";
	}
	

	function getTooLateOrderDeadline($deliveryTimestamp) {
        
        $businessDaysToSubtract = BusinessConfigs::ChangesDeadlineInBusinessDays;

        $resultDeadlineTimestamp = $deliveryTimestamp;

        while ($businessDaysToSubtract > 0) {
            $resultDeadlineTimestamp = strtotime("yesterday", $resultDeadlineTimestamp);

            // only make it count if it was a business day
            if (!TimeHelpers::isWeekend($resultDeadlineTimestamp)
             && !TimeHelpers::isHoliday(BusinessConfigs::Holidays, $resultDeadlineTimestamp)) {
                $businessDaysToSubtract--;
            }
        }

        // set time to noon
        $resultDateTime = DateTime::createFromFormat('U', $resultDeadlineTimestamp);
        $resultDateTime->setTime(19, 00);

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
		$resultDateTime->setTime(19, 00);

		echo "after setting the time, order late is : <b>" . $resultDateTime->format("F j, Y, g:i a") . "</b><BR/>";

		return $resultDateTime;
	}

	showChildShortName();

	function showChildShortName() {
		global $children;
		echo "ChildName: " . $children[1]->getChildShortDisplayName() . " <BR><BR><BR>";
	}

?>		
			
			
<?php
// test displaying order deadline
	
	$displaydeadline = strtotime("next Tuesday");
	$displayorderweekstart = strtotime("+6 days", $displaydeadline);
	$displayorderweekend = strtotime("+4 days", $displayorderweekstart);
	
	echo "<h5>Next Ordering Deadline:</h5> Order meals by <b><font color=\"#3ab44a\"> ". date("D, M j", $displaydeadline ) . 
		" at noon</b></font> for the week of <b>" . date("M j", $displayorderweekstart ) . " - " . date("M j", $displayorderweekend ) . "</b> <BR><BR><BR>";

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




	
	

