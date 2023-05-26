<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\CommentStatus;
use App\Security\PrivacyCheckbox;

if (\App\Storages\Vars::get('comments_enabled')) {
    ?>
    <?php
    $comment_published = Request::getVar('comment_published');
    $cssClass = 'alert ';
    if ($comment_published) {
        switch ($comment_published) {
            case CommentStatus::PUBLISHED:
            case CommentStatus::PENDING:
                $cssClass .= ' alert-success';
                break;
            case CommentStatus::SPAM:
                $cssClass .= ' alert-warning';
                break;
            default:
                $cssClass .= ' alert-info';
                break;
        }
        $cssClass .= '  alert-dismissable fade in';
        ?>
        <div class="<?php esc($cssClass); ?>">
            <a
                href="#"
                class="close"
                data-dismiss="alert"
                aria-label="close">&times;</a>
                <?php translate("comment_published_{$comment_published}"); ?>
        </div>
    <?php }
    ?>

    <h3><?php translate('write_a_comment'); ?></h3>
    <div class="comments">
        <?php
        echo \App\Helpers\ModuleHelper::buildMethodCallForm(
            CommentsController::class,
            'postComment',
            [],
            'post',
            [
                'autocomplete' => 'off'
            ]
        );
    ?>
        <?php
    echo App\HTML\Input::hidden(
        'content_id',
        \App\Storages\Vars::get('content_id')
    );
    ?>

        <div>
            <label for="author_name"><?php translate('your_name'); ?>
                <span class="text-danger">*</span></label>
            <div>
                <?php
            echo App\HTML\Input::textBox(
                'author_name',
                '',
                'text',
                [
                    'class' => 'form-control',
                    'required' => 'required'
                ]
            );
    ?>
            </div>
        </div>
        <div>
            <label for="author_email"><?php translate('your_email'); ?></label>
            <div>
                <?php
    echo App\HTML\Input::textBox(
        'author_email',
        '',
        'email',
        [
            'class' => 'form-control'
        ]
    );
    ?>
            </div>
        </div>
        <label for="author_url"><?php translate('your_website'); ?></label>
        <div>
            <?php
            echo App\HTML\Input::textBox('author_url', '', 'url', [
                'class' => 'form-control'
            ]);
    ?>
        </div>
        <div>
            <div class="comment-text">
                <p>
                    <label for="text"><?php translate('text'); ?>
                        <span class="text-danger">*</span></label>
                    <?php
            echo App\HTML\Input::textArea('text', '', 10, 80, [
                'required' => 'required',
                'class' => 'form-control'
            ]);
    ?>
                </p>
            </div>
        </div>
        <?php
        $checkbox = new PrivacyCheckbox(getCurrentLanguage(true));
    if ($checkbox->isEnabled()) {
        echo $checkbox->render();
    }
    ?>
        <input type="url" name="my_homepage_url" class="antispam_honeypot"
               value="" autocomplete="nope">
        <p>
            <button type="submit" class="btn btn-primary"><?php
            translate(
                'post_comment'
            );
    ?></button>
        </p>
    </div>
    <?php
    echo \App\Helpers\ModuleHelper::endForm();
}
