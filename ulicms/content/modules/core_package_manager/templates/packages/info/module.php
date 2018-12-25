<?php $model = ViewBag::get("model");?>
<h3><?php esc($model->name);?></h3>
<?php if($model->version){?>

<p>
	<strong>Version:</strong> <?php esc($model->version);?>
</p>
<?php }?>
<?php if($model->source){?>
<p>
	<strong><?php translate("source");?>: </strong> <?php secure_translate($model->source);?></p>

<?php }?>
<?php if(count($model->customPermissions)){?>
<p>
	<strong><?php translate("custom_permissions");?>: </strong><br />
<ul>
	<?php foreach($model->customPermissions as $permission){?>
	<li><?php esc($permission);?></li>
	<?php }?>
	</ul>
</p>
<?php }?>


