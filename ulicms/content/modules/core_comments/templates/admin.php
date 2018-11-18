<?php
use UliCMS\Backend\BackendPageRenderer;
use UliCMS\Data\Content\Comment;
use UliCMS\HTML\Input;

$comments = BackendPageRenderer::getModel () ? BackendPageRenderer::getModel () : Comment::getAll ();
?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("contents");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("comments_manage");?></h1>
<div class="alert alert-warning">
	<p>Work in Progress</p>
</div>
<table class="tablesorter table">
	<thead>
		<td><?php
		
		echo Input::CheckBox ( "select_all", false, "", array (
				"disabled" => "disabled" 
		) );
		?></td>
		<th><?php translate("date");?></th>
		<th><?php translate("status");?></th>
		<th><?php translate("author_name");?></th>
		<th><?php translate("author_email");?></th>
		<th><?php translate("author_url");?></th>
		<th><?php translate("text")?></th>
	</thead>
	<tbody>
	<?php foreach($comments as $comment){?>
	<tr>
			<td><?php
		
		echo Input::CheckBox ( "comments[]", false, $comment->getId (), array (
				"disabled" => "disabled" 
		) );
		?></td>

			<td><?php esc(date("Y-m-d H:i:s", $comment->getDate()));?></td>
			<td><?php translate($comment->getStatus());?></td>
			<td><?php esc($comment->getAuthorName());?></td>
			<td><?php esc($comment->getAuthorEmail());?></td>
			<td><a href="<?php esc($comment->getAuthorUrl());?>" target="_blank"
				<?php $url = strlen($comment->getAuthorUrl()) > 30 ? substr($comment->getAuthorUrl(),0, 30) ."...": $comment->getAuthorUrl();?>
				rel="nofollow"><?php esc($url);?></td>
			<td><a href="#"
				data-url="<?php echo ModuleHelper::buildMethodCallUrl(CommentsController::class, "getCommentText", "id=".$comment->getID());?>"
				class="ajax-alert"><?php esc(getExcerpt($comment->getText()));?></a></td>
		</tr>
	<?php }?>
	</tbody>
</table>
<?php
enqueueScriptFile ( ModuleHelper::buildRessourcePath ( "core_comments", "js/admin.js" ) );
combinedScriptHtml ();
?>