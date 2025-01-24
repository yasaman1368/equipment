<?php
/*
Plugin Name: fajr
Plugin URI: http://example.com/
Description: A simple WordPress plugin example.
Version: 1.0
Author: محمد حسین عالی پور
Author URI: http://example.com/
License: GPL2
*/


defined('ABSPATH') || exit;

define('FAJR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FAJR_PLUGIN_URL', plugin_dir_url(__FILE__));


include_once FAJR_PLUGIN_DIR . '/view/front/form-shortcode.php';
include_once FAJR_PLUGIN_DIR . '/_inc/add_member/fajr_add_member.php';
include_once FAJR_PLUGIN_DIR . '/_inc/show-members/show_members.php';
