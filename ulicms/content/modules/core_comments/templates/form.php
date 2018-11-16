<?php if(Vars::get("comments_enabled")){?>
<h3><?php translate("write_a_comment");?></h3>
<div class="comments">
<?php echo ModuleHelper::buildMethodCallForm(CommentsController::class, "postComment");?>
<?php echo UliCMS\HTML\Input::Hidden("content_id", Vars::get("content_id"));?>

    <div>
		<label for="author_name"><?php translate("your_name")?>
<span class="text-danger">*</span></label>
		<div>
    <?php
    
    echo UliCMS\HTML\Input::TextBox("author_name", "", "text", array(
        "class" => "form-control",
        "required" => "required"
    ));
    ?>
    </div>
	</div>
	<div>
		<label for="author_email"><?php translate("your_email")?></label>
		<div>
    <?php
    echo UliCMS\HTML\Input::TextBox("author_email", "", "email", array(
        "class" => "form-control"
    ));
    ?>
    </div>
	</div>
	<label for="author_url"><?php translate("your_website")?></label>
	<div>
    <?php
    echo UliCMS\HTML\Input::TextBox("author_url", "", "url", array(
        "class" => "form-control"
    ));
    ?>
    </div>
	<div>
		<div class="comment-text">
			<label for="text"><?php translate("text")?>
			<span class="text-danger">*</span></label>
<?php
    echo UliCMS\HTML\Input::TextArea("text", "", 10, 80, array(
        "required" => "required",
        "class" => "form-control"
    ))?>	</div>
	</div>
<?php
    $checkbox = new PrivacyCheckbox(getCurrentLanguage(true));
    if ($checkbox->isEnabled()) {
        echo $checkbox->render();
    }
    ?>
<p>
		<button type="submit" class="btn btn-primary"><?php translate("post_comment")?></button>
	</p>
</div>
<?php echo ModuleHelper::endForm();?>
<?php }?>