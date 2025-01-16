<?php
function yas_theme_comment($comment, $args)
{

?>
    <li id="comment-<?php echo $comment->comment_ID; ?>" class=" p-2 my-1">
        <article>
            <div class="article_comments_thumb">
                <?php // echo get_avatar($comment->comment_author_email, $args['avatar_size'], '', $comment->comment_author, ['class' => 'rounded-5']) 
                ?>
            </div>
            <div class="comment-details">
                <div class="comment-meta">

                    <div class="comment-left-meta">
                        <div class="author-name">
                            <?php
                            echo $comment->comment_author
                            ?>
                            <?php

                            $user_data = get_userdata($comment->user_id);

                            if (isset($user_data->roles[0]) && $user_data->roles[0] === 'administrator') :
                            ?>
                                <span class="selected"><i class="bi bi-star-fill text-warning"></i></span>

                            <?php endif
                            ?>


                        </div>
                        <div class="comment-date text-muted fw-lighter date-set-comment"><?php echo get_comment_date('Y-m-d') ?></div>
                        <div class="comment-reply">
                            <?php if (is_user_logged_in()) : ?>
                                <a href="#" class="text-primary reply-comment  p-1" data-bs-toggle="modal" data-bs-target="#exampleModal" data-comment-id="<?php echo $comment->comment_ID ?>" data-comment-author="<?php echo $comment->comment_author ?>">پاسخ</a>

                            <?php endif ?>
                            <?php if (current_user_can('manage_options')) {
                                edit_comment_link('<i class="bi bi-pencil-square text-warning p-2"></i>');
                            }
                            ?>
                        </div>
                    </div>
                    <div class="p-2 mt-2 border rounded-2   overflow-auto   ">
                        <?php if ($comment->comment_approved) : ?>
                            <p><?php echo $comment->comment_content ?></p>
                        <?php else : ?>
                            <div class="alert alert-info p-1">پیام شما ثبت شد پس از تایید مدیر نمایش داده خواهد شد.</div>
                        <?php endif ?>


                    </div>
                </div>
        </article>

    </li>
<?php
}
