<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">ثبت دیدگاه</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                <?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>
                    <div class="alert alert-info">لطفا برای ثبت نظر وارد سایت شوید.</div>
                <?php else : ?>
                    <?php $user = get_userdata(get_current_user_id()) ?>
                    <p><span class="text-info"><?php echo $user->display_name ?></span> عزیز شما مجاز به ارسال پیام هستید</p>
                    <form action="<?php echo site_url() . '/wp-comments-post.php' ?>" method="post">
                        <div class="row">
                            <?php if (!is_user_logged_in()) : ?>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="author" placeholder="نام کامل">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="email" placeholder="ایمیل معتبر">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="url" placeholder="وب سایت ">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">

                                    <p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"> <label for="wp-comment-cookies-consent">ذخیره نام، ایمیل و وبسایت من در مرورگر برای زمانی که دوباره دیدگاهی می‌نویسم.</label></p>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h6 class="text-muted">در پاسخ به <span id="replytext" class="text-info"></span></h6>

                                <div class="form-group">
                                    <textarea id="reply" name="comment" class="form-control" cols="30" rows="6" placeholder="نظر خود را بنویسید..."></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">

                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="form-group">

                                <input name="submit" type="submit" id="submit" class="btn btn-success " value="فرستادن دیدگاه">
                                <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID()    ?>" id="comment_post_ID">
                                <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                                <?php if (is_user_logged_in()) : ?>
                                    <input type="hidden" id="_wp_unfiltered_html_comment_disabled" name="_wp_unfiltered_html_comment" value="<?php echo wp_create_nonce() ?>">
                                <?php endif ?>
                            </div>
                        </div>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<!-- commmments -->
<div class="bg-white rounded m-3  p-3 shadow-sm format-standard">

    <div class="comment-area">
        <?php if (comments_open()): ?>
            <div class="all-comments">
                <?php if (get_comments_number() == 0) : ?>
                    <h6 class="comments-title bg-warning p-2 rounded">اولین نفری باشید که برای این مطلب دیدگاه می گذارید.</h6>
                <?php else : ?>
                    <h3 class="comments-title"><?php echo get_comments_number() ?> دیدگاه</h3>
                <?php endif ?>
                <div>
                    <ul class="p-0">

                        <?php

                        $args = [
                            'callback' => 'yas_theme_comment',
                            'style' => 'ul',
                            'avatar_size' => 70
                        ];
                        wp_list_comments($args) ?>
                    </ul>
                    <div class="comment-pagination">
                        <!-- pagination comment -->
                        <?php
                        paginate_comments_links(array(
                            'prev_text'  => 'قبلی',
                            'next_text' => 'بعدی'
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="comment-box submit-form">
                <hr>
                <h6 class="reply-title">ثبت دیدگاه</h6>
                <?php if (!is_user_logged_in()) : ?>
                    <div class="alert alert-info">لطفا برای ثبت نظر <a class="fw-bold text-info" href="<?php echo home_url('panel/login') ?>">وارد</a> سایت شوید.</div>
                <?php else : ?>
                    <div class="comment-form">
                        <?php $user = get_userdata(get_current_user_id()) ?>
                        <p class=" rounded-2 p-2 "><span class="text-danger"><?php echo $user->display_name  ?></span> عزیز شما مجاز به ارسال پیام هستید</p>
                        <form action="<?php echo site_url() . '/wp-comments-post.php' ?>" method="post">
                            <div class="row">


                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <h6 id="replytext"></h6>
                                    <div class="form-group">
                                        <textarea id="reply" name="comment" class="form-control" cols="30" rows="6" placeholder="نظر خود را بنویسید..."></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">

                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-start">
                                <div class="form-group  ">

                                    <input name="submit" type="submit" id="submit" class="btn btn-outline-secondary m-3 " value="فرستادن دیدگاه">
                                    <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID() ?>" id="comment_post_ID">
                                    <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                                    <?php if (is_user_logged_in()) : ?>
                                        <input type="hidden" id="_wp_unfiltered_html_comment_disabled" name="_wp_unfiltered_html_comment" value="<?php echo wp_create_nonce() ?>">
                                    <?php endif ?>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">نوشتن دیدگاه برای این مطلب بسته است.</div>
        <?php endif; ?>
    </div>

</div>