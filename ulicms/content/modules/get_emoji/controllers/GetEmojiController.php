<?php

// this module just inserts a link to getemoji.com into the
// UliCMS backend menu bar
class GetEmojiController extends MainClass {

    public function adminMenuEntriesFilter($entries) {
        $useragent = get_useragent() ? get_useragent() : "";

        $oldWindowsVersions = [
            "5.0",
            "5.1",
            "5.2",
            "6.0",
            "6.1",
            "6.2",
            "6.3"
        ];

        $isOldOs = false;

        // detect old windows versions
        foreach ($oldWindowsVersions as $ntVersion) {
            if (str_contains("NT $ntVersion", $useragent)) {
                $isOldOs = true;
            }
        }

        if ($this->isOldAndroid("8.0.0", $useragent)) {
            $isOldOs = true;
        }

        // if the client is an outdated windows or android version then
        // link the classic get emoji page
        // else the regular one.
        $url = !$isOldOs ?
                "https://getemoji.com/" : "http://classic.getemoji.com/";

        $entries[count($entries) - 1] = new MenuEntry('<i class="far fa-grin"></i> Get Emoji', $url, "get_emoji", null, array(), true);

        $logoutUrl = ModuleHelper::buildMethodCallUrl(SessionManager::class, "logout");
        $entries[] = new MenuEntry('<i class="fa fa-sign-out-alt"></i> ' . get_translation("logout"), $logoutUrl, "logout");

        return $entries;
    }

    protected function isOldAndroid($version, $useragent): bool {
        if (strstr($useragent, 'Android')) {
            preg_match(
                    '/Android (\d+(?:\.\d+)+)[;)]/',
                    $useragent,
                    $matches
            );

            return version_compare($matches[1], $version, '<');
        }
        return false;
    }

}
