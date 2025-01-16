<?php
add_action('wp_ajax_nopriv_porsnegar_pagination', 'porsnegar_pagination');
add_action('wp_ajax_porsnegar_pagination', 'porsnegar_pagination');
function porsnegar_pagination()
{
    $pageNumber = intval($_POST['pageNumber']);
    $pageNumber++;
    $page_num = $pageNumber;

    // WP_Query arguments
    $args = array(
        'post_type'      => 'post', // Use any for any kind of post type
        'post_status'    => 'publish', // Only published posts
        'order'          => 'DESC', // Order by descending
        'orderby'        => 'date', // Order by date
        'posts_per_page' => 3,
        'paged'          => $page_num
    );
    // Use get_posts for better performance with small queries
    $posts = new WP_Query($args);
    $max_num_pages = $posts->max_num_pages;
    ob_start();
    if ($posts->have_posts()) :
        while ($posts->have_posts()) : $posts->the_post();
            // Store values in variables for better performance

            $thumbnail_url = get_the_post_thumbnail_url(get_the_id(), 'medium');
            $post_title = esc_html(get_the_title());
            //  $post_excerpt = esc_html(custom_exerpt_post($post->ID, 20));
            $author_name = esc_html(get_the_author_meta('display_name'));
            $permalink = get_permalink(get_the_id());


?>
            <div class="col-sm-4">
                <a href="<?php echo $permalink; ?>">
                    <div class="card shadow h-100">
                        <div class="img-thumbnail-container">
                            <?php if ($thumbnail_url) : ?>
                                <img src="<?php echo esc_url($thumbnail_url); ?>" style="max-height: 250px" class="w-100 d-block m-auto thumbnail-latest-post" alt="<?php echo $post_title; ?>" />
                            <?php else : ?>
                                <img class="thumbnail-latest-post" style="max-width: 100%;height:auto" src="<?php echo esc_url(get_placeholder_image_url());
                                                                                                            ?>" alt="Placeholder image" />
                            <?php endif; ?>
                        </div>
                        <div class="card-body pb-0 ">
                            <h6 class="card-title fw-lighter fs-5 text-black"><?php echo $post_title; ?></h6>
                            <!-- <p class="card-text fw-lighter"><?php // echo $post_excerpt; 
                                                                    ?></p> -->
                        </div>
                        <div>
                            <div class=" pb-2 fw-lighter">
                                <span class="icons mx-2 mb-2"><i class="bi bi-person text-muted "></i>نویسنده:</span><?php echo $author_name; ?>
                            </div>
                            <!-- <div class="m-1 p-1 fw-lighter">
                                    <span class="icons mx-2"><i class="bi bi-chat text-muted "></i> نظرات:</span><?php // echo $post->comment_count 
                                                                                                                    ?>
                                </div> -->
                        </div>

                    </div>
                </a>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); // Reset post data 
        ?>
    <?php else: ?>
        <div class="alert alert-info">پستی منتشر نشده است.</div>

<?php
    endif;
    $next_posts = ob_get_clean();
    if ($max_num_pages <= $page_num) {
        $page_num = 'end';
    }
    wp_send_json([
        'success' => true,
        'nextPosts' => $next_posts,
        'pageNumber' => $page_num
    ], 200);
}
