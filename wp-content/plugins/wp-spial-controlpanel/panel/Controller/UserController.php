<?php
class UserController
{
    public function __construct() {}
    public function index()
    {
        $view = YAS_SCP_DIR_VIEW . 'panel/UserView.php';
        View::loadView($view);
    }
}
