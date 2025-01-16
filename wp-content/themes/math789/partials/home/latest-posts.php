<div class="container-fluid">
    <div class="mt-5 mx-3">

        <h4>جدید ترین نوشته ها</h4>
        <hr>
    </div>
    <div class="row bg-warning  m-3 mb-5 rounded shadow g-3 p-3">
        <?php
        $page_num = 2;
        // WP_Query arguments
        $args = array(
            'post_type'      => 'post', // Use any for any kind of post type
            'post_status'    => 'publish', // Only published posts
            //'order'          => 'DESC', // Order by descending
            'orderby'        => 'rand', // Order by date
            //'numberposts'    => 10 // Limit the number of posts for better performance
            'posts_per_page' => 3,
            'paged'          => $page_num
        );
        // Use get_posts for better performance with small queries
        $posts = new WP_Query($args);

        if ($posts->have_posts()) :
            while ($posts->have_posts()) : $posts->the_post();
                // Store values in variables for better performance
                setup_postdata($post); // Set up post data
                $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
                $post_title = esc_html(get_the_title());
                //  $post_excerpt = esc_html(custom_exerpt_post($post->ID, 20));
                $author_name = esc_html(get_the_author_meta('display_name'));
                $permalink = get_permalink($post->ID);

        ?>
                <div class="col-sm-4">
                    <a href="<?php echo $permalink; ?>">
                        <div class="card shadow h-100 ">
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
        <?php endif; ?>
        <a href="<?php echo home_url('archive') ?>">

            <div class="btn btn-danger rounded all-posts-btn sadwo" width='200px'>
                <i class="bi bi-list"></i>
                همه مطالب
            </div>
        </a>

    </div>



    <div class="row bg-light p-2">
        <!-- Slider main container -->
        <div class="swiper">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <?php
                // WP_Query arguments
                $args = array(
                    'post_type'      => 'post', // Use any for any kind of post type
                    'post_status'    => 'publish', // Only published posts
                    'posts_per_page' => 5, // Limit to 5 posts
                    'order'          => 'DESC', // Order by descending
                    'orderby'        => 'date' // Order by date
                );

                // Use get_posts for better performance with small queries
                $posts = get_posts($args);
                if ($posts) :
                    foreach ($posts as $index => $post) :
                        // Store values in variables for better performance
                        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
                        $post_title = esc_html(get_the_title($post->ID));
                        $post_excerpt = esc_html(custom_exerpt_post($post->ID, 10));
                ?>
                        <!-- Slides -->
                        <div class="swiper-slide">
                            <?php if ($thumbnail_url) : ?>
                                <img src="<?php echo esc_url($thumbnail_url); ?>" style="max-height: 250px" class="w-100 d-block m-auto rounded" alt="<?php echo $post_title; ?>" />
                            <?php else : ?>
                                <img style="max-width: 100%;height:auto" src="<?php echo esc_url(get_placeholder_image_url());
                                                                                ?>" alt="Placeholder image" />
                            <?php endif; ?>
                            <div class="swiper-caption my-3 p-3">
                                <a href="<?php the_permalink() ?>">
                                    <h5><?php echo $post_title; ?></h5>
                                </a>
                                <p><?php echo $post_excerpt; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">پستی منتشر نشده است.</div>
                <?php endif; ?>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <!-- If we need scrollbar -->
            <div class="swiper-scrollbar"></div>
        </div>
    </div>
</div>