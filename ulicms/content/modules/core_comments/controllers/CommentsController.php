<?php
use UliCMS\Data\Content\Comment;
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\HTML as HTML;

class CommentsController extends MainClass
{

    public function beforeHtml()
    {
        Vars::set("comments_enabled", false);
        
        if (is_200()) {
            $page = ContentFactory::getCurrentPage();
            
            // currently it's not supported to cache pages where comments are enabled
            // This is a limitation of UliCMS caching system and will get fixed in a future
            // release of UliCMS
            if ($page->areCommentsEnabled()) {
                Flags::setNoCache(true);
                Vars::set("comments_enabled", true);
            }
        }
        Vars::set("content_id", $page->id);
    }

    // This method handles posted comments
    public function postComment()
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
        } catch (FileNotFoundException $e) {
            ExceptionResult(get_translation("no_such_content"));
        }
        
        $comment = new Comment();
        $comment->setContentId($content_id);
        $comment->setDate(time());
        $comment->setAuthorName(Request::getVar("author_name"));
        $comment->setAuthorEmail(Request::getVar("author_email"));
        $comment->setAuthorUrl(Request::getVar("author_url"));
        $comment->setText(Request::getVar("text"));
        $comment->setIp(Request::getIp());
        $comment->setUserAgent(get_useragent());
        
        // if comments must be approved, set the comment status to pending, else to published
        $status = Settings::get("comments_must_be_approved") ? CommentStatus::PENDING : CommentStatus::PUBLISHED;
        
        // show error if not all required fields are filled
        if (! $comment->getAuthorName() or ! $comment->getText()) {
            ExceptionResult(get_translation("fill_all_fields"));
        }
        
        if ($comment->isSpam()) {
            $status = CommentStatus::SPAM;
        }
        
        $comment->setStatus($status);
        
        // if ip login is disabled (which is a must in countries of the european union)
        // unset the ip field
        if (! Settings::get("log_ip")) {
            $comment->setIp(null);
        }
        
        $comment->save();
        
        // Clear cache when posting a comment
        // FIXME: clear only the cache entries for the commented content
        $cacheAdapater = CacheUtil::getAdapter(true);
        $cacheAdapater->clear();
        
        // Redirect to the page and show a message to the user
        Response::redirect(ModuleHelper::getFullPageURLByID($content_id, "comment_published=" . $status));
    }

    public function getCommentText()
    {
        $id = Request::getVar("id");
        try {
            $comment = new Comment($id);
            HtmlResult(StringHelper::makeLinksClickable(HTML\text(trim($comment->getText()))));
        } catch (FileNotFoundException $e) {
            HTMLResult(get_translation("not_found"), 404);
        }
    }

    public function getDefaultStatus()
    {
        $defaultStatus = Settings::get("comments_must_be_approved") ? CommentStatus::PENDING : CommentStatus::PUBLISHED;
        return $defaultStatus;
    }

    public function getResults($status = null, $content_id = null, $limit = 0)
    {
        $results = array();
        if ($status) {
            $results = Comment::getAllByStatus($status, $content_id);
        } else if ($content_id) {
            $results = Comment::getAllByContentId($content_id);
        } else {
            $status = $this->getDefaultStatus();
            $results = Comment::getAllByStatus($status);
        }
        if ($limit > 0) {
            $results = ArrayHelper::take($limit, $results);
        }
        return $results;
    }

    public function filterComments()
    {
        $status = Request::getVar("status", null, "str");
        $content_id = Request::getVar("content_id", null, "int");
        $limit = Request::getVar("limit", 0, "int");
        
        $results = $this->getResults($status, $content_id, $limit);
        ActionResult("comments_manage", $results);
    }
}
