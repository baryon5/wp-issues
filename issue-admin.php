
<?php

if (!current_user_can("manage_options")) {
  die("You are not allowed to view this page.");
}

function get_posts_in_issue($issue,$year) {
  if (isset($year) && isset($issue)) {
    $posts_in_issue = new WP_Query(array(
					 'post_type' => 'post',
					 'meta_query' => array(
							       array(
								     'key' => '_ao_issues_year',
								     'value' => $year,
								     ),
							       array(
								     'key' => '_ao_issues_number',
								     'value' => $issue,
								     )
							       ),
					 'nopaging' => true,
					 'orderby' => 'title',
					 'order' => 'ASC',
					 ));
    return $posts_in_issue;
  }
  return false;
};

if (isset($_REQUEST["issue-number"])) {
  $the_issue = $_REQUEST["issue-number"];
}
if (isset($_REQUEST["issue-year"])) {
  $the_year = $_REQUEST["issue-year"];
}

if (isset($_POST["action"])) {
  // DO ACTION!!!
  require("actions.php");
}

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
  if ($year->meta_value == $the_year)
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
  if ($number->meta_value == $the_issue)
    echo "<option selected=\"selected\">$number->meta_value</option>";
  else
    echo "<option>$number->meta_value</option>";
}
?>
</select></label></p>

<input type="submit" class="button" value="Get Posts" />

<?php
  $issue_posts = get_posts_in_issue($the_issue,$the_year);
  if ($issue_posts) {
    echo "<h3>Posts in Issue $the_issue of $the_year</h3>";
    echo '<ul style="list-style: initial; margin-left: 1em">';
    while ($issue_posts->have_posts()) {
      $issue_posts->the_post();
      echo '<li>' . get_the_title() . " [Status: $post->post_status] (Date: ";
      echo get_the_date() . " @ " . get_the_time() . ") - ";
      edit_post_link("edit this post");
      echo "</li>";
      echo '<!--'; print_r($post); echo '-->';
    }
    echo '</ul>';
    ?>
    
    <div class="timestamp-wrap">
    <select id="month" name="month">
    <option value="01">01-Jan</option>
    <option value="02">02-Feb</option>
    <option value="03">03-Mar</option>
    <option value="04">04-Apr</option>
    <option value="05">05-May</option>
    <option value="06">06-Jun</option>
    <option value="07">07-Jul</option>
    <option value="08">08-Aug</option>
    <option value="09">09-Sep</option>
    <option value="10">10-Oct</option>
    <option value="11">11-Nov</option>
    <option value="12">12-Dec</option>
    </select>
    <select id="day" name="day">
    <option value="01">01</option>
    <option value="02">02</option>
    <option value="03">03</option>
    <option value="04">04</option>
    <option value="05">05</option>
    <option value="06">06</option>
    <option value="07">07</option>
    <option value="08">08</option>
    <option value="09">09</option>
    <option value="10">10</option>
    <option value="11">11</option>
    <option value="12">12</option>
    <option value="13">13</option>
    <option value="14">14</option>
    <option value="15">15</option>
    <option value="16">16</option>
    <option value="17">17</option>
    <option value="18">18</option>
    <option value="19">19</option>
    <option value="20">20</option>
    <option value="21">21</option>
    <option value="22">22</option>
    <option value="23">23</option>
    <option value="24">24</option>
    <option value="25">25</option>
    <option value="26">26</option>
    <option value="27">27</option>
    <option value="28">28</option>
    <option value="29">29</option>
    <option value="30">30</option>
    <option value="31">31</option>
    </select>
    <input type="text" id="year" name="year" value="<?php echo date("Y"); ?>" size="4" maxlength="4" autocomplete="off"> @
    <select id="time" name="time">
    <option value="00:00:00">Midnight</option>
    <option value="09:00:00">9:00 AM</option>
    <option value="12:00:00">Noon</option>
    <option value="15:00:00">3:00 PM</option>
    <option value="18:00:00">6:00 PM</option>
    <option value="21:00:00">9:00 PM</option>
    </select>
    <button type="submit" class="button" name="action" value="schedule" formmethod="POST">Set Publish Date and Schedule All</button>
    </div>

    <?php

       }
   
?>

</form>

</div>