<?php
class AutoloadSCP
{
    private static $_instance = null;

    private function __construct()
    {
        spl_autoload_register([$this, 'load']);
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new AutoloadSCP();
        }
        return self::$_instance;
    }

    public function load($class)
    {
        $filePath = trailingslashit(YAS_SCP_DIR . 'class') . $class . '.php';

        // Check if the file exists before requiring it
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
}

// Initialize the autoloader
AutoloadSCP::getInstance();
