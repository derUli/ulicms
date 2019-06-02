<?php

use zz\Html\HTMLMinify;

class HomeController extends Controller {

    public function getModel() {
        $model = new HomeViewModel();
        $query = Database::query("SELECT count(id) as amount FROM `{prefix}content`", true);
        $result = Database::fetchObject($query);
        $model->contentCount = $result->amount;

        $topPages = Database::query("SELECT language, slug, title, `views` FROM " . tbname("content") . " WHERE deleted_at is null and type <> 'node' ORDER BY `views` DESC LIMIT 5", false);
        while ($row = Database::fetchObject($topPages)) {
            $model->topPages[] = $row;
        }

        $lastModfiedPages = Database::query("SELECT language, slug, title, lastmodified, case when lastchangeby is not null and lastchangeby > 0 then lastchangeby else author_id end as lastchangeby FROM " . tbname("content") . "  WHERE deleted_at is null and type <> 'node'  ORDER BY lastmodified DESC LIMIT 5", false);
        while ($row = Database::fetchObject($lastModfiedPages)) {
            $model->lastModfiedPages[] = $row;
        }
        $adminsQuery = Database::query("SELECT id, username FROM " . tbname("users"));
        while ($row = Database::fetchObject($adminsQuery)) {
            $admins[$row->id] = $row->username;
        }
        $model->admins = $admins;

        $pkg = new PackageManager();
        if (in_array("guestbook", getAllModules())) {
            $guestbookEntries = Database::query("SELECT count(id) as amount FROM " . tbname("guestbook_entries"), false);
            $result = Database::fetchObject($guestbookEntries);
            $model->guestbookEntryCount = $result->amount;
        }
        return $model;
    }

    public function newsfeed() {

        $html = Template::executeModuleTemplate("core_home", "news.php");
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($html, $options);
        $html = $HTMLMinify->process();
        $html = StringHelper::removeEmptyLinesFromString($html);
        HtmlResult($html);
    }

    public function statistics() {
        $html = Template::executeModuleTemplate("core_home", "statistics.php");
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($html, $options);
        $html = $HTMLMinify->process();
        HtmlResult($html);
    }

    public function topPages() {
        $html = Template::executeModuleTemplate("core_home", "top_pages.php");
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($html, $options);
        $html = $HTMLMinify->process();
        HtmlResult($html);
    }

    public function lastUpdatedPages() {
        $html = Template::executeModuleTemplate("core_home", "last_updated_pages.php");
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($html, $options);
        $html = $HTMLMinify->process();
        HtmlResult($html);
    }

    public function onlineUsers() {
        $html = Template::executeModuleTemplate("core_home", "online_users.php");
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($html, $options);
        $html = $HTMLMinify->process();
        HtmlResult($html);
    }

}
