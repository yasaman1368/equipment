<?php
/*
Plugin Name: پلاگین پنل کاربری پیشرفته
Plugin URI: https://wordpress.org/plugins/wp-wep-plugin
Description: پلاگین پنل کاربری پیشرفته
Author: محمد حسین عالی پور
Version: 1.0.0
Licence: GPLv2 or Later
Author URI: http://learnup.local
*/

class YasSCPCore
{
    public function __construct()
    {
        $this->define_constants();
        $this->init();
    }

    private function init()
    {
        include_once YAS_SCP_DIR . 'class/loader.php';
        include_once YAS_SCP_DIR . 'panel/router.php';
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    private function define_constants()
    {
        define('YAS_SCP_DIR', plugin_dir_path(__FILE__));
        define('YAS_SCP_URL', plugin_dir_url(__FILE__));
        define('YAS_SCP_DIR_VIEW', plugin_dir_path(__FILE__) . 'view/');
        define('YAS_SCP_URL_STYLE', plugin_dir_url(__FILE__) . 'assets/front/css/');
        define('YAS_SCP_URL_JS', plugin_dir_url(__FILE__) . 'assets/front/js/');
    }

    public function register_assets()
    {
        // wp_enqueue_style('yas-scp-style', YAS_SCP_URL . 'assets/front/css/style.css', [], '1.0.0');
        // wp_enqueue_script('yas-scp-main-js', YAS_SCP_URL . 'assets/front/ajax/main.js', ['jquery'], '1.0.0', true);
    }
}

new YasSCPCore();
