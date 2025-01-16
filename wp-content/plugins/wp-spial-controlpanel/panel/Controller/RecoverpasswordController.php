<?php
class RecoverpasswordController extends Handler
{
    public function index()
    {
        View::loadView(YAS_SCP_DIR_VIEW . 'panel/RecoverpasswordView.php', [], 'Empty');
        die;
    }
}
