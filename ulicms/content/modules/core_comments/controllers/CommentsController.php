<?php

declare(strict_types=1);

use UliCMS\Models\Content\Comment;
use UliCMS\Exceptions\DatasetNotFoundException;
use UliCMS\HTML as HTML;
use UliCMS\Exceptions\NotImplementedException;
use UliCMS\Constants\CommentStatus;
use UliCMS\Utils\CacheUtil;
use zz\Html\HTMLMinify;

class CommentsController extends MainClass
{
    public function beforeHtml(): void
    {
        Vars::set("comments_enabled", false);

        if (is_200()) {
            $page = ContentFactory::getCurrentPage();

            // currently it's not supported to cache pages
            // where comments are enabled
            // This is a limitation of UliCMS caching system and will get fixed
            // in a future release of UliCMS
            if ($page->areCommentsEnabled()) {
                Flags::setNoCache(true);
                Vars::set("comments_enabled", true);
            }
            Vars::set("content_id", $page->id);
        }
    }

    // This method handles posted comments
    public function postComment(): void
    {

        // check if DSGVO checkbox is checked
        $checkbox = new PrivacyCheckbox(getCurrentLanguage(true));
        if ($checkbox->isEnabled()) {
            $checkbox->check();
        }

        $content_id = Request::getVar("content_id", 0, "int");
        $content = null;
        try {
            $content = ContentFactory::getByID($content_id);
        } catch (DatasetNotFoundException $e) {
            ExceptionResult(get_translation("no_such_content"));
        }

        // create a comment dataset and fill properties
        $comment = new Comment();
        $comment->setContentId($content_id);
        $comment->setDate(time());
        $comment->setAuthorName(Request::getVar("author_name"));
        $comment->setAuthorEmail(Request::getVar("author_email"));
        $comment->setAuthorUrl(Request::getVar("author_url"));
        $comment->setText(Request::getVar("text"));
        $comment->setIp(Request::getIp());
        $comment->setUserAgent(get_useragent());

        // if comments must be approved, set the comment status to pending,
        // else to published
        $status = Settings::get("comments_must_be_approved") ?
                CommentStatus::PENDING : CommentStatus::PUBLISHED;

        // show error if not all required fields are filled
        if (!$comment->getAuthorName() || !$comment->getText()) {
            ExceptionResult(get_translation("fill_all_fields"));
        }

        // If the message looks like a spam comment lag it as spam
        if ($comment->isSpam()) {
            $status = CommentStatus::SPAM;
        }

        $comment->setStatus($status);

        // if ip login is disabled (which is a legal must in countries
        // of the european union)
        // unset the ip field
        if (!Settings::get("log_ip")) {
            $comment->setIp(null);
        }

        $comment->save();

        // Clear cache when posting a comment
        // FIXME: clear only the cache entries for the commented content
        CacheUtil::clearPageCache();

        // Redirect to the page and show a message to the user
        Response::redirect(
            ModuleHelper::getFullPageURLByID(
                $content_id,
                "comment_published=" . $status
            )
        );
    }

    public function getCommentText(): void
    {
        $id = Request::getVar("id", 0, "int");
        $text = $this->_getCommentText($id);
        if ($text) {
            HtmlResult($text, HttpStatusCode::OK, HTMLMinify::OPTIMIZATION_ADVANCED);
        }
        HTMLResult(get_translation("not_found"), 404);
    }

    public function _getCommentText(int $id): ?string
    {
        try {
            $comment = new Comment($id);
            $comment->setRead(true);
            $comment->save();
            return StringHelper::makeLinksClickable(
                HTML\text(trim($comment->getText()))
            );
        } catch (DatasetNotFoundException $e) {
            return null;
        }
    }

    // this returns the default status for new comments
    public function _getDefaultStatus(): string
    {
        $defaultStatus = Settings::get("comments_must_be_approved") ?
                CommentStatus::PENDING : CommentStatus::PUBLISHED;
        return $defaultStatus;
    }

    public function _getResults(
        ?string $status = null,
        ?int $content_id = null,
        ?int $limit = 0
    ): array {
        $results = [];
        if ($status) {
            $results = Comment::getAllByStatus($status, $content_id);
        } elseif ($content_id) {
            $results = Comment::getAllByContentId($content_id);
        } else {
            $status = $this->_getDefaultStatus();
            $results = Comment::getAllByStatus($status);
        }
        return $results;
    }

    // filter and show the comments to the comment moderation
    public function filterComments(): void
    {
        // get arguments from the URL
        $status = Request::getVar("status", null, "str");
        $content_id = Request::getVar("content_id", null, "int");
        $limit = Request::getVar("limit", $this->_getDefaultLimit(), "int");

        $results = $this->_filterComments($status, $content_id, $limit);
        // output the comment backend page to the user
        ActionResult("comments_manage", $results);
    }

    public function _filterComments(
        ?string $status = null,
        ?int $content_id = null,
        ?int $limit = null
    ): array {
        // do the search query
        return $this->_getResults($status, $content_id, $limit);
    }

    // get the configured default limit or if is set the default value
    public function _getDefaultLimit(): int
    {
        $limit = 100;
        if (Settings::get("comments_default_limit")) {
            $limit = intval(Settings::get("comments_default_limit"));
        }
        return $limit;
    }

    public function doAction(): void
    {
        // post arguments
        $commentIds = Request::getVar("comments", []);
        $action = Request::getVar("action", null, "str");

        // if we have comments and an action
        if (is_array($commentIds) && !empty($action)) {
            // do the selected action for each comment
            $this->_doActions($commentIds, $action);
        }

        // referrer is from a hidden field on the form
        // Append jumpto=comments to the url to jump to the comment
        // table after redirect
        // It's inpossible to append an anchor to the url on a http redirect
        // a javascript in fx.js performs the jump to the anchor
        $referrer = Request::getVar("referrer");
        if (!str_contains($referrer, "jumpto=comments")) {
            if (!str_contains($referrer, "?")) {
                $referrer .= "?";
            } else {
                $referrer .= "&";
            }
            $referrer .= "jumpto=comments";
        }
        Request::redirect($referrer);
    }

    public function _doActions(array $commentIds, string $action): array
    {
        $processedComments = [];

        foreach ($commentIds as $id) {
            $comment = new Comment($id);
            $processedComments[] = $this->_doAction($comment, $action);
        }
        return $processedComments;
    }

    public function _doAction(Comment $comment, string $action): Comment
    {
        switch ($action) {
            case "mark_as_spam":
                $comment->setStatus(CommentStatus::SPAM);
                $comment->setRead(true);
                break;
            case "publish":
                $comment->setStatus(CommentStatus::PUBLISHED);
                $comment->setRead(true);
                break;
            case "unpublish":
                $comment->setStatus(CommentStatus::PENDING);
                break;
            case "mark_as_read":
                $comment->setRead(true);
                break;
            case "mark_as_unread":
                $comment->setRead(false);
                break;
            case "delete":
                $comment->delete();
                break;
            default:
                throw new NotImplementedException(
                    "comment action not implemented"
                );
        }
        // if action is not delete save it
        if ($action != "delete") {
            $comment->save();
        }

        CacheUtil::clearPageCache();

        return $comment;
    }
}
