<?php /* Template Name: Welcome_School */ 

	if ( is_user_logged_in() ) {
		header("Location: /order/");
		die();
	} else {
		get_header();
	}

$layout = onepress_get_layout();

?>

<div id="content" class="site-content">

  <div class="page-header">
    <div class="container">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </div>
  </div>

  <?php echo onepress_breadcrumb(); ?>

  
  <div id="content-inside" class="container"
    <?php echo esc_attr( $layout ); ?>">
    <div id="primary" class="content-area">
      <main id="main" class="site-main" role="main">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'template-parts/content', 'page' ); ?>

						<?php
							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;
						?>

					<?php endwhile; // End of the loop. ?>

					
	<!-- This is where text should go -->
  
	  
      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>