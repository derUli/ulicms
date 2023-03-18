<?php
use App\Helpers\DateTimeHelper;

$id = get_ID();
if ($id !== null) {
    $list = new List_Data($id);
    if ($list->content_id !== null) {
        $limit = intval($list->limit);
        $use_pagination = $list->use_pagination;

        $list->use_pagination = false;
        $entries = $list->filter();
        if (count($entries) > 0) {
            // Pagination
            $entries_count_total = count($entries);
            $start = 0;

            if ($limit > 0 and $use_pagination) {
                if (isset($_GET ["start"])) {
                    $start = intval($_GET ["start"]);
                }
                $entries = array_slice($entries, $start, $limit);
                $entries_count = count($entries);
            }

            $previous_start = $start - $limit;
            if ($previous_start < 0) {
                $previous_start = 0;
            }

            $next_start = $start + $limit;
            if ($next_start <= $entries_total_count) {
                $next_start = $start + $limit;
            }
            ?>
            <div class="fluid-container">
                <?php
                foreach ($entries as $entry) {
                    $article_image = getTemplateDirPath('impro17') . "images/nopic.jpg";
                    $meta = get_article_meta($entry->slug);

                    $article_date = $entry->article_date ?? $entry->created;
                    $excerpt = strip_tags($meta->excerpt, '<img><iframe><embed><object>');
                    $excerpt = trim($excerpt);
                    $excerpt = !empty($excerpt) ? $meta->excerpt : $entry->content;
                    ?>
                    <div class="article-list-row">
                        <p>
                            <strong><a
                                    href="<?php Template::escape(buildSEOUrl($entry->slug, $entry->link_url)); ?>"><?php Template::escape($entry->title); ?></a></strong>
                            <br /> <small><?php translate('date'); ?>: <?php echo DateTimeHelper::timestampToFormattedDateTime($article_date); ?></small>
                        </p>
                        <p><?php echo $excerpt; ?></p>
                        <p>
                            <a
                                href="<?php Template::escape(buildSEOUrl($entry->slug, $entry->link_url)); ?>"><?php translate('readmore'); ?></a>
                        </p>
                    </div>
                <?php }
                ?>
                <div class="bottom-list-border"></div>
                <?php if ($use_pagination) { ?>
                    <div class="page_older_newer">
                        <?php if ($start > 0 and $use_pagination) { ?>
                            <span class="blog_pagination_newer"><a
                                    href="<?php Template::escape(buildSEOUrl()); ?>?start=<?php echo $previous_start; ?>"><?php Template::escape('<<'); ?></a></span>
                            <?php } ?>
                            <?php if ($start + $limit < $entries_count_total and $use_pagination) { ?>
                            <span class="blog_pagination_older"><a
                                    href="<?php Template::escape(buildSEOUrl()); ?>?start=<?php echo $next_start; ?>"><?php Template::escape('>>'); ?></a></span>
                            <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }
}
