<?php /* Template Name: CustomPageT1 */ 

//errors out because of declaring theme constants
	//include "Constants.php";

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




	
	

