<?php
$students_writer = [15, 58, 20, 124, 36, 121, 175];
foreach ($students_writer as $student) {
    add_author_to_stutent($student);
}
function add_author_to_stutent($user_id)
{
    $user = new WP_User($user_id);
    $user->add_cap('edit_posts');
    $user->add_cap('upload_files');
}
