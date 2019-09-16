<?php
$questions = PollFactory::getAllQuestions();
?>
<p>
    <a href="<?php echo build_polls_admin_url("do=new"); ?>">[<?php translate("create_poll") ?>]</a>
    <a href="<?php echo build_polls_admin_url("do=info"); ?>"
       style="float: right">[<?php translate("info") ?>]</a>
</p>
<?php
if (count($questions) > 0) {
    ?>
    <div class="scroll">
        <table class="tablesorter">
            <thead>
                <tr>
                    <th><?php translate("id"); ?></th>
                    <th><?php translate("question"); ?></th>
                    <th><?php translate("votes_total"); ?></th>
                    <td class="no-sort"></td>
                    <td class="no-sort"></td>
                    <td class="no-sort"></td>
                    <td class="no-sort"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($questions as $q) {
                    ?>
                    <tr>
                        <td><?php echo $q->getID();
            ;
                    ?></td>
                        <td><?php Template::escape($q->title); ?></td>
                        <td style="text-align: right"><?php echo PollFactory::getVotesSum($q->getID()); ?></td>

                        <td style="text-align: center"><a
                                href="<?php echo build_polls_admin_url("poll_stats=" . $q->getID()); ?>"
                                target="_blank"><img
                                    src="<?php echo getModulePath("polls"); ?>gfx/statistics.png"
                                    alt="<?php translate("view_stats") ?>"
                                    title="<?php translate("view_stats"); ?>" class="mobile-big-image"></a></td>
                        <td style="text-align: center"><a
                                href="<?php echo build_polls_admin_url("do=edit&edit=" . $q->getID()); ?>"><img
                                    class="mobile-big-image" src="gfx/edit.png"
                                    alt="<?php translate("edit"); ?>"
                                    title="<?php translate("edit"); ?>"></a></td>

                        <td style="text-align: center"><form
                                action="<?php echo build_polls_admin_url("reset=" . $q->getID()); ?>"
                                method="post" class="delete-form"
                                onsubmit="return confirm('<?php translate("ask_for_reset_poll"); ?>');">
        <?php csrf_token_html(); ?> <input type="image"
                                       src="<?php echo getModulePath("polls"); ?>gfx/rubber.png"
                                       class="mobile-big-image" alt="<?php translate("reset_poll"); ?>"
                                       title="<?php translate("reset_poll"); ?>">


                            </form></td>

                        <td style="text-align: center"><form
                                action="<?php echo build_polls_admin_url("delete=" . $q->getID()); ?>"
                                method="post" class="delete-form"
                                onsubmit="return confirm('<?php translate("ask_for_delete"); ?>');">
        <?php csrf_token_html(); ?> <input type="image" src="gfx/delete.gif"
                                       class="mobile-big-image" alt="<?php translate("delete"); ?>"
                                       title="<?php translate("delete"); ?>">


                            </form></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    translate("no_polls_found");
}