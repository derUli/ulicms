<?php

class TelegramController extends MainClass {

    const MODULE_NAME = "telegram";

    public function registerCronjobs() {

        BetterCron::minutes("telegram/post_blog_articles", 5, function() {
            @set_time_limit(0);

            $connection = $this->connect();

            if (!$connection) {
                return;
            }
            $this->postBlogArticles($connection);
        });
    }

    public function savePost() {
        Settings::set("telegram/bot_token", Request::getVar("bot_token"));
        Settings::set("telegram/channel_name", Request::getVar("channel_name"));

        $connection = $this->connect();
        if ($connection) {
            Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME, "save=1"));
        } else {
            Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME, "error=telegram_connect_failed"));
        }
    }

    public function settings() {
        return Template::executeModuleTemplate(self::MODULE_NAME, "settings.php");
    }

    public function getSettingsHeadline() {
        return '<i class="fab fa-telegram" '
                . 'style="font-size: 30px; color:#0088cc"></i> | Telegram';
    }

    protected function connect() {
        $bot_token = Settings::get("telegram/bot_token");
        $channel_name = Settings::get("telegram/channel_name");
        if (!is_present($bot_token) or ! is_present($channel_name)) {
            return null;
        }

        return new \naffiq\telegram\channel\Manager($bot_token, $channel_name);
    }

    protected function postBlogArticles($connection) {
        foreach (getAllLanguages() as $language) {
            $page = ModuleHelper::getFirstPageWithModule("blog", $language);
            if (!$page) {
                continue;
            }

            $pageModel = ContentFactory::getById($page->id);
            $query = Database::selectAll("blog",
                            ["id", "title", "seo_shortname", "meta_description"], "entry_enabled = 1 and posted2telegram = 0 and UNIX_TIMESTAMP() >= datum and language='" . Database::escapeValue($language) . "'", [], true, "datum asc limit 1");
            while ($article = Database::fetchObject($query)) {
                $viewModel = new stdClass();
                $viewModel->title = $article->title;
                $viewModel->description = $article->meta_description;
                $viewModel->url = $pageModel->getUrl("single={$article->seo_shortname}");

                ViewBag::set("message", $viewModel);
                $messageText = Template::executeModuleTemplate(self::MODULE_NAME, "message.php");
                $result = $connection->postMessage($messageText);
                if ($result->ok) {
                    Database::pQuery("update {prefix}blog set posted2telegram = 1 where id = ?", array($article->id), true);
                }
            }
        }
    }

}
