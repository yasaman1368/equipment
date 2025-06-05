<?php

// Add this to handle the AJAX request for fetching the user list
add_action('wp_ajax_get_users_list', 'get_users_list');
add_action('wp_ajax_nopriv_get_users_list', 'get_users_list'); // If you want non-logged-in users to access it

function get_users_list()
{
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $users_per_page = 5; // Number of users per page
    $offset = ($paged - 1) * $users_per_page; // Calculate the offset for user numbers

    $args = array(
        'number' => $users_per_page,
        'offset' => $offset, // Add offset to the query
        'paged' => $paged,
    );
    $users = get_users($args);


    ob_start(); // Start output buffering

    if (!empty($users)) {
        echo ' <p class="bg-secondary d-inline p-2 rounded">  تعداد کاربران :  <span class=" text-white">' . count_users()['total_users'] . '</span></p>';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead><tr><th class="text-center" style="width: 1%; white-space: nowrap;">ردیف</th><th>نام کاربری</th><th>نام و نام خانوادگی</th><th>نقش</th></tr></thead>';
        echo '<tbody>';
        foreach ($users as $index => $user) {
            $get_role = get_user_meta($user->ID, '_role', true);
            if ($get_role === 'manager') {
                $role = 'مدیر';
            } else if ($get_role === 'helper') {
                $role = 'معاون';
            } else if ($get_role === 'user') {
                $role = 'کاربر';
            } else {
                $role = 'مدیر ارشد';
            }
            echo '<tr>';
            echo '<td class="text-center">' . ($offset + $index + 1) . '</td>'; // Calculate and display the user number
            echo '<td>' . esc_html($user->user_login) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>' . $role . '<i class="bi bi-gear text-success" data-user-id=' . $user->ID . '></i></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';

        // Pagination
        $total_users = count(get_users());
        $total_pages = ceil($total_users / $users_per_page);
        echo '<nav aria-label="Page navigation">';
        echo '<ul class="pagination justify-content-center">';
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<li data-pagination=' . $i . ' class="page-item ' . ($paged == $i ? 'active' : '') . '">';
            echo '<a class="page-link"  >' . $i . '</a>';
            echo '</li>';
        }
        echo '</ul></nav>';
    } else {
        echo '<p class="text-center">هیچ کاربری یافت نشد.</p>';
    }

    $html = ob_get_clean(); // Get the buffered output and clean the buffer
    wp_send_json_success($html); // Send the HTML as a JSON response
}
