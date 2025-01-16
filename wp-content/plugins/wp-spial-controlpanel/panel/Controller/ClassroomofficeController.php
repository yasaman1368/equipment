<?php
class ClassroomofficeController
{
    public function __construct() {}
    public function index()
    {
        $view = YAS_SCP_DIR_VIEW . 'panel/ClassroomofficeView.php';
        View::loadView($view);
    }
}
