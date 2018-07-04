<?php
$field = ViewBag::get ( "field" );
$value = ViewBag::get ( "field_value" );
if (is_null ( $value )) {
	$value = $field->defaultValue;
}
?>
<div class="custom-field"
	data-field-name="<?php Template::escape($field->name);?>">
	<p>
		<strong><?php translate($field->title);?> <?php if($field->required) echo "*";?></strong><br />
		<input type="number"
			name="<?php Template::escape(ViewBag::get("field_name"));?>"
			value="<?php Template::escape($value);?>" step="any"
			<?php if($field->required) echo "required";?>
			<?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes);?>>
		<?php if($field->helpText){?>
	<br /> <small><?php translate($field->helpText);?></small>
<?php }?>
</p>
</div>