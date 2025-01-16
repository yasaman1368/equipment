<?php
class TestKeyPageController extends Handler
{
    public function __construct() {}
    public function index()
    {
        View::loadView(YAS_SCP_DIR_VIEW . 'panel/TestKeyPageView.php');
        die;
    }
}
