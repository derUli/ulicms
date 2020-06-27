<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("pages")
        or $permissionChecker->hasPermission("banners")
        or $permissionChecker->hasPermission("categories")
        or $permissionChecker->hasPermission("export")
        or $permissionChecker->hasPermission("forms")) {
    ?>
    <h2><?php translate("contents"); ?></h2>
    <strong><?php translate("select_content_type"); ?> </strong>
    <div class="button-menu">
        <?php
        if ($permissionChecker->hasPermission("pages")) {
            ?>
            <a
                href="index.php?action=pages"
                class="btn btn-default is-not-ajax"
                ><i
                    class="fas fa-book"></i> <?php translate("pages"); ?></a>

            <?php
        } ?>
        <?php
        if ($permissionChecker->hasPermission("comments_manage")) {
            ?>
            <a href="?action=comments_manage"
               class="btn btn-default is-not-ajax voffset2"
               ><i
                    class="fa fa-comments" aria-hidden="true"></i>
                <?php translate("comments"); ?></a>
            <?php
        } ?>
        <?php
        if ($permissionChecker->hasPermission("forms")) {
            ?><a href='?action=forms'
               class="btn btn-default is-not-ajax voffset2"
               ><i
                    class="fab fa-wpforms" aria-hidden="true"></i>
                    <?php
                    translate("forms"); ?></a> 
            <?php
        }
    if ($permissionChecker->hasPermission("banners")) {
        ?>
            <a href="index.php?action=banner"
               class="btn btn-default is-not-ajax voffset2"
               ><i
                    class="fas fa-bullhorn"></i>
                <?php translate("advertisements"); ?></a>

            <?php
    }
    if ($permissionChecker->hasPermission("categories")) {
        ?>
            <a href="index.php?action=categories"
               class="btn btn-default is-not-ajax voffset2"><i
                    class="fa fa-list-alt" aria-hidden="true"></i>
                <?php translate("categories"); ?></a>
                <?php
    } ?>
    </div>
    <?php
    do_event("content_type_list_entry");
} else {
    noPerms();
}
