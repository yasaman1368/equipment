<?php
class View
{
    public static  function loadView($view, $data = [], $layout = 'Defualt')
    {
        $layout_path = YAS_SCP_DIR_VIEW . 'layout/';
        $layout_file_path = $layout_path . $layout . '.php';
        if (file_exists($layout_file_path) and is_readable($layout_file_path)) {
            extract($data);
            require_once $layout_file_path;
        }
        die;
    }
}
