
<?php

if (!current_user_can("manage_options")) {
  die("You are not allowed to view this page.");
}

function get_posts_in_issue($issue,$year) {
  if (isset($year) && isset($issue)) {
    $query_args = array(
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
					 'orderby' => 'modified',
					 'order' => 'ASC',
			);
    if (isset($_REQUEST["category"])) {
      $query_args['cat'] = $_REQUEST["category"];
    }
    $posts_in_issue = new WP_Query($query_args);
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

wp_enqueue_style("jquery.tablesorter","//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.1/css/theme.default.css");
wp_enqueue_script("jquery.tablesorter","//cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.17.1/jquery.tablesorter.min.js");

wp_enqueue_style("issue-admin",plugins_url("issue-admin.css",__FILE__));
wp_enqueue_script("issue-admin",plugins_url("issue-admin.js",__FILE__));

?>

<div class="wrap">

<h2>Issue Management</h2>

<form method="GET" action="">

<input type="hidden" name="page" value="wp-issues/issue-admin.php" />

<p>

<label for="issue-number">Issue 
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
</select></label>

<label for="issue-year"> of
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
</select></label>

<input type="submit" class="button" value="Get Posts" />

<?php

  function category_link($cat) {
    $link = '<a href="';
    $link .= $_SERVER['REQUEST_URI'] . "&category=$cat->term_id";
    return $link . '">' . $cat->cat_name . '</a>';
  }

  $issue_posts = get_posts_in_issue($the_issue,$the_year);
  if ($issue_posts) {
    echo "<h3>Posts in Issue $the_issue of $the_year</h3>";
    ?>
    <table class="articles-in-issue tablesorter">
    <thead><tr>
      <td>Title</td>
      <td>Category</td>
      <td>Tags</td>
      <td>State</td>
      <td>Date and Time</td>
      <td>Edit</td>
    </tr></thead>
    <tbody>
    <?php
    while ($issue_posts->have_posts()) {
      echo '<tr>';
      $issue_posts->the_post();
      echo '<td><a target="_blank" href="/?preview=true&p=' . $post->ID . '">' . get_the_title() . "</a></td>";
      $categories = get_the_category();
      echo '<td>' . category_link(array_shift($categories));
      foreach ($categories as $category) {
	echo ', ' . category_link($category);
      }
      echo '</td>';
      $tags = get_the_tags();
      echo '<td>' . array_shift($tags)->name;
      foreach ($tags as $tag) {
	echo ', ' . $tag->name;
      }
      echo '</td>';
      echo '<td>' . $post->post_status . "</td>";
      echo '<td class="wp-date">' . get_the_date() . " @ " . get_the_time() . "</td>";
      echo '<td>'; edit_post_link("Edit"); echo '</td>';
      //echo '<!--'; print_r($post); echo '-->';
      echo "</tr>";
    }
    ?>
    </tbody></table>
    
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