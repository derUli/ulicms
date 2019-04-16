<?php
if (isset($_POST["change_password"])) {
    if (!empty($_POST["password"]) and $_POST["password"] === $_POST["password_repeat"]) {
        $uid = get_user_id();
        changePassword($_POST["password"], $uid);
        db_query("UPDATE " . tbname("users") . " SET `require_password_change` = 0 where id = $uid");
        $_SESSION["require_password_change"] = 0;
    } else {
        ?>
        <div class="alert alert-danger"><?php translate("passwords_not_equal"); ?></div>
        <?php
    }
}
if (!$_SESSION["require_password_change"]) {
    echo '<script type="text/javascript">location.replace(window.location.href);</script>';
} else {
    ?>
    <form id="change_password_form" action="index.php" method="post">
        <?php csrf_token_html(); ?>
        <input name="username" type="hidden" value="<?php esc($_SESSION["ulicms_login"]); ?>">
        <h1><?php translate("change_password"); ?></h1>
        <p><?php translate("require_password_change_notice"); ?></p>
        <strong><?php translate("password"); ?></strong> <input
            name="password" id="password" type="password"
            class="password-security-check"
            autocomplete="new-password"> <br /> <br /> <strong><?php translate("password_repeat"); ?> </strong>
        <input name="password_repeat" id="password_repeat" type="password"
               autocomplete="new-password"> <br /></br>
        <button type="submit" name="change_password" class="btn btn-warning">
            <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
    </form>
    <?php
    enqueueScriptFile("../node_modules/password-strength-meter/dist/password.min.js");
    combinedScriptHtml();
    ?>
    <?php
}
