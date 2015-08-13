<?php

if ($_POST["action"] == "schedule") {
  $posts = get_posts_in_issue($the_issue,$the_year);
  while ($posts->have_posts()) {
    $posts->the_post();
    $post->post_status = "publish";

    $date = $_POST["year"] . "-" . $_POST["month"] . "-" . $_POST["day"] . " ";
    $date = $date . $_POST["time"] . ":" . $_POST["time_min"] . ":00";
    $gmt_date = get_gmt_from_date($date);

    $post->post_date = $date;
    $post->post_date_gmt = $gmt_date;
    $post->edit_date = true;

    wp_update_post($post);
    echo '<!--'; print_r($post); echo '-->';
  }
}

?>