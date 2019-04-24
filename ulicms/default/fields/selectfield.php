<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
$options = Viewbag::get("field_options") ? Viewbag::get("field_options") : array();
if (is_null($value)) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field"
     data-field-name="<?php Template::escape($field->name); ?>">
    <p>
        <strong><?php translate($field->title); ?> <?php if ($field->required) echo "*"; ?></strong><br />
        <select name="<?php Template::escape(ViewBag::get("field_name")); ?>"
        <?php if ($field->required) echo "required"; ?>
                <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>>
                    <?php foreach ($options as $optionValue => $optionTitle) { ?>
                <option value="<?php Template::escape($optionValue); ?>"
                <?php
                if ($optionValue == $value) {
                    echo "selected";
                }
                ?>><?php
                            if ($field->translateOptions) {
                                secure_translate($optionTitle);
                            } else {
                                Template::escape($optionTitle);
                            }
                            ?></option>
            <?php } ?>
        </select>
        <?php if ($field->helpText) { ?>
            <br /> <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </p>
</div>