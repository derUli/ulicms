<form
	action="<?php Template::escape(ModuleHelper::buildAdminURL("simple_md5_generator"));?>"
	method="post">
	<strong><?php translate("enter_text");?></strong><br />
	<p>
		<input type="text" name="text"
			value="<?php Template::escape(Request::getVar("text"));?>">
	</p>
	<p>
		<input type="submit" value="<?php translate("generate");?>">
	</p>
		<?php csrf_token_html();?>
		<?php if(ViewBag::get("result")){?>
		
		<p>
		<strong><?php translate("result");?></strong><br /><?php  Template::escape(ViewBag::get("result"));?>
		</p>
		<?php } else {?>
		<p>&nbsp;</p>
		<?php }?>
</form>