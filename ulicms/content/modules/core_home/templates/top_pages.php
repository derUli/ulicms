<?php
$controller = ControllerRegistry::get("HomeController");
$model = $controller->getModel();
?>
<table>
    <tr style="font-weight: bold;
        ">
        <td><?php translate("title"); ?>
        </td>
        <td><?php translate("views"); ?>
        </td>
    </tr>
    <?php
    foreach ($model->topPages as $row) {

        $domain = getDomainByLanguage($row->language);
        if (!$domain) {
            $url = "../" . $row->slug . ".html";
        } else {
            $url = "http://" . $domain . "/" . $row->slug . ".html";
        }
        ?>
        <tr>
            <td><a href="<?php
    echo $url;
        ?>"
                   target="_blank"><?php
            esc($row->title);
        ?></a></td>
            <td align="right"><?php
                   echo $row->views;
        ?></td>
                <?php
        }
        ?>
    </tr>
</table>