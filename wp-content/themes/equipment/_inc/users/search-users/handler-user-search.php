<?php
// Add AJAX handler for searching users
add_action('wp_ajax_search_users', 'search_users_callback');
add_action('wp_ajax_nopriv_search_users', 'search_users_callback');

function search_users_callback()
{
    $search_term = sanitize_text_field($_POST['term']);
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $users_per_page = 5; // Number of users per page
    $offset = ($paged - 1) * $users_per_page; // Calculate the offset for user numbers

    $args = array(
        'number'         => $users_per_page,
        'offset'         => $offset,
        'paged'          => $paged,
        'search'         => '*' . $search_term . '*',
        'search_columns' => array('user_login', 'user_email', 'display_name'),
    );
    $users = get_users($args);

    ob_start(); // Start output buffering

    if (!empty($users)) {
        echo '<p class="bg-secondary d-inline p-2 rounded">تعداد کاربران : <span class="text-white">' . count($users) . '</span></p>';

        // Wrap the table in a responsive container for better mobile experience.
        echo '<div class="table-responsive">';
        // Include table-hover for a modern hover effect along with other table classes.
        echo '<table class="table table-hover table-bordered table-striped">';
        echo '<thead><tr><th class="text-center" style="width: 1%; white-space: nowrap;">ردیف</th><th>نام کاربری</th><th>نام و نام خانوادگی</th><th>نقش</th></tr></thead>';
        echo '<tbody>';

        foreach ($users as $index => $user) {
            $role = eqiupment_get_user_role($user->ID);
            echo '<tr>';
            echo '<td  class="text-center">' . ($offset + $index + 1) . '</td>'; // Calculate and display the user number
            echo '<td>' . esc_html($user->user_login) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>' . $role . ' <i class="bi bi-gear text-success" data-user-id="' . $user->ID . '"></i></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '</div>'; // End of responsive table container

        // Pagination
        $total_users = count(get_users($args)); // Total users matching the search term
        $total_pages = ceil($total_users / $users_per_page);
        echo '<nav aria-label="Page navigation">';
        echo '<ul class="pagination justify-content-center">';
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<li data-pagination="' . $i . '" class="page-item ' . ($paged == $i ? 'active' : '') . '">';
            echo '<a class="page-link" href="#">' . $i . '</a>';
            echo '</li>';
        }
        echo '</ul></nav>';
    } else {
        echo '<p class="text-center fw-bold bg-light p-2 rounded">هیچ کاربری یافت نشد.</p>';
    }

    $html = ob_get_clean(); // Get the buffered output and clean the buffer
    wp_send_json_success($html); // Send the HTML as a JSON response
}
