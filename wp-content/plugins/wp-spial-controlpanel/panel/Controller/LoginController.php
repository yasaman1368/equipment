<?php
class LoginController extends Handler
{
    public function index()
    {
        View::loadView(YAS_SCP_DIR_VIEW . 'panel/LoginView.php', [], 'Empty');
        die;
    }
}
