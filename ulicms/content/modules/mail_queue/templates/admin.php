<?php
use MailQueue\MailQueue as MailQueue;
$queue = MailQueue::getInstance();

$mails = $queue->getAllMails();
?>
<?php echo ModuleHelper::buildMethodCallForm("MailQueueAdminController", "doAction");?>
<table class="tablesorter">
	<thead>
		<tr>
		<td style="width:30px;"><input type="checkbox" class="select-all" id="select-all-mails" data-target=".mail-ids"></td>
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
<td><input type="checkbox" name="ids[]" class="mail-ids checkbox" data-checkbox-group="mail"
	value="<?php esc($mail->getId());?>" data-select-all-checkbox="#select-all-mails"></td>
			<td><?php esc($mail->getId());?></td>
			<td><?php esc($mail->getRecipient());?></td>
			<td><?php echo nl2br(_esc($mail->getHeaders()));?></td>
			<td><?php esc($mail->getSubject());?></td>
			<td><?php echo nl2br(_esc($mail->getMessage()));?></td>
			<td><?php echo _esc(date('Y-m-d H:i:s', $mail->getCreated()));?></td>
		</tr>
 <?php
}
?>
<!-- <tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
<tr><td><input type="checkbox" name="ids[]" class="mail-ids" value="<?php echo uniqid();?>"></td></tr>
-->
</tbody>
</table>
<?php echo ModuleHelper::endForm();?>
