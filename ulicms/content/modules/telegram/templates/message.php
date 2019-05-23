<?php

$model = ViewBag::get("message");
?>
<?php esc($model->title); ?>
<?php if (is_present($model->description)) { ?>


    <?php esc($model->description); ?>

<?php } ?>
<?php esc($model->url); ?>