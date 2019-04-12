<?php

function ajaxOnChangeLanguage($lang, $menu, $parent) {
    ?>
    <option selected="selected" value="NULL">
        [
        <?php
        translate("none");
        ?>
        ]
    </option>
    <?php
    $pages = getAllPages($lang, "title", false, $menu);
    foreach ($pages as $key => $page) {
        ?>
        <option value="<?php
        echo $page["id"];
        ?>"
                <?php if ($page["id"] == $parent) echo "selected"; ?>>
                    <?php
                    echo esc($page["title"]);
                    ?>
            (ID:
            <?php
            echo $page["id"];
            ?>
            )
        </option>
        <?php
    }
}

// ajax_cmd abschaffen, stattdessen Actions verwenden
$ajax_cmd = $_REQUEST["ajax_cmd"];

switch ($ajax_cmd) {
    case "getPageListByLang":
        ajaxOnChangeLanguage($_REQUEST["mlang"], $_REQUEST["mmenu"], $_REQUEST["mparent"]);
        break;
    default:
        echo "Unknown Call";
        break;
}
