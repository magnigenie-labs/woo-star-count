<?php
/*
  Plugin Name: Review Stars Count For  WooCommerce
  Plugin URI: https://wordpress.org/plugins/review-stars-count-for-woocommerce
  Version: 1.0
  Description: This plugin allows you to easily display review stars count for the products in your store.
  Author: MagniGenie
  Author URI: http://magnigenie.com/
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct file access
!defined('ABSPATH') AND exit;

define('WOOPSR_FILE', __FILE__);
define('WOOPSR_PATH', plugin_dir_path(__FILE__));
define('WOOPSR_BASE', plugin_basename(__FILE__));

require WOOPSR_PATH . 'includes/function.php';
new wooPsr();
