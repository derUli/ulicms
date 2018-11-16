<?php
use UliCMS\Data\Content\Comment;

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
        $checkbox = new PrivacyCheckbox(getCurrentLanguage(true));
        if ($checkbox->isEnabled()) {
            $checkbox->check();
        }
        
        $content_id = Request::getVar("content_id", 0, "int");
        $content = null;
        try {
            $content = ContentFactory::getByID($content_id);
        } catch (Exception $e) {
            ExceptionResult(get_translation("no_such_content"));
        }
        
        if (! $content->areCommentsEnabled()) {
            ExceptionResult(get_translation("comments_are_disabled"));
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
        
        $status = Settings::get("comments_must_be_approved") ? CommentStatus::PENDING : CommentStatus::PUBLISHED;
        
        // show error if not all required fields are filled
        if (! $comment->getAuthorName() or ! $comment->getText()) {
            return ExceptionResult(get_translation("fill_all_fields"));
        }
        
        if ($comment->isSpam()) {
            $status = CommentStatus::SPAM;
        }
        
        $comment->setStatus($status);
        
        if (! Settings::get("log_ip")) {
            $comment->setIp(null);
        }
        
        $comment->save();
        
        $cacheAdapater = CacheUtil::getAdapter(true);
        $cacheAdapater->clear();
        
        Response::redirect(ModuleHelper::getFullPageURLByID($content_id, "comment_published=" . $status));
    }
}