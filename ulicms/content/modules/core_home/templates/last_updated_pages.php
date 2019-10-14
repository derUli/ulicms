<?php
$controller = ControllerRegistry::get("HomeController");
$model = $controller->getModel();
?>
<table cellpadding="2">
    <tr style="font-weight: bold;">
        <td><?php translate("title"); ?>
        </td>
        <td><?php translate("date"); ?>
        </td>
        <td><?php translate("done_by"); ?>
        </td>
    </tr>
    <?php
    foreach ($model->lastModfiedPages as $row) {
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
                ?>" target="_blank"><?php
                       esc($row->title);
                       ?></a></td>
            <td><?php echo strftime("%x %X", $row->lastmodified) ?></td>
            <td><?php
                $autorName = $model->admins[$row->lastchangeby];
                if (!empty($autorName)) {

                } else {
                    $autorName = $model->admins[$row->author_id];
                }
                echo $autorName;
                ?></td>
        </tr>
        <?php
    }
    ?>
</table>