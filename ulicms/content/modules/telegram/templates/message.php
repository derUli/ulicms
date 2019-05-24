<?php

$model = ViewBag::get("message");
?>
<?php echo $model->title; ?>

<?php if (is_present($model->description)) { ?>
    <?php echo $model->description; ?>

<?php } ?>
<?php echo $model->url; ?>