<?php
use UliCMS\HTML\Script;
use UliCMS\Security\PermissionChecker;

class MessageServiceController extends MainClass
{

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
}