<?php
function get_form_locations($form_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "equipment_forms";
    $locations = $wpdb->get_var($wpdb->prepare("SELECT locations FROM $table_name WHERE id = $form_id"));
    if (!$locations) {
        return false;
    }

    return json_decode($locations);
}

function get_user_locations($user_id)
{
    $user_locations = get_user_meta($user_id, '_locations', true) ?: [];
    return $user_locations;
}
