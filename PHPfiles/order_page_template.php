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
					
<script>
function childIdDropDownOnChange(){
    document.getElementById("childSelectionForm").submit();
	var div = document.getElementById("childLoading").style.display = "inline-block";
}
</script>

  <!-- TODO: Replace with account data -->

	<?php
	
/******* hide upcoming order deadline notice
*	
*	$displaydeadline = strtotime("next Tuesday");
*	$displayorderweekstart = strtotime("+6 days", $displaydeadline);
*	$displayorderweekend = strtotime("+4 days", $displayorderweekstart);
*
*	echo "<h5><i class=\"fa fa-hourglass-half\" aria-hidden=\"true\"></i>  Upcoming Deadline:</h5>Order meals by <b><font color=\"#3ab44a\"> ".
*		 date("D, M j", $displaydeadline ) . 
*		 " at noon</b></font> for the week of <b>" . date("M j", $displayorderweekstart ) . " - " . date("M j", $displayorderweekend ) . 
*		 "</b>.<font size=\"-1\"><BR><i class=\"fa fa-star\" aria-hidden=\"true\"></i> <i>More about timing, pricing, and other information <a href=\"http://www.beelish.com/ordering-info\">here</a>.</i><br><BR><br>";
*
************************************/

// display child drop down

	function renderChildDropDownMenu($selectedChildId) {
		global $children;

		echo "<form id=\"childSelectionForm\" method=\"post\" action=\"$PHP_SELF\">";
		echo "<h5 style=\"line-height:40px;\">You are ordering for:  <select name=\"childIdDropDown\" onchange=\"childIdDropDownOnChange()\">";

		foreach($children as $childDetails) {
			echo "<option value=\"" . $childDetails->id . "\"";
			if ($selectedChildId == $childDetails->id) {
				echo " selected";
			}
			echo ">" . $childDetails->getChildSelectionName() . "</option>";
		}

		echo "  </select>";
		echo "<div id=\"childLoading\" style=\"display: none;\"><img style=\"padding-left: 10px; width:45px;\" src=\"" . 
			get_stylesheet_directory_uri() . "/spinner.gif\" /><span style=\"font-size: 12px;\">loading...</span></div></h5>";
		echo "</form><BR>";
	}

	$selectedChildId = getSelectedChildId();
	renderChildDropDownMenu($selectedChildId);

// display ordering information link
	
	echo "<font size=\"-1\"><i class=\"fa fa-star\" aria-hidden=\"true\"></i><em> Find order deadlines, meal pricing, and other details on the <b><a href=\"http://www.beelish.com/ordering-info\">Ordering Information</a></b> page.<br>
	To see menu item descriptions, hover or click on the <b>Order</b> links below.</i></font></em><br><br>";

// Display Menu Tables

// $october2017Calendar = new ProductCalendar("October 2017", BusinessConfigs::OctoberProducts, $selectedChildId);
// $october2017Calendar->renderCalendar();

// $november2017Calendar = new ProductCalendar("November 2017", BusinessConfigs::NovemberProducts, $selectedChildId);
// $november2017Calendar->renderCalendar();

$december2017Calendar = new ProductCalendar("December 2017", BusinessConfigs::DecemberProducts, $selectedChildId);
$december2017Calendar->renderCalendar();
?>
	  
      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>