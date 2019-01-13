<?php
use UliCMS\HTML\Script;
use UliCMS\Security\PermissionChecker;

class MessageServiceController extends MainClass
{

    const MODULE_NAME = 'message_service';

    public function backendFooter()
    {
        $permissionChecker = new PermissionChecker(get_user_id());
        if (! $permissionChecker->hasPermission("receive_messages")) {
            return;
        }
        $messages = Message::getAllWithReceiver(get_user_id());
        $texts = array();
        foreach ($messages as $message) {
            $texts[] = $message->getFormattedMessage();
            $message->delete();
        }
        
        $js = '$(function(){';
        $js .= 'var messages = ' . json_encode($texts) . ';';
        $js .= 'messages.forEach(function(element){alert(element);});';
        $js .= '});';
        echo Script::FromString($js);
    }

    public function adminMenuEntriesFilter($menuEntries)
    {
        $entry = new MenuEntry('<i class="fas fa-envelope"></i> ' . get_translation("messages"), ModuleHelper::buildAdminURL(self::MODULE_NAME), "messages", array(
            "send_messages"
        ));
        
        $menuEntries = ArrayHelper::insertAfter($menuEntries, 2, $entry);
        return $menuEntries;
    }

    public function settings()
    {
        return Template::executeModuleTemplate(self::MODULE_NAME, "form.php");
    }

    public function getSettingsHeadline()
    {
        return get_translation("messages");
    }

    public function getSettingsLinkText()
    {
        return get_translation("messages");
    }
}