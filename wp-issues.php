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
  $number = ($number == "")?get_option("current-issue-default")["current-issue-default"]:$number;
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


  function validate_setting_field($val) {
    return $val;
  }

  function issue_management_settings()
  {
    ?>
    <div class="section panel">
      <h1>Issue Management &ndash; Settings</h1>
      <form method="post" enctype="multipart/form-data" action="options.php">
	<?php 
	   settings_fields('issue-management-settings');	   
	   do_settings_sections('issue-management-settings');

	   settings_fields('hp-override-settings');
	   do_settings_sections('hp-override-settings');
	   ?>
	<p class="submit">  
	  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
	</p>  
	
      </form>
    </div>
    <?php
   }

add_action('admin_menu', 'issue_management_settings_menu');

function void_settings_sdisplay() { }

function issue_management_settings_display($args)
{
  extract( $args );

  $option_name = 'current-issue-default';

  $options = get_option( $option_name );
  $value = esc_attr( $options[$id] );

  switch ( $type ) {  
  case 'text':
    echo "<input class='regular-text$class' type='text' id='$id' name='" . $option_name . "[$id]' value=\"$value\" />";  
    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
    break;  
  }
}

function issue_management_settings_menu() {
  add_submenu_page( "wp-issues/issue-admin.php", "Issue Management Settings", "Issue Management Settings", "manage_options", "issue-management-settings", "issue_management_settings");
}

add_action("admin_init", "issue_management_settings_init");

function hp_override_year_settings_display($args)
{
  extract( $args );
  $option_name = 'hp-override-year';
  $options = get_option( $option_name );
  $value = esc_attr( $options[$id] );

  global $wpdb;
  $number_query = "SELECT DISTINCT meta_value FROM  $wpdb->postmeta WHERE meta_key = '_ao_issues_year' ORDER BY meta_value DESC";
  $number_result = $wpdb->get_results( $number_query );
  $numbers = array();
  echo "<select name='" . $option_name . "[$id]' id='$id'>";
  if ($value) {
    echo "<option value=\"\">Automatic</option>";
  } else {
    echo "<option value=\"\" selected=\"selected\">Automatic</option>";
  }
  foreach ( $number_result as $number ) {
    if ($number->meta_value == $value)
      echo "<option selected=\"selected\">$number->meta_value</option>";
    else
      echo "<option>$number->meta_value</option>";
  }
  echo '</select>';
  echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
}

function hp_override_issue_settings_display($args)
{
  extract( $args );
  $option_name = 'hp-override-issue';
  $options = get_option( $option_name );
  $value = esc_attr( $options[$id] );

  global $wpdb;
  $number_query = "SELECT DISTINCT meta_value FROM  $wpdb->postmeta WHERE meta_key = '_ao_issues_number' ORDER BY meta_value DESC";
  $number_result = $wpdb->get_results( $number_query );
  $numbers = array();
  echo "<select name='" . $option_name . "[$id]' id='$id'>";
  if ($value) {
    echo "<option value=\"\">Automatic</option>";
  } else {
    echo "<option value=\"\" selected=\"selected\">Automatic</option>";
  }
  foreach ( $number_result as $number ) {
    if ($number->meta_value == $value)
      echo "<option selected=\"selected\">$number->meta_value</option>";
    else
      echo "<option>$number->meta_value</option>";
  }
  echo '</select>';
  echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
}


function hp_override_category_settings_display($args)
{
  extract( $args );
  $option_name = 'hp-override-category';
  $options = get_option( $option_name );
  $value = esc_attr( $options[$id] );

  echo "<select name='" . $option_name . "[$id]' id='$id'>";
  if ($value) {
    echo "<option value=\"\">Use Defaults</option>";
  } else {
    echo "<option value=\"\" selected=\"selected\">Use Defaults</option>";
  }
  $categories = get_categories(array("orderby"=>"count","order"=>"DESC")); 
  foreach ($categories as $category) {
      echo '<option value="'.$category->category_nicename.'"';
    if ($category->category_nicename == $value) {
      echo ' selected="selected">';
    } else {
      echo '>';
    }
    echo $category->cat_name .'</option>';
  }
  echo '</select>';
  echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
}

function issue_management_settings_init() {
  register_setting( "issue-management-settings", "current-issue-default", "validate_setting_field" );

  add_settings_section( 'issue-defaults', 'Current Issue', 'void_settings_sdisplay', 'issue-management-settings' );
  $field_args = array(
		      'type'      => 'text',
		      'id'        => 'current-issue-default',
		      'name'      => 'current-issue-default',
		      'desc'      => 'The current default issue number',
		      'std'       => '',
		      'label_for' => 'current-issue-default',
		      'class'     => 'css_class'
		      );
  add_settings_field( 'current-issue-default', 'Issue Defaults', 'issue_management_settings_display', 'issue-management-settings', 'issue-defaults', $field_args );

  register_setting( "hp-override-settings", "hp-override-year", "validate_setting_field" );
  register_setting( "hp-override-settings", "hp-override-issue", "validate_setting_field" );
  register_setting( "hp-override-settings", "hp-override-category", "validate_setting_field" );

  add_settings_section( 'hp-overrides', 'Homepage Overrides', 'void_settings_sdisplay', 'hp-override-settings' );

  $field_args = array(
		      'type'      => 'text',
		      'id'        => 'hp-override-year',
		      'name'      => 'hp-override-year',
		      'desc'      => 'Year to display on the homepage.',
		      'std'       => '',
		      'label_for' => 'hp-override-year',
		      'class'     => 'css_class'
		      );
  add_settings_field( 'hp-override-year', 'Current Year', 'hp_override_year_settings_display', 'hp-override-settings', 'hp-overrides', $field_args );
  $field_args = array(
		      'type'      => 'text',
		      'id'        => 'hp-override-issue',
		      'name'      => 'hp-override-issue',
		      'desc'      => 'Issue to display on the homepage.',
		      'std'       => '',
		      'label_for' => 'hp-override-issue',
		      'class'     => 'css_class'
		      );
  add_settings_field( 'hp-override-issue', 'Current Issue', 'hp_override_issue_settings_display', 'hp-override-settings', 'hp-overrides', $field_args );
  $field_args = array(
		      'type'      => 'text',
		      'id'        => 'hp-override-category',
		      'name'      => 'hp-override-category',
		      'desc'      => 'Category to display on the homepage.',
		      'std'       => '',
		      'label_for' => 'hp-override-category',
		      'class'     => 'css_class'
		      );
  add_settings_field( 'hp-override-category', 'Category Override', 'hp_override_category_settings_display', 'hp-override-settings', 'hp-overrides', $field_args );

}

}