<?php
function custom_exerpt_post($post_id, $num): string
{
    $text = get_the_excerpt($post_id);
    return  wp_trim_words($text, $num, '...');
}
