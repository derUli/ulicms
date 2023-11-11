<?php
use App\Helpers\ModuleHelper;
use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();
?>
<?php echo ModuleHelper::buildMethodCallForm('BotTrapController', 'saveSettings');?>
	<?php
if (Request::getVar('save') && $permissionChecker->hasPermission('bot_trap_settings')) {
    ?>
<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate('changes_was_saved');?>
		</div>
<?php
}
?>
<h3>
	robots.txt
</h3>
<p>
	<?php translate('add_this_to_robots_txt');?>
</p>
<pre style="word-wrap: break-word; white-space: pre-wrap;">User-agent: *
Disallow: /?trap_me=1</pre>
<h3>
	<?php translate('trapped_bots');?>
</h3>
<p>
	<textarea name="trapped_bots" cols="80" rows="5"
		<?php if(! $permissionChecker->hasPermission('bot_trap_settings')) echo 'disabled';?>><?php echo nl2br(_esc(Settings::get('trapped_bots')));?></textarea>
</p>
<h3>
	<?php translate('bot_trap_custom_message');?>
</h3>
<p>
	<textarea name="bot_trap_custom_message" cols="80" rows="5"
		<?php if(! $permissionChecker->hasPermission('bot_trap_settings')) echo 'disabled';?>><?php echo nl2br(_esc(Settings::get('bot_trap_custom_message')));?></textarea>
</p>

<?php if($permissionChecker->hasPermission('bot_trap_settings')) {?>
<p class="voffset3">
	<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php translate('save');?></button>
</p>
<?php }?>
<?php echo ModuleHelper::endForm();?>