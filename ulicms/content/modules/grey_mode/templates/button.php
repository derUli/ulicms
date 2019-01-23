<?php echo ModuleHelper::buildMethodCallForm(GreyMode::class, "toggleGreyModePost");?>
<div class="form-group">
<?php if($_SESSION["grey_mode"]){?>
	<button type="submit" class="btn btn-default"><i class="far fa-check-square"></i> <?php translate("grey_mode")?></button>
	<?php } else {?>
<button type="submit" class="btn btn-default"><i class="far fa-square"></i> <?php translate("grey_mode")?></button>

<?php }?>
</div>
<?php echo ModuleHelper::endForm();?>