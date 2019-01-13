<?php if(Request::getVar("sent", 0, "int")){?>
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<?php translate("message_sent_successfully")?>
</div>
<?php }?>
<?php echo ModuleHelper::buildMethodCallForm(MessageServiceController::class, "sendMessage");?>

<label for="receivers"><?php translate("receivers")?> <span
	class="text-red">*</span></label>
<?php
echo UliCMS\HTML\Input::MultiSelect("receivers[]", array(), ViewBag::get("users"), 5, array(
    "required" => "required",
    "id" => "receivers",
    "class" => "form-control"
));
?>
<div class="form-group voffset2">
	<button type="button" id="select-all-receivers" class="btn btn-default"><?php translate("select_all");?></button>
	<button type="button" id="select-nothing-receivers"
		class="btn btn-default"><?php translate("select_nothing");?></button>
</div>
<label for="message"><?php translate("message");?> <span
	class="text-red">*</span></label>
<?php
echo UliCMS\HTML\Input::TextArea("message", "", 7, 80, array(
    "required" => "required",
    "id" => "message",
    "class" => "form-control",
    "maxlength" => "900"
));
?>
<small><?php translate("max_900_letters")?></small>
<div class="form-group voffset2">
	<button type="submit" class="btn btn-primary">
		<i class="fas fa-envelope"></i> <?php translate("send_message");?></button>
</div>
<?php echo ModuleHelper::endForm();?>
<?php
enqueueScriptFile(ModuleHelper::buildRessourcePath("message_service", "js/backend.js"));
combinedScriptHtml();
?>