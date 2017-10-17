<?php /* Template Name: RegTOSRedirect */ 

/*  THIS IS TO REDIRECT THE USER TO THE TERMS AND CONDITIONS PAGE FROM USER REGISTRATION PAGE, SINCE THE URL HAS TO HAVE A BASE OF "USER-REGISTRATION" */
	get_header();

	$layout = onepress_get_layout();
?>

<HEAD>
<META HTTP-EQUIV="refresh" CONTENT="0;URL=https://www.beelish.com/about/terms-and-conditions/">
</HEAD>

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
