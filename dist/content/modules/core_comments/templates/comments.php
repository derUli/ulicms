<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\CommentStatus;
use App\Helpers\DateTimeHelper;
use App\Models\Content\Comment;

$comments = Comment::getAllByStatus(
    CommentStatus::PUBLISHED,
    \App\Storages\Vars::get('content_id')
);

$last = end($comments);
reset($comments);

?>
<?php echo Template::executeModuleTemplate('core_comments', 'form.php'); ?>
<?php if (count($comments) > 0) { ?>
    <div class="comment-list">
        <h3><?php translate('comments'); ?></h3>
        <?php
        foreach ($comments as $comment) {
            ?>  
            <div class="comment" id="comment-<?php echo $comment->getId(); ?>">
                <strong><?php translate('date'); ?>:</strong>
                <?php echo DateTimeHelper::timestampToFormattedDateTime($comment->getDate()); ?>
                <br /> <strong><?php translate('name'); ?>:</strong>
                <?php esc($comment->getAuthorName()); ?>
                <br />
                <?php if ($comment->getAuthorUrl()) { ?>
                    <strong><?php translate('website'); ?>:</strong> <a
                        href="<?php esc($comment->getAuthorUrl()); ?>"
                        rel="nofollow"
                        target="_blank"><?php
                            esc(
                                $comment->getAuthorUrl()
                            );
                    ?></a> <br />
                <?php } ?>
                <?php
                echo make_links_clickable(
                    App\HTML\text($comment->getText())
                );
            ?>
            </div>
            <?php if ($comment != $last) { ?>
                <hr />
            <?php } ?>
        <?php }
        ?>
    </div>
    <?php
}
