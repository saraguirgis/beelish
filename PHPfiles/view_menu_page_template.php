<?php /* Template Name: ViewMenu */ 

get_header();

$layout = onepress_get_layout();

include "ProductCalendar.php";

?>

<div id="content" class="site-content">

  <div class="page-header">
    <div class="container">
	<?php  
		//page title
		the_title( '<h1 class="entry-title">', '</h1>' );
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
	


// display ordering information link
	
	echo "<i class=\"fa fa-star\" aria-hidden=\"true\"></i> Please <b><a href=\"http://www.beelish.com/wms\">LOG IN</a></b> to order meals
	<br><br>Find order deadlines, meal pricing, and other info at <b><a href=\"http://www.beelish.com/ordering-info\">Ordering Information</a></b>
	<br><font size=\"-1\"><em><b><a href=\"http://www.beelish.com/contact-us\">Contact us</a></b> to discuss adding your school as a Beelish delivery location</font></em><br><br>";

// Display Menu Tables

$december2017Calendar = new ProductCalendar("December 2017", BusinessConfigs::DecemberProducts, $selectedChildId);
$december2017Calendar->renderMenuView();
?>
	  
      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>