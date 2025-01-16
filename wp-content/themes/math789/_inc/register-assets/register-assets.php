<?php
add_action('admin_enqueue_scripts', 'action_admin_enqueue_scripts');


function action_admin_enqueue_scripts(string $hook_suffix): void
{
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css');
    wp_enqueue_style('bootstrap-icon', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
}
