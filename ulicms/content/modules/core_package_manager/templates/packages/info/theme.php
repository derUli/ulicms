<?php $model = ViewBag::get("model");?>
<h3><?php esc($model->name);?></h3>
<?php if($model->version){?>

<p>
	<strong>Version:</strong> <?php esc($model->version);?>
</p>
<?php }?>
<?php if($model->manufacturerName){?>
<p>
	<strong>
<?php translate("manufacturer");?>:
</strong>

<?php if($model->manufacturerUrl){?>
<a href="<?php esc($model->manufacturerUrl);?>" target="_blank"><?php esc($model->manufacturerName);?></a>
<?php } else {?>
<?php esc($model->manufacturerName);?>
<?php }?> 
</p>
<?php }?>
<?php if($model->source){?>
<p>
	<strong><?php translate("source");?>: </strong> <?php secure_translate($model->source);?></p>
<?php }?>

<?php if(count($model->disableFunctions) > 0){?>
<p>
	<strong><?php translate("disabled_functions");?></strong><br />
<ul>
<?php foreach($model->disableFunctions as $function){?>
<li><?php esc($function);?></li>
<?php }?>
</ul>
<?php }?>


