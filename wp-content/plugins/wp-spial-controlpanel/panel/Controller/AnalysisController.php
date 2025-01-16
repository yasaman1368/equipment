<?php
class AnalysisController extends Handler
{
    public function __construct() {}
    public function index()
    {
        View::loadView(YAS_SCP_DIR_VIEW . 'panel/AnalysisView.php');
        die;
    }
}
