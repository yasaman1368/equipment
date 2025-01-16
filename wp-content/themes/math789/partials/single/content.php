<?php if (have_posts()):
    while (have_posts()) : the_post();
?>
        <section class="bg-light">

            <div class="container">

                <!-- row Start -->
                <div class="row g-3">

                    <!-- Blog Detail -->
                    <div class="col-lg-8 col-md-12 col-sm-12 col-12  ">
                        <div class="article_detail_wrapss single_article_wrap format-standard bg-white rounded  shadow-sm p-3 m-3">
                            <div class="article_body_wrap">

                                <div class="article_featured_image">

                                    <?php
                                    $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');
                                    if ($thumbnail_url) : ?>
                                        <img src="<?php echo esc_url($thumbnail_url); ?>" style="max-height: 250px;width:auto !important" class="img-fluid w-100 d-block m-auto rounded" alt="<?php echo $post_title; ?>" />
                                    <?php else : ?>
                                        <div class="defualt-img-container">

                                            <img style="max-width: 100%;height:auto" class="rounded" src="<?php echo esc_url(get_placeholder_image_url());
                                                                                            ?>" alt="Placeholder image" />
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="article_top_info m-4">
                                    <ul class="article_middle_info d-flex text-muted fw-lighter">

                                        <li class="m-2"><a href="#"><span class="icons"><i class="bi bi-person"></i></span><?php echo get_the_author_meta('display_name') ?></a></li>
                                        <li class="m-2"><a href="#"><span class="icons"><i class="ti-comment-alt"></i></span><span><?php echo get_comments_number() ?></span> نظر ثبت شده</a></li>
                                    </ul>
                                </div>
                                <h2 class="post-title"><?php the_title() ?></h2>
                                <div class="text-muted lh-lg main-content-post" style="text-align: justify;"><?php the_content() ?></div>

                                <div class="article_bottom_info">
                                    <!-- <div class="post-tags">
                                        <h4 class="pbm-title">تگ های پربازدید</h4>
                                        <ul class="list d-flex">
                                            <li class="p-2 btn btn-outline-danger m-1"><a href="#">کنکور</a></li>
                                            <li class="p-2 btn btn-outline-danger m-1"><a href="#">موفقیت</a></li>
                                            <li class="p-2 btn btn-outline-danger m-1"><a href="#">تدریس</a></li>
                                        </ul>
                                    </div> -->
                                    <!-- <div class="post-share">
                                        <h4 class="pbm-title">شبکه های اجتماعی</h4>
                                        <ul class="list">
                                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                            <li><a href="#"><i class="fab fa-vk"></i></a></li>
                                            <li><a href="#"><i class="fab fa-tumblr"></i></a></li>
                                        </ul>
                                    </div> -->
                                </div>
                                <div class="single_article_pagination d-flex justify-content-space-between justify-content-between">
                                    <?php $previous_post = get_previous_post(true, '', 'category') ?>
                                    <?php if (!empty($previous_post)) : ?>
                                        <div class="prev-post btn btn-outline-info">
                                            <a href="<?php echo get_permalink($previous_post->ID) ?>" class="theme-bg">
                                                <div class="title-with-link">
                                                    <span class="intro"> <?php echo mb_substr($previous_post->post_title, 0, 12) . ' ' . '...' ?></span>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif ?>
                                    <div class="article_pagination_center_grid">
                                        <a href="#"><i class="ti-layout-grid3"></i></a>
                                        <?php $next_post = get_next_post(true, '', 'category') ?>
                                        <?php if (!empty($next_post)) : ?>
                                    </div>

                                    <div class="next-post btn btn-outline-info">
                                        <a href="<?php echo get_permalink($next_post->ID) ?>" class="theme-bg">
                                            <div class="title-with-link">

                                                <span class="intro"><?php echo mb_substr($next_post->post_title, 0, 12) . ' ' . '...'  ?></span>
                                            </div>
                                        </a>
                                    </div>
                                <?php endif ?>
                                </div>

                            </div>
                        </div>

                        <!-- Author Detail -->
                        <!-- <div class="  shadow-sm p-3 format-standard bg-white rounded m-3">

                            <div class="article_posts_thumb text-center">
                                <span class="img"><img class="img-fluid" src="" alt=""></span>
                                <h3 class="pa-name"><?php the_author(); ?></h3>
                                <ul class="social-links">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                                <p class="pa-text"><?php echo get_the_author_meta('description') ?></p>
                            </div>

                        </div> -->

                        <!-- Blog Comment -->
                        <?php comments_template(); ?>


                    </div>

                    <!-- Single blog Grid -->
                    <div class="col-lg-4 col-md-12 col-sm-12 col-12 ">

                        <!-- Searchbard -->
                        <div class="single_widgets widget_search bg-white shadow-sm  p-2 rounded m-4 p-3">
                            <h4 class="title">جستجو</h4>
                            <form action="<?php echo site_url() ?>" class="sidebar-search-form">
                                <div class="input-group mb-3">
                                    <input type="search" class="form-control" name="s" placeholder="جستجو..." aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>

                                </div>

                            </form>
                        </div>

                        <!-- Categories -->
                        <!-- <div class="single_widgets widget_category bg-white shadow-sm  p-2 rounded m-4 p-3">
                            <h4 class="title">دسته بندی ها</h4>
                            <ul>
                                <li><a href="#">سبک زندگی <span>09</span></a></li>
                                <li><a href="#">تدریس خصوصی <span>12</span></a></li>
                                <li><a href="#">آموزش آنلاین <span>19</span></a>
                                </li>
                                <li><a href="#">برندسازی <span>17</span></a></li>
                                <li><a href="#">موزیک <span>10</span></a></li>
                            </ul>
                        </div> -->

                        <!-- Trending Posts -->
                        <!-- <div class="single_widgets widget_thumb_post bg-white shadow-sm  p-2 rounded m-4 p-3">
                            <h4 class="title">پرمخاطب</h4>
                            <ul>
                                <li>
                                    <span class="left">
                                        <img src="" alt="" class="">
                                    </span>
                                    <span class="right">
                                        <a class="feed-title" href="#">در الکامپ امسال چه خبر است؟</a>
                                        <span class="post-date"><i class="ti-calendar"></i>10دقیقه پیش</span>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">
                                        <img src="" alt="" class="">
                                    </span>
                                    <span class="right">
                                        <a class="feed-title" href="#">چگونه بهانه آوردن را متوقف کنید؟</a>
                                        <span class="post-date"><i class="ti-calendar"></i>2ساعت پیش</span>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">
                                        <img src="" alt="" class="">
                                    </span>
                                    <span class="right">
                                        <a class="feed-title" href="#">مشخصات اولین تبلت فراسو</a>
                                        <span class="post-date"><i class="ti-calendar"></i>4ساعت پیش</span>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">
                                        <img src="" alt="" class="">
                                    </span>
                                    <span class="right">
                                        <a class="feed-title" href="#">مالزی به دنبال دانشجویان آمریکایی</a>
                                        <span class="post-date"><i class="ti-calendar"></i>7ساعت پیش</span>
                                    </span>
                                </li>
                                <li>
                                    <span class="left">
                                        <img src="" alt="" class="">
                                    </span>
                                    <span class="right">
                                        <a class="feed-title" href="#">فیلترینگ 100 هزار واژه از سوی گوگل</a>
                                        <span class="post-date"><i class="ti-calendar"></i>3روز پیش</span>
                                    </span>
                                </li>
                            </ul>
                        </div> -->

                        <!-- Tags Cloud -->
                        <!-- <div class="single_widgets widget_tags bg-white shadow-sm  p-2 rounded m-4 p-3">
                            <h4 class="title">تگ</h4>
                            <ul>
                                <li><a href="#">سبک زندگی</a></li>
                                <li><a href="#">کنکور 1399</a></li>
                                <li><a href="#">تدریس</a></li>
                                <li><a href="#">برندسازی</a></li>
                                <li><a href="#">موزیک</a></li>
                            </ul>
                        </div> -->

                    </div>

                </div>
                <!-- /row -->
            </div>
        </section>
    <?php
    endwhile;
else : ?>
    <div class="alert alert-info">پستی وجود ندارد!!!</div>
<?php endif; ?>