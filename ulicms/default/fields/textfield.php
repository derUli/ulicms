<?php
$field = ViewBag::get ( "field" );
$value = ViewBag::get ( "field_value" );
if (is_null ( $value )) {
	$value = $field->defaultValue;
}
?>
<p>
	<strong><?php Template::escape($field->title);?> <?php if($field->required) echo "*";?></strong><br />
	<input type="text" name="<?php Template::escape($field->name);?>"
		value="<?php Template::escape($value);?>"
		<?php if($field->required) echo "required";?>
		<?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes);?>>
		<?php if($field->helpText){?>
	<br /> <small><?php echo $field->helpText;?></small>

<?php }?>
</p>