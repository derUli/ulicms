<?php
use GDPR\PersonalData as PersonalData;
$acl = new ACL();
?>
<?php $search = Request::getVar("search", null, "str");?>
<form
	action="<?php echo Modulehelper::buildAdminURL("personal_data_export");?>"
	autocomplete="off">
<?php echo UliCMS\HTML\Input::Hidden("action", "module_settings");?>
<?php echo UliCMS\HTML\Input::Hidden("module", "personal_data_export");?>
<p>
		<strong><?php translate("name_or_email_address");?></strong> <br /> <input
			type="search" name="search" value="<?php esc($search);?>">
	</p>
	<button type="submit" class="btn btn-primary"><?php translate("search");?></button>
</form>
<?php if($search){?>
<?php
    $query = new PersonalData\Query();
    $results = $query->searchPerson($search);
    ?>
   <?php if(count($results) > 0){?>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("email");?></th>
			<th><?php translate("name");?></th>
			<th><?php translate("actions");?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach($results as $persons){?>
<?php foreach($persons as $person){?>
<tr>
			<td><?php esc($person->email);?></td>
			<td><?php esc($person->name);?></td>
			<td></td>
		</tr>
		<?php }?>
<?php }?>
</tbody>
</table>
<?php
    } else {
        ?>
<div class="voffset2 alert alert-warning vspacing2"><?php translate("no_results_found");?></div>
<?php
    }
    ?>


<?php }?>