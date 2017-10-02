<?php /* Template Name: Order */ 

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
		the_title( '<h1 class="entry-title">', '</h1>' );
	?>  
	  
    </div>
  </div>

  <?php echo onepress_breadcrumb(); ?>

    <div id="content-inside" class="container"
    <?php echo esc_attr( $layout ); ?>">
    <div id="primary" class="content-area">
      <main id="main" class="site-main" role="main">

	  
	<!-- Welcome message to user just above menu -->
	  	<?php
			echo '<p align="right">Welcome, ' . $current_user->first_name . '!</p>'; 
		?>
	

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
					
	
	<!-- Menu Table -->

<?php
  $octoberProducts = array(
		"10/02/2017" => ProductCalendar::NoLunchProductId,
		"10/03/2017" => ProductCalendar::NoLunchProductId,
		"10/04/2017" => ProductCalendar::NoLunchProductId,
		"10/05/2017" => ProductCalendar::NoLunchProductId,
		"10/06/2017" => ProductCalendar::NoLunchProductId,

		"10/09/2017" => ProductCalendar::NoLunchProductId,
		"10/10/2017" => ProductCalendar::NoLunchProductId,
		"10/11/2017" => ProductCalendar::NoLunchProductId,
		"10/12/2017" => ProductCalendar::NoLunchProductId,
		"10/13/2017" => ProductCalendar::NoLunchProductId,

		"10/16/2017" => ProductCalendar::NoLunchProductId,
		"10/17/2017" => ProductCalendar::NoLunchProductId,
		"10/18/2017" => ProductCalendar::NoLunchProductId,
		"10/19/2017" => ProductCalendar::NoLunchProductId,
		"10/20/2017" => ProductCalendar::NoLunchProductId,

		"10/23/2017" => 401,
		"10/24/2017" => 401,
		"10/25/2017" => 401,
		"10/26/2017" => 401,
		"10/27/2017" => 401,

		"10/30/2017" => 448,
		"10/31/2017" => 401,
		"11/01/2017" => ProductCalendar::NoDetailsProductId,
		"11/02/2017" => ProductCalendar::NoDetailsProductId,
		"11/03/2017" => ProductCalendar::NoDetailsProductId,
);

$novemberProducts = array(
		"10/30/2017" => ProductCalendar::NoDetailsProductId,
		"10/31/2017" => ProductCalendar::NoDetailsProductId,
		"11/01/2017" => 401,
		"11/02/2017" => 401,
		"11/03/2017" => 401,

		"11/06/2017" => 401,
		"11/07/2017" => 401,
		"11/08/2017" => 401,
		"11/09/2017" => 401,
		"11/10/2017" => 401,

		"11/13/2017" => 401,
		"11/14/2017" => 401,
		"11/15/2017" => 401,
		"11/16/2017" => 401,
		"11/17/2017" => 401,

		"11/20/2017" => 401,
		"11/21/2017" => 401,
		"11/22/2017" => 401,
		"11/23/2017" => ProductCalendar::NoLunchProductId,
		"11/24/2017" => ProductCalendar::NoLunchProductId,

		"11/27/2017" => 401,
		"11/28/2017" => 401,
		"11/29/2017" => 401,
		"11/30/2017" => 448,
		"12/01/2017" => ProductCalendar::NoDetailsProductId,
);

$decemberProducts = array(
		"11/27/2017" => ProductCalendar::NoDetailsProductId,
		"11/28/2017" => ProductCalendar::NoDetailsProductId,
		"11/29/2017" => ProductCalendar::NoDetailsProductId,
		"11/30/2017" => ProductCalendar::NoDetailsProductId,
		"12/01/2017" => 448,

		"12/04/2017" => 401,
		"12/05/2017" => 401,
		"12/06/2017" => 401,
		"12/07/2017" => 401,
		"12/08/2017" => 401,

		"12/11/2017" => 401,
		"12/12/2017" => 401,
		"12/13/2017" => 401,
		"12/14/2017" => 401,
		"12/15/2017" => 401,

		"12/18/2017" => 401,
		"12/19/2017" => 401,
		"12/20/2017" => 401,
		"12/21/2017" => 401,
		"12/22/2017" => 401,

		"12/25/2017" => ProductCalendar::NoLunchProductId,
		"12/26/2017" => ProductCalendar::NoLunchProductId,
		"12/27/2017" => 401,
		"12/28/2017" => 401,
		"12/29/2017" => 401,
);

$octoberCalendar = new ProductCalendar("October 2017", $octoberProducts);
$octoberCalendar->renderCalendar();

$novemberCalendar = new ProductCalendar("November 2017", $novemberProducts);
$novemberCalendar->renderCalendar();

$decemberCalendar = new ProductCalendar("December 2017", $decemberProducts);
$decemberCalendar->renderCalendar();
?>
	  
      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>