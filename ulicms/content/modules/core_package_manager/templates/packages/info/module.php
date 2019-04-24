<?php $model = ViewBag::get("model"); ?>
<h3><?php esc($model->name); ?></h3>
<?php if ($model->version) { ?>

    <p>
        <strong>Version:</strong> <?php esc($model->version); ?>
    </p>
<?php } ?>
<?php if ($model->manufacturerName) { ?>
    <p>
        <strong>
            <?php translate("manufacturer"); ?>:
        </strong>

        <?php if ($model->manufacturerUrl) { ?>
            <a href="<?php esc($model->manufacturerUrl); ?>" target="_blank"><?php esc($model->manufacturerName); ?></a>
        <?php } else { ?>
            <?php esc($model->manufacturerName); ?>
        <?php } ?>
    </p>
<?php } ?>
<?php if ($model->source) { ?>
    <p>
        <strong><?php translate("source"); ?>: </strong> <?php secure_translate($model->source); ?></p>

<?php } ?>
<?php if (count($model->customPermissions)) { ?>
    <p>
        <strong><?php translate("custom_permissions"); ?>:</strong><br />
    <ul>
        <?php foreach ($model->customPermissions as $permission) { ?>
            <li><?php esc($permission); ?>
                <?php if ($model->adminPermission == $permission) { ?>
                    <span class="text-primary">*</span>
                <?php } ?></li>
            <?php } ?>
    </ul>
    </p>
<?php } ?>


