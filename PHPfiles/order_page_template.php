<?php /* Template Name: Order */ 

	if ( is_user_logged_in() ) {
		get_header();
	} else {
		header("Location: /wms");
		die();
	}

$layout = onepress_get_layout();

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
		<h2> October 2017 </h2>
		<table fontcolor="000000">
		  <tr>
			<th width="20%" bgcolor="D9EDF7">Monday</th>
			<th bgcolor="D9EDF7">Tuesday</th>
			<th bgcolor="D9EDF7">Wednesday</th>
			<th bgcolor="D9EDF7">Thursday</th>
			<th bgcolor="D9EDF7">Friday</th>
		  </tr>
		  <tr>
			<td width="20%" height="100" bgcolor="#D3D3D3">Sept 11, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 12, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 13, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 14, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 15, 2017 <p><i>No lunch</i></p></td>
		  </tr>
		  <tr>
			<td width="20%" height="100" bgcolor="#D3D3D3">Sept 18, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 19, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 20, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 21, 2017 <p><i>No lunch</i></p></td>
			<td bgcolor="#D3D3D3">Sept 22, 2017 <p><i>No lunch</i></p></td>
			<?php
//new row
				echo "</tr> <tr> <td width=\"20%\" height=\"100\"";
					$mealID = '170';
					display_meal(170);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '167';
					display_meal(167);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '159';
					display_meal(159);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '389';
					display_meal(389);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '392';
					display_meal(392);
//new row
				echo "</td> </tr> <tr> <td width=\"20%\" height=\"100\"";
					$mealID = '395';
					display_meal(395);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '401';
					display_meal(401);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '401';
					display_meal(401);		
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '437';
					display_meal(437);		
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '401';
					display_meal(401);		
//new row
				echo "</td> </tr> <tr> <td width=\"20%\" height=\"100\"";
					$mealID = '401';
					display_meal(401);
				echo "</td> <td width=\"20%\" height=\"100\"";
					$mealID = '401';
					display_meal(401);
			?>
			</td>
			<td bgcolor="#D3D3D3"> </td>
			<td bgcolor="#D3D3D3"> </td>
			<td bgcolor="#D3D3D3"> </td>		 
		  </tr>
		  </table>
		  
		  

	  
      </main>
      <!-- #main -->
    </div><!-- #primary -->



  </div>
  <!--#content-inside -->
</div><!-- #content -->


<?php get_footer(); ?>