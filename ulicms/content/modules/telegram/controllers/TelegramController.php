<?php

use UliCMS\Models\Content\TypeMapper;

class TelegramController extends MainClass {

    const MODULE_NAME = "telegram";

    public function registerCronjobs() {

        if (isModuleInstalled("blog")
                and Settings::get("telegram/publish_blog_posts")) {
            BetterCron::minutes("telegram/post_blog_articles", 5, function() {
                @set_time_limit(0);

                $connection = $this->connect();

                if (!$connection) {
                    return;
                }

                $this->postBlogArticles($connection);
            });
        }

        if (Settings::get("telegram/publish_articles_and_images")) {
            BetterCron::minutes("telegram/post_content", 5, function() {
                @set_time_limit(0);

                $connection = $this->connect();

                if (!$connection) {
                    return;
                }
                $this->postContent($connection);
            });
        }
    }

    public function savePost() {
        Settings::set("telegram/bot_token", Request::getVar("bot_token"));
        Settings::set("telegram/channel_name", Request::getVar("channel_name"));

        if (Request::getVar("publish_articles_and_images")) {
            Settings::set("telegram/publish_articles_and_images", "1");
        } else {
            Settings::delete("telegram/publish_articles_and_images");
        }
        if (isModuleInstalled("blog")) {
            if (Request::getVar("publish_blog_posts")) {
                Settings::set("telegram/publish_blog_posts", "1");
            } else {
                Settings::delete("telegram/publish_blog_posts");
            }
        }
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
                            ["id", "title", "seo_shortname", "meta_description"], "entry_enabled = 1 and posted2telegram = 0 and UNIX_TIMESTAMP() >= datum and language='" . Database::escapeValue($language) . "'", [], true, "RAND() limit 1");
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

    protected function postContent($connection) {
        $types = TypeMapper::getMappings();
        $typesSubQuery = "('--',";

        foreach ($types as $key => $class) {
            if (new $class() instanceof Image_Page or
                    new $class() instanceof Article
            ) {
                $typesSubQuery .= "'{$key}',";
            }
        }
        $typesSubQuery .= "'--')";

        foreach (getAllUsedLanguages() as $language) {
            $query = Database::selectAll("content",
                            ["id", "title", "slug", "meta_description", "article_image", "image_url"], "active = 1 and posted2telegram = 0 and language='" . Database::escapeValue($language) . "' and type in $typesSubQuery", [], true, "RAND() limit 1");
            while ($dataset = Database::fetchObject($query)) {
                $page = ContentFactory::getById($dataset->id);

                $viewModel = new stdClass();
                $viewModel->title = $dataset->title;
                $viewModel->description = $dataset->meta_description;
                $viewModel->url = $page->getUrl();

                ViewBag::set("message", $viewModel);
                $messageText = Template::executeModuleTemplate(self::MODULE_NAME, "message.php");

                $image_url = $this->getImageUrl($page);
                if ($this->postMessage($connection, $image_url, $messageText)) {
                    Database::pQuery("update {prefix}content set posted2telegram = 1 where id = ?", array($page->id), true);
                }
            }
        }
    }

    protected function getImageUrl($page) {
        $image_url = null;
        if ($page instanceof Image_Page and is_present($page->image_url)) {
            $image_url = Path::resolve("ULICMS_ROOT" .
                            urldecode($page->image_url));
        } else if ($page instanceof Article and is_present($page->article_image)) {
            $image_url = Path::resolve("ULICMS_ROOT" .
                            urldecode($page->article_image));
        }
        return $image_url;
    }

    protected function postMessage($connection, $image_url, $messageText) {
        $result = null;
        if ($image_url) {
            $result = $connection->postPhoto($image_url, $messageText);
        } else {
            $result = $connection->postMessage($messageText);
        }
        return $result and $result->ok;
    }

}
