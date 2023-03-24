<?php

use App\Models\Users\PasswordReset;

if (Settings::get("disable_password_reset")) {
    translate("function_is_disabled");
} else {
    ?>
    <?php
    $message = null;
    $color = "danger";
    if (isset($_POST["username"]) && !empty($_POST["username"])) {
        $username = $_POST["username"];
        $user = getUserByName($username);
        if ($user) {
            $passwordReset = new PasswordReset();
            $token = $passwordReset->addToken($user['id']);
            $passwordReset->sendMail($token, $user["email"], get_ip(), $user["firstname"], $user["lastname"]);
            $message = get_translation("PASSWORD_RESET_SUCCESSFUL");
            $color = "success";
        } else {
            $message = get_translation("NO_SUCH_USER");
        }
    }
    ?>
    <p>
        <a href="./" class="btn btn-default btn-back is-not-ajax"><i
                class="fa fa-arrow-left"></i> <?php translate("back_to_login"); ?></a>
    </p>
    <h1>
        <?php translate("reset_password"); ?>
    </h1>
    <form action="index.php?reset_password" method="post">
        <?php csrf_token_html(); ?><p>
            <strong><?php translate("username"); ?>
            </strong> <br /> <input type="text" name="username" value="" required>
        </p>
        <p>
            <button type="submit" class="btn btn-warning"><i class="fa fa-lock"></i> <?php translate("reset_password"); ?></button>
        </p>
        <?php
        if ($message) {
            ?>
            <div class="alert alert-<?php echo $color; ?>">
                <?php esc($message); ?>
            </div>

        <?php }
        ?>
    </form>
    <?php
}
