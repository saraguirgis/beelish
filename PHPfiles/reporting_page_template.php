<?php /* Template Name: OrderReporting */ 

	if ( is_user_logged_in() ) {
		get_header();
	} else {
		header("Location: /wms");
		die();
	}

$layout = onepress_get_layout();

include "ProductCalendar.php";

?>

<div id="content" class="site-content">

  <div class="page-header">
    <div class="container">
    <?php  
		// Welcome message to user using their first name in the page title bar
		echo "<p style=\"text-align:right;\">Welcome, " . $current_user->first_name . "!<span style=\"float:left;\">";
		//page title
		the_title( '<h1 class="entry-title">', '</h1>' );
		echo "</span></p>";
?>  
	  
    </div>
  </div>

  <?php echo onepress_breadcrumb(); ?>

    <div id="content-inside" class="container"
    <?php echo esc_attr( $layout ); ?>">
    <div id="primary" class="content-area">
      <main id="main" class="site-main" role="main">

	  
	<!-- Any text in WP Admin Page -->
	  
					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'template-parts/content', 'page' ); ?>

						<?php
							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;
						?>

					<?php endwhile; // End of the loop. ?>  
					
	<?php 
		
		/*

WORK LEFT TO DO:
 1 - ADD DATE DROP DOWN TO PAGE AND SET IT TO VARIABLE $LOOKUPDATE
 1 - ADD BUTTON TO RUN SEARCH FUNCTION AND DISPLAY RESULTS
 1 - TAKE RETURNS INTO ACCOUNT
 2 - GET DRINKS AND SIDES TO SHOW UP
 3 - GROUP BY SITE
 3 - GROUP BY CLASS
 3 - PUT RESULTS INTO TABLE
 4 - EXPORT TO PDF OR EXCEL

*/
		
		
		/* GET ORDER ITEM METADATA */
		if ( !function_exists( 'wc_get_order_item_meta' ) ) { 
			require_once '/includes/wc-order-item-functions.php'; 
		} 
		
		// The item id. 
		$item_id = 050; 
		
		// The key. 
		$key = null; 
		
		// (default: true) 
		$single = true; 
		
		$results = wc_get_order_item_meta($item_id, $key, $single); 
		var_dump($results);
		echo "<br><br>";
		print_r($results);

		?>
		
		<br>
		<br>

		<?php if (count($results) > 0): ?>
		<table>
		  <thead>
			<tr>
			  <th><?php echo implode('</th><th>', array_keys(current($results))); ?></th>
			</tr>
		  </thead>
		  <tbody>
		<?php foreach ($results as $row): array_map('htmlentities', $row); ?>
			<tr>
			  <td><?php echo implode('</td><td>', $row); ?></td>
			</tr>
		<?php endforeach; ?>
		  </tbody>
		</table>
		<?php endif;


	$filters = array(
		'post_status' => 'wc-processing',
		'post_type' => 'shop_order',
		'posts_per_page' => -1,
		//'paged' => 1,
		//'orderby' => 'modified',
		//'order' => 'ASC'
		);

	$loop = new WP_Query($filters);
	
	$lookupdate = '2017-11-17';

	echo '<h1>Orders for ' . date("F j, Y",strtotime($lookupdate)) . '</h1>';
	
	while ($loop->have_posts()) {
  	  $loop->the_post();
  	  $order = new WC_Order($loop->post->ID);

  	foreach ($order->get_items() as $key => $lineItem) {

        //uncomment the following to see the full data
        //       echo '<pre>';
        //       print_r($lineItem);
        //       echo '</pre>';
		$deliverydate = get_post_meta( $lineItem['product_id'], '_sku', true );
		if 	($lookupdate == $deliverydate) {
			echo '<br>';
			//echo 'Delivery Date : ' . $deliverydate . '<br>';
			echo 'For : ' . $lineItem['For'] . '<br>';
			//echo 'For using get_meta function : ' . $lineItem->get_meta( 'For' ) . '<br>';
			echo 'Product Name : ' . $lineItem['name'] . '<br>';
			//echo 'Delivery Name : ' . '<br>';
			//echo 'Delivery Class : ' . '<br>';
			echo 'Size : ' . $lineItem['pa_size'] . '<br>';
			echo 'Type : ' . $lineItem['pa_veggie'] . '<br>';
			echo 'Drinks : ' . $lineItem['Drinks ($1.50)'] . '<br>';
			echo 'Sides : ' . $lineItem['Sides ($2.00)'] . '<br>';
			//echo 'Product ID : ' . $lineItem['product_id'] . '<br>';
			//echo 'Order Item ID : ' . $key . '<br>';
			//print_r($lineItem->get_data());
		}
    }
}

?>


      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>