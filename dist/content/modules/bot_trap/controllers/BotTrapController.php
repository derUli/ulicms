<?php
use App\Controllers\MainClass;
use App\Helpers\ModuleHelper;

class BotTrapController extends MainClass
{

    private $moduleName = "bot_trap";

    public function beforeEditButton()
    {
        return Template::executeModuleTemplate($this->moduleName, "traplink.php");
    }

    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "settings.php");
    }

    public function getSettingsHeadline()
    {
        return get_translation("bot_trap");
    }

    public function saveSettingsPost()
    {
        Settings::set("trapped_bots", trim(Request::getVar("trapped_bots")));
        Settings::set("bot_trap_custom_message", trim(Request::getVar("bot_trap_custom_message")));
        Response::redirect(ModuleHelper::buildAdminURL($this->moduleName, "save=1"));
    }

    public function beforeInit()
    {
        if (Settings::get("trapped_bots") === false) {
            Settings::register("trapped_bots", "");
        }
        $trappedBots = trim(Settings::get("trapped_bots") ?? '');
        $trappedBotsArray = Settings::mappingStringToArray($trappedBots);
        $ip = get_ip();
        $useragent = get_useragent();
        
        if (isset($trappedBotsArray[$ip])) {
            $blockedMessage = Settings::get("bot_trap_custom_message") ? Settings::get("bot_trap_custom_message") : "Sorry, you are blocked";
            TextResult($blockedMessage, 403);
        }
        
        if (Request::getVar("trap_me")) {
            $trappedBots .= "\n";
            $trappedBots .= get_ip() . "=>" . get_useragent();
            Settings::set("trapped_bots", trim($trappedBots));
            
            TextResult("Bot Trapped", 403);
        }
    }

    public function uninstall()
    {}
}