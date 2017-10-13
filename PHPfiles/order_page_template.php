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
					
<script>
function childIdDropDownOnChange(){
    document.getElementById("childSelectionForm").submit();
	var div = document.getElementById("childLoading").style.display = "inline-block";
}
</script>

  <!-- TODO: Replace with account data -->


	<?php

	function renderChildDropDownMenu($selectedChildId) {
		global $children;

		echo "<form id=\"childSelectionForm\" method=\"post\" action=\"$PHP_SELF\">";
		echo "<h2>Ordering for:  <select name=\"childIdDropDown\" onchange=\"childIdDropDownOnChange()\">";

		foreach($children as $childDetails) {
			echo "<option value=\"" . $childDetails->id . "\"";
			if ($selectedChildId == $childDetails->id) {
				echo " selected";
			}
			echo ">" . $childDetails->getChildSelectionName() . "</option>";
		}

		echo "  </select>";
		echo "<div id=\"childLoading\" style=\"display: none;\"><img style=\"padding-left: 10px; width:45px;\" src=\"" . get_stylesheet_directory_uri() . "/spinner.gif\" /><span style=\"font-size: 12px;\">loading...</span></div></h2>";
		echo "</form>";
	}

	$selectedChildId = getSelectedChildId();
	renderChildDropDownMenu($selectedChildId);
	?>

<!-- Menu Table -->

<?php

$octoberCalendar = new ProductCalendar("October 2017", BusinessConfigs::OctoberProducts, $selectedChildId);
$octoberCalendar->renderCalendar();

$novemberCalendar = new ProductCalendar("November 2017", BusinessConfigs::NovemberProducts, $selectedChildId);
$novemberCalendar->renderCalendar();

$decemberCalendar = new ProductCalendar("December 2017", BusinessConfigs::DecemberProducts, $selectedChildId);
$decemberCalendar->renderCalendar();
?>
	  
      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>