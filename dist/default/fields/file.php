<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
if ($value === null) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field field"
     data-field-name="<?php Template::escape($field->name); ?>">
    <img src="gfx/preview.png" class="img-thumbnail"
         id="thumbnail-<?php Template::escape(ViewBag::get("field_name")); ?>"
         style="display: none">
    <div class="field">
        <strong class="field-label"><?php translate($field->title); ?> <?php
            if ($field->required) {
                echo "*";
            }
?></strong>
        <input type="text"
               name="<?php Template::escape(ViewBag::get("field_name")); ?>"
               id="field-<?php Template::escape(ViewBag::get("field_name")); ?>"
               value="<?php Template::escape($value); ?>" class="fm"
               data-fm-type="<?php ViewBag::get("fm_type") ? esc(ViewBag::get("fm_type")) : "files" ?>"
               <?php
   if ($field->required) {
       echo "required";
   }
?>
               <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>
               readonly>
    </div>
    <div class="field">
        <a href="#" class="btn btn-default clear-field"
           data-for="#field-<?php Template::escape(ViewBag::get("field_name")); ?>"
           class="btn btn-default"><i class="fa fa-eraser"></i> <?php translate("clear") ?></a>
        <?php if ($field->helpText) { ?>
            <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </div>
</div>