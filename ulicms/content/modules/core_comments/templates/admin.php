<?php
use UliCMS\Backend\BackendPageRenderer;
use UliCMS\Data\Content\Comment;
use UliCMS\HTML\Input;
use UliCMS\HTML\ListItem;

$controller = ModuleHelper::getMainController("core_comments");
$defaultStatus = $controller->getDefaultStatus();

$comments = is_array(BackendPageRenderer::getModel()) ? BackendPageRenderer::getModel() : Comment::getAllByStatus($defaultStatus);

$stati = array(
    new ListItem(CommentStatus::SPAM, get_translation(CommentStatus::SPAM)),
    new ListItem(CommentStatus::PENDING, get_translation(CommentStatus::PENDING)),
    new ListItem(CommentStatus::PUBLISHED, get_translation(CommentStatus::PUBLISHED))
);

$selectedStatus = Request::getVar("status", $defaultStatus, "str");
$content_id = Request::getVar("content_id", 0, "int");

$contents = ContentFactory::getAllWithComments("title");

$contentSelect = array();

$contentSelect[] = new ListItem(0, "[" . get_translation("every") . "]");
foreach ($contents as $content) {
    $language = getLanguageNameByCode($content->language);
    $type = get_translation($content->type);
    $contentSelect[] = new ListItem($content->id, "{$content->title} ({$type} - {$language})");
}

?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("contents");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<?php

echo ModuleHelper::buildMethodCallForm(CommentsController::class, "filterComments", array(), "get");
?>
<div class="form-group">
	<label for="status"><?php translate("status");?></label>
	<?php
echo Input::SingleSelect("status", $selectedStatus, $stati, 1);
?>
</div>
<div class="form-group">
	<label for="status"><?php translate("contents");?></label>
	<?php
echo Input::SingleSelect("content_id", $content_id, $contentSelect, 1);
?>
</div>
<p>
	<button type="submit" class="btn btn-primary"><?php translate("search");?></button>
</p>
<?php echo ModuleHelper::endForm();?>

<h1><?php translate("comments_manage");?></h1>
<div class="alert alert-warning">
	<p>Work in Progress</p>
</div>
<div class="scroll">
	<table class="tablesorter table">
		<thead>
			<tr>
				<td><?php
    echo Input::CheckBox("select_all", false, "", array(
        "disabled" => "disabled"
    ));
    ?></td>
				<th><?php translate("date");?></th>
				<th><?php translate("status");?></th>
				<th><?php translate("author");?></th>
				<th><?php translate("comment")?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($comments as $comment){?>
		<?php $url = strlen($comment->getAuthorUrl()) > 30 ? substr($comment->getAuthorUrl(),0, 30) ."...": $comment->getAuthorUrl();?>
		<?php $content = $comment->getContent();?>
			<tr>
				<td><?php
    echo Input::CheckBox("comments[]", false, $comment->getId(), array(
        "disabled" => "disabled"
    ));
    ?></td>
				<td><?php esc(date("Y-m-d H:i:s", $comment->getDate()));?></td>
				<td><?php translate($comment->getStatus());?></td>
				<td><?php esc($comment->getAuthorName());?>
					<br />
						<?php if($comment->getAuthorEmail()){?>
						<?php esc($comment->getAuthorEmail());?>
					<br />
						<?php }?>
					<?php if($comment->getAuthorUrl()){?>
					<a href="<?php esc($comment->getAuthorUrl());?>" target="_blank"
					rel="nofollow"><?php esc($url);?></a>
					<?php }?></td>
				<td>
					<p>
						<strong><?php esc($content->title);?></strong>
					</p>
					<p>
						<a href="#"
							data-url="<?php echo ModuleHelper::buildMethodCallUrl(CommentsController::class, "getCommentText", "id=".$comment->getID());?>"
							class="ajax-alert"><?php esc(getExcerpt($comment->getText()));?></a>
					</p>
				</td>
			</tr>
		<?php }?>
		</tbody>
	</table>
</div>
<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath("core_comments", "js/admin.js"));
combinedScriptHtml();
?>