<div class="container ">
    <div class=" m-5">
        تعداد مطالب یافت شده <span class="text-info fw-bolder "> <?php echo $wp_query->found_posts ?></span>
    </div>
    <div class="row">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();

        ?>
                <div class="col-lg-4 col-md-6 ">
                    <div class="education_block_grid shadow-sm p-3 rounded h-100 d-flex flex-column justify-content-between">

                        <div class="education_block_thumb">

                            <a class="pic-main" tabindex="-1" href="<?php echo get_the_permalink() ?>">
                                <div class="article_featured_image">

                                    <?php
                                    $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
                                    if ($thumbnail_url) : ?>
                                        <img src="<?php echo esc_url($thumbnail_url); ?>" style="max-height: 250px;width:auto !important" class="img-fluid w-100 d-block m-auto rounded" alt="<?php echo $post_title; ?>" />
                                    <?php else : ?>
                                        <img src="<?php // echo esc_url(get_placeholder_image_url()); 
                                                    ?>" alt="Placeholder image" />
                                    <?php endif; ?>
                                </div>
                            </a>



                        </div>
                        <div class="education_block_body">
                            <h4 class="bl-title"><a href="<?php the_permalink() ?>"> <?php echo  get_the_title() ?></a></h4>
                            <p><?php echo custom_exerpt_post(get_the_ID(), 20) ?></p>
                        </div>

                        <div class="education_block_footer">
                            <div class="education_block_author">
                                <div class="path-img"><a href="<?php the_permalink() ?>"><?php echo get_avatar(get_the_author_meta('email'), '35') ?></a></div>
                                <div class="text-muted fw-lighter">نویسنده:<a href="<?php the_permalink() ?>"> <?php echo get_the_author() ?> </a></div>
                            </div>

                        </div>

                    </div>
                </div>
            <?php endwhile ?>
        <?php else : ?>
            <div class="alert alert-info fs-6" style="width: 100%;"> کاربر گرامی: برای مطلب <b>
                    <?php echo $_GET['s'] ?>
                </b> نتیجه ای یافت نشد!!!</div>

            <!-- <div class="single_widgets widget_tags" style="width: 100%;border:none">
                <h4 class="title my-5"> تگ های پربازدید</h4>
                <?php if (function_exists('wp_tag_cloud')) : ?>

                    <ul>
                        <?php $tags = wp_tag_cloud('smallest=8&largest=14&format=array');
                        foreach ($tags as $tag)  echo '<li>' . $tag . '</li>'; ?>

                    </ul>

                <?php else :
                ?>
                    <div class="alert wrning-alert">no tags here</div>
                <?php endif ?>

            </div> -->
        <?php endif ?>
    </div>
    <div class="comment-pagination">

        <?php the_posts_pagination() ?>
    </div>
    <?php wp_reset_postdata() ?>

</div>