<?php



class manageuserController
{
    public function __construct() {}
    public function index()
    {
        $params = [
            'current_user_dispaly_name' => $this->get_current_user_dispaly_name()
        ];
        View::loadView(get_template_directory() . '/view/panel/manageuserView.php', $params);
        die;
    }
    private function get_current_user_dispaly_name()
    {
        return wp_get_current_user()->display_name;
    }
}
