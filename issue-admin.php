
<?php

$the_issue = $_REQUEST["issue-number"];
$the_year = $_REQUEST["issue-year"];

?>

<div class="wrap">

<h2>Issue Management</h2>

<form method="GET" action="">

<input type="hidden" name="page" value="wp-issues/issue-admin.php" />

<p><label for="issue-year">Year:
<select name="issue-year">
<?php
   $year_query = "SELECT DISTINCT meta_value FROM  $wpdb->postmeta WHERE meta_key = '_ao_issues_year' ORDER BY meta_value DESC";
   $year_result = $wpdb->get_results( $year_query );

   $years = array();

foreach ( $year_result as $year ) {
  if ($year->meta_value == $_GET["issue-year"])
    echo "<option selected=\"selected\">$year->meta_value</option>";
  else
    echo "<option>$year->meta_value</option>";
}
?>
</select></label></p>

<p><label for="issue-number">Issue Number:
<select name="issue-number">
<?php
   $number_query = "SELECT DISTINCT meta_value FROM  $wpdb->postmeta WHERE meta_key = '_ao_issues_number' ORDER BY meta_value DESC";
   $number_result = $wpdb->get_results( $number_query );

   $numbers = array();

foreach ( $number_result as $number ) {
  if ($number->meta_value == $_GET["issue-number"])
    echo "<option selected=\"selected\">$number->meta_value</option>";
  else
    echo "<option>$number->meta_value</option>";
}
?>
</select></label></p>

<input type="submit" class="button" value="Get Posts" />
</form>

<?php

  if (isset($_GET["issue-year"]) && isset($_GET["issue-number"])) {
    $posts_in_issue = new WP_Query(array(
					 'post_type' => 'post',
					 'meta_query' => array(
							       array(
								     'key' => '_ao_issues_year',
								     'value' => $_GET["issue-year"],
								     ),
							       array(
								     'key' => '_ao_issues_number',
								     'value' => $_GET["issue-number"],
								     )
							       ),
					 ));
    echo '<h3>Posts in Issue $
    echo '<ul style="list-style: initial; margin-left: 1em">';
    while ($posts_in_issue->have_posts()) {
      $posts_in_issue->the_post();
      echo '<li>' . get_the_title() . '</li>';
    }
    echo '</ul>';
  }
   
?>

<?php
/*    <?php // view filters */
/*    if ( !is_singular() ) { */
/*      $carc_query = "SELECT DISTINCT YEAR(meta_value) AS yyear, MONTH(meta_value) AS mmonth FROM $wpdb->postmeta WHERE meta_key = '_EventEndDate' ORDER BY meta_value DESC"; */

/*      $carc_result = $wpdb->get_results( $carc_query ); */
/*      $month_count = count($carc_result); */

/*      if ( $month_count && !( 1 == $month_count && 0 == $carc_result[0]->mmonth ) ) { */
/*        $postmeta = isset($_GET['postmeta']) ? (int)$_GET['postmeta'] : 0; */

/* ?> */
/* <select name='postmeta'  id='postmeta' class='postform'> */
/*    <option<?php selected( $m, 0 ); ?> value='0'>Kalender</option> */
/* <?php */
/* 					foreach ($carc_result as $carc_row) { */
/* 	 if ( $carc_row->yyear == 0 ) */
/* 	   continue; */
/* 	 $carc_row->mmonth = zeroise( $carc_row->mmonth, 2 ); */

/* 	 if ( $carc_row->yyear . $carc_row->mmonth == $m ) */
/* 	   $default = ' selected="selected"'; */
/* 	 else */
/* 	   $default = ''; */

/* 	 echo "<option$default value='" . esc_attr("$arc_row->yyear$arc_row->mmonth") . "'>"; */
/* 	 echo $wp_locale->get_month($carc_row->mmonth) . " $arc_row->yyear"; */
/* 	 echo "</option>\n"; */
/*        } */
/* ?> */
/* </select> */
/*     <?php } */
/*    } */
/* ?> */ ?>

</div>