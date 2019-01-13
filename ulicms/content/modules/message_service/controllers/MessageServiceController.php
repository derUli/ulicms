<?php
use UliCMS\HTML\Script;
use UliCMS\Security\PermissionChecker;
use UliCMS\HTML\ListItem;
use UliCMS\Exceptions\NotImplementedException;

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
        $userManager = new UserManager();
        
        $userList = array();
        $users = $userManager->getLockedUsers(false, "username");
        foreach ($users as $user) {
            $permissionChecker = new PermissionChecker($user->getId());
            if ($permissionChecker->hasPermission("receive_messages")) {
                $userList[] = new ListItem($user->getId(), $user->getUsername());
            }
        }
        
        ViewBag::set("users", $userList);
        return Template::executeModuleTemplate(self::MODULE_NAME, "form.php");
    }

    public function sendMessage()
    {
        $receivers = Request::getVar("receivers", array());
        $messageText = Request::getVar("message", "", "str");
        
        if (! is_array($receivers) || empty($messageText)) {
            ExceptionResult(get_translation("fill_all_fields"), HttpStatusCode::BAD_REQUEST);
        }
        foreach ($receivers as $receiver) {
            $message = new Message();
            $message->setSenderId(get_user_id());
            $message->setReceiverId($receiver);
            $message->setMessage($messageText);
            $message->send();
        }
        Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME, "sent=1"));
    }

    public function getSettingsHeadline()
    {
        return get_translation("write_a_message");
    }

    public function getSettingsLinkText()
    {
        return get_translation("write_a_message");
    }

    public function uninstall()
    {
        $migrator = new DBMigrator("module/message_service", ModuleHelper::buildModuleRessourcePath("message_service", "sql/down"));
        $migrator->rollback();
    }
}