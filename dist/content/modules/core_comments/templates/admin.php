<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Backend\BackendPageRenderer;
use App\Constants\CommentStatus;
use App\HTML\Input;
use App\HTML\ListItem;

$controller = \App\Helpers\ModuleHelper::getMainController('core_comments');
$defaultStatus = $controller->_getDefaultStatus();

$selectedStatus = Request::getVar('status', $defaultStatus, 'str');
$content_id = Request::getVar('content_id', 0, 'int');
$limit = Request::getVar('limit', $controller->_getDefaultLimit(), 'int');

$comments = is_array(BackendPageRenderer::getModel()) ?
        BackendPageRenderer::getModel() :
        $controller->_getResults($selectedStatus, $content_id, $limit);

$stati = [
    new ListItem(
        CommentStatus::SPAM,
        get_translation(CommentStatus::SPAM)
    ),
    new ListItem(
        CommentStatus::PENDING,
        get_translation(CommentStatus::PENDING)
    ),
    new ListItem(
        CommentStatus::PUBLISHED,
        get_translation(CommentStatus::PUBLISHED)
    )
];

$contents = ContentFactory::getAllWithComments('title');

$contentSelect = [];

$contentSelect[] = new ListItem(0, '[' . get_translation('every') . ']');
foreach ($contents as $content) {
    $language = getLanguageNameByCode($content->language);
    $type = get_translation($content->type);
    $contentSelect[] = new ListItem(
        $content->id,
        "{$content->title} ({$type} - {$language})"
    );
}

$actionSelect = [
    new ListItem('', '[' . get_translation('select_action') . ']'),
    new ListItem('mark_as_spam', get_translation('mark_as_spam')),
    new ListItem('mark_as_read', get_translation('mark_as_read')),
    new ListItem('mark_as_unread', get_translation('mark_as_unread')),
    new ListItem('publish', get_translation('publish')),
    new ListItem('unpublish', get_translation('unpublish')),
    new ListItem('delete', get_translation('delete'))
];
?>

<?php echo Template::executeModuleTemplate('core_content', 'icons.php'); ?>

<h1><?php translate('comments_manage'); ?></h1>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm(
    CommentsController::class,
    'filterComments',
    [],
    'get'
);
?>
<div class="row">
    <div class="col col-12 col-lg-4">
        <label for="status"><?php translate('status'); ?></label>
        <?php
        echo Input::singleSelect('status', $selectedStatus, $stati, 1);
?>
    </div>
    <div class="col col-12 col-lg-4">
        <label for="status"><?php translate('contents'); ?></label>
        <?php
echo Input::singleSelect('content_id', $content_id, $contentSelect, 1);
?>
    </div>
    <div class="col col-12 col-lg-4">
        <label for="status"><?php translate('limit_results'); ?></label>
        <?php
echo Input::textBox('limit', $limit, 'number', [
    'step' => '10',
    'min' => '0'
]);
?>
    </div>
</div>
<div class="voffset3">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-search"></i> <?php translate('search'); ?></button>
</div>
<?php echo \App\Helpers\ModuleHelper::endForm(); ?>
<?php
echo \App\Helpers\ModuleHelper::buildMethodCallForm(
    CommentsController::class,
    'doAction',
    [
        'referrer' => getCurrentURL()
    ],
    'post',
    [
        'class' => 'voffset3',
        'id' => 'comments'
    ]
);
?>
<div class="scroll">
    <table class="tablesorter table">
        <thead>
            <tr>
                <th class="no-sort"><?php
                    echo Input::checkBox('select_all', false, '', [
                        'class' => 'select-all',
                        'data-target' => '.comment-checkbox'
                    ]);
?></th>
                <th><?php translate('date'); ?></th>
                <th><?php translate('status'); ?></th>
                <th><?php translate('author'); ?></th>
                <th><?php translate('comment'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment) { ?>
                <?php
                $url = $comment->getAuthorUrl() && strlen($comment->getAuthorUrl()) > 30 ?
    substr($comment->getAuthorUrl(), 0, 30) . '...' : $comment->getAuthorUrl();
                ?>
                <?php $content = $comment->getContent(); ?>
                <tr class="<?php
                if (! $comment->isRead()) {
                    echo 'unread';
                }
                ?>">
                    <td><?php
                        echo Input::checkBox(
                            'comments[]',
                            false,
                            $comment->getId(),
                            [
                                'class' => 'checkbox comment-checkbox',
                                'data-select-all-checkbox' => '.select-all',
                                'data-checkbox-group' => '.comment-checkbox'
                            ]
                        );
                ?></td>
                    <td>
                        <?php esc(date('Y-m-d H:i:s', $comment->getDate())); ?>
                    </td>
                    <td><?php translate($comment->getStatus()); ?></td>
                    <td><?php esc($comment->getAuthorName()); ?>
                        <br />
                        <?php if ($comment->getAuthorEmail()) { ?>
                            <?php esc($comment->getAuthorEmail()); ?>
                            <br />
                        <?php } ?>
                        <?php if ($comment->getAuthorUrl()) { ?>
                            <a href="<?php esc($comment->getAuthorUrl()); ?>"
                               target="_blank"
                               rel="nofollow"><?php esc($url); ?></a>
                        <?php } ?></td>
                    <td>
                        <p>
                            <a href="<?php esc($comment->getCommentUrl()); ?>"
                               target="_blank">
                                <strong>
                                    <?php esc($content->title); ?>
                                </strong>
                            </a>
                        </p>
                        <p>
                            <span
                                class="has-pointer ajax-alert"
                                data-url="<?php
                        echo
                        \App\Helpers\ModuleHelper::buildMethodCallUrl(
                            CommentsController::class,
                            'getCommentText',
                            'id=' . $comment->getID()
                        );
                ?>"><?php esc(getExcerpt($comment->getText())); ?></span>
                        </p>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col col-6">
        <?php
        echo Input::singleSelect('action', '', $actionSelect, 1);
?>
    </div>
    <div class="col col-6">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-running"></i>
            <?php translate('do_action'); ?></button>
    </div>
</div>
<?php
echo \App\Helpers\ModuleHelper::endForm();
enqueueScriptFile(\App\Helpers\ModuleHelper::buildRessourcePath(
    'core_comments',
    'js/admin.js'
));
combinedScriptHtml();
