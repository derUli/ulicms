<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
if (is_null($value)) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field"
	data-field-name="<?php Template::escape($field->name);?>">
	<img src="gfx/preview.png" class="img-thumbnail"
		id="thumbnail-<?php Template::escape(ViewBag::get("field_name"));?>"
		style="display: none">
	<p>
		<strong><?php translate($field->title);?> <?php if($field->required) echo "*";?></strong><br />
		<input type="text"
			name="<?php Template::escape(ViewBag::get("field_name"));?>"
			id="field-<?php Template::escape(ViewBag::get("field_name"));?>"
			value="<?php Template::escape($value);?>" class="kcfinder"
			data-kcfinder-type="<?php ViewBag::get("kcfinder_type") ? esc(ViewBag::get("kcfinder_type")) : "files"?>"
			<?php if($field->required) echo "required";?>
			<?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes);?>
			readonly>
	
	
	<div class="voffset2">
		<a href="#" class="btn btn-default clear-field"
			data-for="#field-<?php Template::escape(ViewBag::get("field_name"));?>"
			class="btn btn-default"><i class="fa fa-eraser"></i> <?php translate("clear")?></a>
	</div>
		<?php if($field->helpText){?>
	<br /> <small><?php translate($field->helpText);?></small>
<?php }?>
</p>
</div>