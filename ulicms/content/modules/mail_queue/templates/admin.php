<?php
use MailQueue\MailQueue as MailQueue;
$queue = MailQueue::getInstance();

$acl = new ACL();

$mails = $queue->getAllMails();
?>
<?php echo ModuleHelper::buildMethodCallForm("MailQueueAdminController", "doAction");?>
<table class="tablesorter">
	<thead>
		<tr>
		
<?php if($acl->hasPermission("mail_queue_manage")){?>
<td style="width:30px;"><input type="checkbox" class="select-all" id="select-all-mails" data-target=".mail-ids"></td>
<?php }?>
			<th><?php translate("id");?></th>
			<th><?php translate("recipient");?></th>
			<th><?php translate("headers");?></th>
			<th><?php translate("subject");?></th>
			<th><?php translate("message");?></th>
			<th><?php translate("created");?></th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($mails as $mail) {
    ?>
<tr>

<?php if($acl->hasPermission("mail_queue_manage")){?>
<td><input type="checkbox" name="ids[]" class="mail-ids checkbox" data-checkbox-group="mail"
	value="<?php esc($mail->getId());?>" data-select-all-checkbox="#select-all-mails"></td> 
	<?php
}
?>
			<td><?php esc($mail->getId());?></td>
			<td><?php esc($mail->getRecipient());?></td>
			<td><?php echo nl2br(_esc($mail->getHeaders()));?></td>
			<td><?php esc($mail->getSubject());?></td>
			<td><?php echo nl2br(_esc($mail->getMessage()));?></td>
			<td><?php echo _esc(date('Y-m-d H:i:s', $mail->getCreated()));?></td>
		</tr>

<!-- <tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
-->
<?php } ?>
</tbody>
</table>
<?php if($acl->hasPermission("mail_queue_manage")){?>
<div class="row">
	<div class="col-xs-8">
		<select name="action">
			<option selected value=""><?php translate("please_select");?></option>
			<option value="delete"><?php translate("delete");?></option>
		</select>
	</div>
	<div class="col-xs-4 text-right">
		<button type="submit" class="btn btn-default"><?php translate("do_action");?></button>
	</div>
</div>
<?php } ?>
<?php echo ModuleHelper::endForm();?>
