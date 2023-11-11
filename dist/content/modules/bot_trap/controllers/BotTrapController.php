<?php

use App\Controllers\MainClass;
use App\Helpers\ModuleHelper;

/**
 * This module places a nofollow link outside of the viewport
 * If a bad bot follows the link it's IP address is getting blocked
 */
class BotTrapController extends MainClass {
    public const MODULE_NAME = 'bot_trap';

    /**
     * Render trap link
     *
     * @return string
     */
    public function beforeEditButton() {
        return Template::executeModuleTemplate(static::MODULE_NAME, 'traplink.php');
    }

    /**
     * Render admin settings page
     *
     * @return string
     */
    public function settings() {
        return Template::executeModuleTemplate(static::MODULE_NAME, 'settings.php');
    }

    /**
     * Get headline for admin settings page
     *
     * @return string
     */
    public function getSettingsHeadline() {
        return get_translation('bot_trap');
    }

    /**
     * Save settings on post
     * @return void
     */
    public function saveSettingsPost() {
        Settings::set('trapped_bots', trim(Request::getVar('trapped_bots')));
        Settings::set('bot_trap_custom_message', trim(Request::getVar('bot_trap_custom_message')));
        Response::redirect(ModuleHelper::buildAdminURL(static::MODULE_NAME, 'save=1'));
    }

    /**
     * Check if IP address is blocked
     *
     * @return void
     */
    public function beforeInit() {
        if (Settings::get('trapped_bots') === false) {
            Settings::register('trapped_bots', '');
        }
        $trappedBots = trim(Settings::get('trapped_bots') ?? '');
        $trappedBotsArray = Settings::mappingStringToArray($trappedBots);
        $ip = get_ip();
        $useragent = get_useragent();

        if (isset($trappedBotsArray[$ip])) {
            $blockedMessage = Settings::get('bot_trap_custom_message') ?: 'Sorry, you are blocked';
            TextResult($blockedMessage, 403);
        }

        if (Request::getVar('trap_me')) {
            $trappedBots .= "\n";
            $trappedBots .= get_ip() . '=>' . get_useragent();
            Settings::set('trapped_bots', trim($trappedBots));

            TextResult('Bot Trapped', 403);
        }
    }
}
