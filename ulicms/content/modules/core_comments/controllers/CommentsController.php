<?php
use UliCMS\Data\Content\Comment;

class CommentsController extends controller
{

    public function postComment()
    {
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
        
        // Todo: Validate input
        // Required fields: author_name and text
        // author_url must be a valid URL is_url()
        
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
        
        Response::redirect(ModuleHelper::getFullPageURLByID($content_id), "comment_published=" . $status);
    }
}