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

$_product = wc_get_product( '975' );
$shortdesc = $_product->post->post_excerpt;
echo $shortdesc . "<br><br>" ;

echo "This is the hover test: <a href=\"http://beelish.com/\" title=\"". $shortdesc . "\">link</a><br><br>";




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




	
	

