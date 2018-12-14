<?php echo ModuleHelper::buildMethodCallForm(FrontendLoginController::class, "doLogin");?>

<?php if(Request::getVar("error")){?>
<div class="alert alert-danger alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
<?php translate(Request::getVar("error"));?></div>
<?php }?>
<input type="text" class="form-control" name="user"
	placeholder="<?php translate("username");?>" />
<input type="password" class="form-control" name="password"
	placeholder="<?php translate("password")?>" />
<button class="btn btn-lg btn-primary btn-block" type="submit"><?php translate("login");?></button>
<?php echo ModuleHelper::endForm();?>
<?php
enqueueScriptFile(ModuleHelper::buildModuleRessourcePath("bootstrap", "js/bootstrap.js"));
combinedScriptHtml();
?>
