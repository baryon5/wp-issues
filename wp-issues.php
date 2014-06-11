<?php
/**
 * Plugin Name: Aaron's Issue Management Plugin
 * Description: Plugin for tagging posts as belong to issues, and managing them in a unit. Very limited functionality so far.
 * Version: 1.0.0
 * Author: Aaron Olkin
 * License: MIT
 * 
 * Issue Management Plugin
 * Copyright (C) 2014, Aaron Olkin
 * 
 */


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function ao_issues_add_meta_box() {
    add_meta_box('ao_issues_yearnumber',
		 'Issue Information',
		 'ao_issues_meta_box_callback',
		 'post','side','high');
}

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function ao_issues_meta_box_callback( $post ) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'ao_issues_meta_box', 'ao_issues_meta_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $year = get_post_meta( $post->ID, '_ao_issues_year', true );
  $number = get_post_meta( $post->ID, '_ao_issues_number', true );
  $year = ($year == "")?date("Y"):$year;
  $number = ($number == "")?"1":$number;

  echo '<style>
.ao-issue-tagging { float: right; }
.ao-issue-tagging-label { display: block; height: 32px; }
</style>';

  echo '<label for="ao_issue_year" class="ao-issue-tagging-label"><span>Year:</span> <input id="ao_issue_year" class="ao-issue-tagging" name="ao_issue_year" value="' . esc_attr( $year ) . '" size="4" /></label>';
  echo '<label for="ao_issue_number" class="ao-issue-tagging-label"><span>Issue Number:</span> <input id="ao_issue_number" class="ao-issue-tagging" name="ao_issue_number" value="' . esc_attr( $number ) . '" size="4" /></label>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function ao_issues_save_meta_box_data( $post_id ) {

  /*
   * We need to verify this came from our screen and with proper authorization,
   * because the save_post action can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['ao_issues_meta_box_nonce'] ) ) {
    return;
  }

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['ao_issues_meta_box_nonce'], 'ao_issues_meta_box' ) ) {
    return;
  }

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  /*// Check the user's permissions.
  if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }

  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
    }*/

  /* OK, its safe for us to save the data now. */

  // Make sure that it is set.
  if ( ! ( isset( $_POST['ao_issue_year'] ) && isset( $_POST['ao_issue_number'] ) ) ) {
    return;
  }

  // Sanitize user input.
  $year = sanitize_text_field( $_POST['ao_issue_year'] );
  $number = sanitize_text_field( $_POST['ao_issue_number'] );

  // Update the meta field in the database.
  update_post_meta( $post_id, '_ao_issues_year', $year );
  update_post_meta( $post_id, '_ao_issues_number', $number );
}

function init_ao_issue_pages() {
  $menu_hookname = add_object_page( 'Issue Management', 'Issues', 'manage_options', 'wp-issues/issue-admin.php');
}


if (is_admin()) {
  add_action( 'add_meta_boxes', 'ao_issues_add_meta_box' );
  add_action( 'save_post', 'ao_issues_save_meta_box_data' );
  add_action( 'admin_menu', 'init_ao_issue_pages');
}