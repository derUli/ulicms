<?php

class BotTrapController extends Controller
{

    private $moduleName = "bot_trap";

    public function beforeEditButton()
    {
        return Template::executeModuleTemplate($this->moduleName, "traplink.php");
    }

    public function beforeInit()
    {
        if (Settings::get("trapped_bots") === false) {
            Settings::register("trapped_bots", "");
        }
        $trappedBots = trim(Settings::get("trapped_bots"));
        $trappedBotsArray = Settings::mappingStringToArray($trappedBots);
        $ip = get_ip();
        $useragent = get_useragent();
        
        if (isset($trappedBotsArray[$ip])) {
            $blockedMessage = Settings::get("you_are_blocked") ? Settings::get("you_are_blocked") : "Sorry, you are blocked";
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