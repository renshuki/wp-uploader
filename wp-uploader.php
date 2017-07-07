<?php

/*
Plugin Name: WP Uploader
Description: Sample plugin to demonstrate Wordpress upload functionality
Author: Rensuki
Credits: Jeffrey Way
Version: 0.1
*/

add_action('admin_menu', 'create_theme_options_page');
add_action('admin_init', 'register_and_build_fields');
add_action('admin_head', 'admin_register_head');

//### Functions ###//

function create_theme_options_page() {
  add_menu_page('WP Uploader', 'WP Uploader', 'administrator', __FILE__, 'build_options_page');
}

function build_options_page() {
  ?>  
  <div id="theme-options-wrap" class="widefat">
    <h1 class="dashicons-before dashicons-smiley"> WP Uploader</h1>
    <p>Take control of your theme, by overriding the default settings with your own specific preferences.</p>
    <form method="post" action="options.php" enctype="multipart/form-data">
      <?php settings_fields('plugin_options'); ?>
      <?php do_settings_sections(__FILE__); ?>
      <p class="submit">
        <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
        </p>
    </form>
  </div>
  <?php
}

function register_and_build_fields() {
  register_setting('plugin_options', 'plugin_options', 'validate_setting');
  add_settings_section('main_section', 'Main Settings', 'section_cb', __FILE__);
  add_settings_field('banner_heading', 'Banner Heading:', 'banner_heading_setting', __FILE__, 'main_section');
  add_settings_field('logo', 'Logo:', 'logo_setting', __FILE__, 'main_section');
}

function validate_setting($plugin_options) { 
  $keys = array_keys($_FILES); $i = 0; foreach ( $_FILES as $image ) {
    // if a files was upload   
    if ($image['size']) {     
      // if it is an image     
      if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {       
        $override = array('test_form' => false);       
        // save the file, and store an array, containing its location in $file       
        $file = wp_handle_upload( $image, $override );       
        $plugin_options[$keys[$i]] = $file['url'];     
      } 
      else {       
        // Not an image.        
        $options = get_option('plugin_options');       
        $plugin_options[$keys[$i]] = $options[$logo];       
        // Die and let the user know that they made a mistake.       
        wp_die('No image was uploaded.');     
      }   
    }   
    // Else, the user didn't upload a file.   
    // Retain the image that's already on file.   
    else {     
      $options = get_option('plugin_options');     
      $plugin_options[$keys[$i]] = $options[$keys[$i]];   
    }   

    $i++;

  } 
  return $plugin_options;
}

function section_cb() {}

function banner_heading_setting() {
  $options = get_option('plugin_options');
  echo "<input name='plugin_options[banner_heading]' type='text' value='{$options['banner_heading']}' />";
}

function logo_setting() {  
  $options = get_option('plugin_options'); 
  echo '<input type="file" name="logo" id="logo" />';
  echo "<img src={$options['logo']} />";
}

function admin_register_head() {
  $url = plugin_dir_url( __FILE__ ) . 'css/main.css';
  echo "<link rel='stylesheet' href='$url' />";
}