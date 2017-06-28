<?php

/*
  Plugin Name: Woo-Star-Count
  Plugin URI: www.magnigeeks.com
  Description: A light weight plugin for showing ratings on every single product .It can be enabled for both product single page & product archive page.
  Version: 1.0
  Author URI: www.magnigeeks.com
  Author: magnigeeks
 */

// No direct file access
!defined('ABSPATH') AND exit;

define('WOOPSR_FILE', __FILE__);
define('WOOPSR_PATH', plugin_dir_path(__FILE__));
define('WOOPSR_BASE', plugin_basename(__FILE__));

require WOOPSR_PATH . 'includes/function.php';
new wooPsr();
