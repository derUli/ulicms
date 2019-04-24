<?php
$id = get_ID();
if ($id !== null) {
    $list = new List_Data($id);
    if ($list->content_id !== null) {
        $entries = $list->filter();
        if (count($entries) > 0) {
            // Pagination
            $entries_count_total = count($entries);
            $use_pagination = $list->use_pagination;
            $start = 0;
            $limit = intval($list->limit);
            if ($limit > 0 and $use_pagination) {
                if (isset($_GET["start"])) {
                    $start = intval($_GET["start"]);
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
            <ol class="ulicms-content-list" start="<?php echo $start + 1; ?>">
                <?php
                foreach ($entries as $entry) {
                    ?>
                    <li><a
                            href="<?php Template::escape(buildSEOUrl($entry->systemname)); ?>"><?php Template::escape($entry->title) ?></a></li>
                    <?php } ?>
            </ol>
            <?php if ($use_pagination) { ?>
                <div class="page_older_newer">
                    <?php if ($start > 0 and $use_pagination) { ?>
                        <span class="blog_pagination_newer"><a
                                href="<?php Template::escape(buildSEOUrl()); ?>?start=<?php echo $previous_start; ?>"><?php Template::escape("<<"); ?></a></span>
                        <?php } ?>
                        <?php if ($start + $limit < $entries_count_total and $use_pagination) { ?>
                        <span class="blog_pagination_older"><a
                                href="<?php Template::escape(buildSEOUrl()); ?>?start=<?php echo $next_start; ?>"><?php Template::escape(">>"); ?></a></span>

                    <?php } ?>
                </div>
            <?php } ?>
            <?php
        }
    }
}