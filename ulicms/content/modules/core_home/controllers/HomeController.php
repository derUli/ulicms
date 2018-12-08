<?php

class HomeController extends Controller
{

    public function getModel()
    {
        $model = new HomeViewModel();
        $query = Database::query("SELECT count(id) as amount FROM `{prefix}content`", true);
        $result = Database::fetchObject($query);
        $model->contentCount = $result->amount;
        
        $topPages = Database::query("SELECT language, systemname, title, `views` FROM " . tbname("content") . " WHERE deleted_at is null and type <> 'node' ORDER BY `views` DESC LIMIT 5", false);
        while ($row = Database::fetchObject($topPages)) {
            $model->topPages[] = $row;
        }
        
        $lastModfiedPages = Database::query("SELECT language, systemname, title, lastmodified, case when lastchangeby is not null and lastchangeby > 0 then lastchangeby else autor end as lastchangeby FROM " . tbname("content") . "  WHERE deleted_at is null and type <> 'node'  ORDER BY lastmodified DESC LIMIT 5", false);
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

    public function newsfeed()
    {
        HtmlResult(Template::executeModuleTemplate("core_home", "news.php"));
    }
}