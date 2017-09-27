<?php /* Template Name: CustomPageT1 */ 


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

			
			
			<?php //testing getting product attributes
				$_product = wc_get_product('170');
				echo nl2br ($_product->get_image( array( 80, 128 ) ) . PHP_EOL . "<a href='" . $_product->get_permalink() . "'>" . $_product->get_title() . "</a>" . PHP_EOL );
			?>

<?php
	$testdatedeadline = Check_Timing(392);
	echo "Testing Return: [" . date('Y-m-d', $testdatedeadline) . "]";
?>
			
			
          
      <?php //creating custom attribute for late or toolate designations and pulling them again
        $prodID = '170';
		$_product = wc_get_product($prodID);
		
        update_post_meta($prodID, 'timing_key', 'late');
        echo '<p>' . get_post_meta($prodID, 'timing_key', True);

        //Is it set to late already?
        $product_set_to_late = get_post_meta($prodID, 'timing_key', True) == 'late';
        echo '<p>Is product already set to late? ' . $product_set_to_late;

		//FOR SETTING TO LATE FOR FIRST TIME: adjust qty available for parent product to 50 if it's more than 50
		echo '<p>Items in stock before: ' . $_product->get_stock_quantity();
		if ($_product->get_stock_quantity() > 50) {
			wc_update_product_stock($prodID, 50);
			echo '<p>Items in stock after: ' . wc_update_product_stock($prodID);
			} else {
			echo '<p>No change to stock qty'; }
		  
		//FOR SETTING TO LATE FOR FIRST TIME: getting and setting price
        $var1_prodID = $prodID + 1;
        $var2_prodID = $prodID + 2;
          //Variant1 **(DO SAME FOR VARIANT2)**
          echo '<p>' . $var1_prodID;
          $_product = wc_get_product($var1_prodID);
          $RegPrice = $_product->get_regular_price();
          echo '<p>Regular Price: ' . $RegPrice . PHP_EOL;
          $lateprice = $RegPrice + 1;
          echo '<p>Late Price: ' . $lateprice;
		  update_post_meta($var1_prodID, '_regular_price', $lateprice);
          wc_delete_product_transients( $var1_prodID );
      ?>
          

      
          
      <?php //testing setting deadline and comparing current date
			
			//sets default timezone
				date_default_timezone_set('America/Los_Angeles');
			
				//sets product ID
				$_product = wc_get_product('170');
				//displays product SKU
				echo "<p>Product SKU: " . $_product->get_SKU() . PHP_EOL;
			
			//assign SKU/delivery date to variable
				$mealdate_strtotime = strtotime($_product->get_SKU());
			
			
				echo "<p>Meal date time: " . $mealdate_strtotime . PHP_EOL;
				echo "<p>Current time: " . time(). PHP_EOL;
				echo "<p>Current time minus 60x60x24: " . (time()-(60*60*24)) . PHP_EOL;
				echo "<p>Current time minus meal date time: " . (time()-$mealdate_strtotime) . PHP_EOL;
				echo "<p>Current time minus meal date time in days: " . (time()-$mealdate_strtotime)/(60*60*24) . PHP_EOL;
				//strtotime sets the time to zero, so the very very beginning of the day
				echo "<p>Today's date with no time in time: " . strtotime('2017-09-16') . PHP_EOL;
				echo "<p>Last Tuesday: " . date('Y-m-d' ,strtotime("last Tuesday")) . PHP_EOL;
				//get week number
				echo '<p>Week number of meal date: ' . date('W',$mealdate_strtotime) . PHP_EOL;
				echo '<p>Week number of meal date minus one: ' . $deadlineweek . PHP_EOL;
			
			
			//find week of order deadline
				$deadlineweek = (date('W',$mealdate_strtotime)-1);
			
			//set day of order deadline
				$deadline = new DateTime();
				$deadline->setISODate(date("Y"), $deadlineweek);
			
			//set time of order deadline
				$deadline->setTime(36, 00);
				echo '<p>Order deadline: ' . $deadline->format('Y-m-d H:i:s') . PHP_EOL;
			
			//is today before the order deadline?
				if (new datetime() < $deadline) {
					$ontime = TRUE;
				} else {
					$ontime = FALSE;
				}
				echo '<p> Is this order on time? ' . $ontime . PHP_EOL;

			//what is the change deadline and are we within it?
				
				//account for weekends
				if (date('w',$mealdate_strtotime) < 3) {
					$changedeadline = ($mealdate_strtotime-302400);
				} else {
					$changedeadline = ($mealdate_strtotime-129600);
				}
				
				//set holidays
				$holidays=array("2017-11-23","2017-12-25","2017-12-24","2018-01-01");
				
				//Subtract a day for the holidays
				foreach($holidays as $holiday){
					$time_stamp=strtotime($holiday);
					//If the holiday doesn't fall in weekend, move back change deadline by a day
					if ($changedeadline <= $time_stamp && $time_stamp <= $mealdate_strtotime && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7) {
						$changedeadline = $changedeadline-86400;
					}
				}
				
				//display change deadline
				echo '<p>Change deadline: ' . date('Y-m-d H:i:s',$changedeadline) . PHP_EOL;
				
				//is today after the change deadline?
				if (time() > $changedeadline) {
					$toolate = TRUE;
				} else {
					$toolate = FALSE;
				}
				echo '<p>Is this order too late? ' . $toolate . PHP_EOL;
			?>
			
			
		<?php		
		/* subtracting holidays
		//set holidays
				$holidays=array("2017-12-25","2017-12-24","2018-01-01");
				
		//Subtract a day for the holidays
			foreach($holidays as $holiday){
				$time_stamp=strtotime($holiday);
				//If the holiday doesn't fall in weekend, move back change deadline by a day
				if (time() <= $time_stamp && $time_stamp <= $changedeadline && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7) {
					$changedeadline = $changedeadline-86400;
				}
			}

		*/
		?>
				
				
					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'template-parts/content', 'page' ); ?>

						<?php
							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;
						?>

					<?php endwhile; // End of the loop. ?>

					
				<?php //displays welcome message depending on logged in state
				if ( is_user_logged_in() ) {
					echo '<span>Welcome, ' .$current_user->first_name;
				} else {
					echo '<span>Welcome, visitor!</span>';
				}
				?>
			
			
			
			
			<?php //displays the product using the shortcode
			echo do_shortcode('[product id="170"]'); ?>
			
			
			

			
					
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




	
	

