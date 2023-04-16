<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\StringHelper;
use zz\Html\HTMLMinify;

class HomeController extends \App\Controllers\Controller
{
    public function getModel(): HomeViewModel
    {
        $model = new HomeViewModel();
        $result = Database::query('SELECT count(id) as amount FROM `{prefix}content`', true);
        $dataset = Database::fetchObject($result);
        $model->contentCount = $dataset->amount;

        $topPages = Database::query('SELECT language, slug, title, `views` FROM ' . tbname('content') . " WHERE deleted_at is null and type <> 'node' ORDER BY `views` DESC LIMIT 5", false);
        while ($row = Database::fetchObject($topPages)) {
            $model->topPages[] = $row;
        }

        $lastModfiedPages = Database::query('SELECT language, slug, title, lastmodified, case when lastchangeby is not null and lastchangeby > 0 then lastchangeby else author_id end as lastchangeby FROM ' . tbname('content') . "  WHERE deleted_at is null and type <> 'node'  ORDER BY lastmodified DESC LIMIT 5", false);
        while ($row = Database::fetchObject($lastModfiedPages)) {
            $model->lastModfiedPages[] = $row;
        }

        $adminsQuery = Database::query('SELECT id, username FROM ' . tbname('users'));
        while ($row = Database::fetchObject($adminsQuery)) {
            $admins[$row->id] = $row->username;
        }

        $model->admins = $admins;

        return $model;
    }

    public function newsfeed(): void
    {
        $html = $this->_newsfeed();
        HtmlResult($html);
    }

    public function _newsfeed()
    {
        $html = Template::executeModuleTemplate('core_home', 'news.php');
        $options = [
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        ];
        $HTMLMinify = new HTMLMinify($html, $options);
        $html = $HTMLMinify->process();
        $html = StringHelper::removeEmptyLinesFromString($html);
        return $html;
    }

    public function statistics(): void
    {
        $html = $this->_statistics();
        HtmlResult($html);
    }

    public function _statistics(): string
    {
        $html = Template::executeModuleTemplate('core_home', 'statistics.php');
        $options = [
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        ];

        $HTMLMinify = new HTMLMinify($html, $options);
        return $HTMLMinify->process();
    }

    public function topPages(): void
    {
        $html = $this->_topPages();
        HtmlResult($html);
    }

    public function _topPages(): string
    {
        $html = Template::executeModuleTemplate('core_home', 'top_pages.php');
        $options = [
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        ];
        $HTMLMinify = new HTMLMinify($html, $options);
        return $HTMLMinify->process();
    }

    public function lastUpdatedPages(): void
    {
        $html = $this->_lastUpdatedPages();
        HtmlResult($html);
    }

    public function _lastUpdatedPages(): string
    {
        $html = Template::executeModuleTemplate('core_home', 'last_updated_pages.php');

        $options = [
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        ];
        $HTMLMinify = new HTMLMinify($html, $options);
        return $HTMLMinify->process();
    }

    public function onlineUsers(): void
    {
        $html = $this->_onlineUsers();
        HtmlResult($html);
    }

    public function _onlineUsers(): string
    {
        \App\Storages\ViewBag::set('users', User::getOnlineUsers());

        $html = Template::executeModuleTemplate('core_home', 'online_users.php');
        $options = [
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        ];

        $HTMLMinify = new HTMLMinify($html, $options);
        return $HTMLMinify->process();
    }
}
