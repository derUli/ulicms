<?php

require_once "../vendor/autoload.php";
require_once "../classes/objects/files/File.php";
require_once "../lib/files.php";

class InstallerController {

    public static function getStep() {
        $step = 1;
        if (isset($_REQUEST["step"]) and ! empty($_REQUEST["step"])) {
            $step = intval($_REQUEST["step"]);
        }
        if ($step > 9) {
            Request::redirect("index.php?step=9");
        }

        return $step;
    }

    public static function initSessionVars() {
        $vars = array(
            "mysql_user",
            "mysql_host",
            "mysql_password",
            "mysql_database",
            "mysql_prefix",
            "language",
            "admin_password",
            "admin_user",
            "admin_email",
            "admin_lastname",
            "admin_firstname",
            "install_demodata"
        );
        foreach ($vars as $var) {
            if (!isset($_SESSION[$var])) {
                $_SESSION[$var] = "";
                switch ($var) {
                    case "install_demodata":
                        $_SESSION[$var] = "yes";
                        break;
                    case "mysql_host":
                        $_SESSION[$var] = "localhost";
                        break;
                    case "mysql_prefix":
                        $_SESSION[$var] = "ulicms_";
                        break;
                    case "admin_user":
                        $_SESSION[$var] = "admin";
                        break;
                }
            }
        }
    }

    public static function loadLanguageFile($lang) {
        include_once "lang/" . $lang . ".php";
        include_once "lang/all.php";
    }

    public static function getLanguage() {
        if (isset($_SESSION["language"]) and ! empty($_SESSION["language"])) {
            return basename($_SESSION["language"]);
        } else {
            $_SESSION["language"] = "en";
            return "en";
        }
    }

    public static function getTitle() {
        return constant("TRANSLATION_TITLE_STEP_" . self::getStep());
    }

    public static function getFooter() {
        $version = new UliCMSVersion();
        return "&copy; 2011 - " . $version->getReleaseYear() . " by <a href=\"http://www.ulicms.de\" target=\"_blank\">UliCMS</a>";
    }

    public static function submitAdminData() {
        $_SESSION["admin_password"] = $_POST["admin_password"];
        $_SESSION["admin_user"] = $_POST["admin_user"];
        $_SESSION["admin_email"] = $_POST["admin_email"];
        $_SESSION["admin_lastname"] = $_POST["admin_lastname"];
        $_SESSION["admin_firstname"] = $_POST["admin_firstname"];
        header("Location: index.php?step=6");
    }

    public static function submitTryConnect() {
        @$connection = mysqli_connect($_POST["servername"], $_POST["loginname"], $_POST["passwort"]);
        if ($connection == false) {
            die(TRANSLATION_DB_CONNECTION_FAILED);
        }

        // Check if database is present else try to create it.
        $query = mysqli_query($connection, "SHOW DATABASES");
        $databases = array();
        while ($row = mysqli_fetch_array($query)) {
            $databases[] = $row[0];
        }

        if (!in_array($_POST["datenbank"], $databases)) {
            // Try to create database if it not exists
            mysqli_query($connection, "CREATE DATABASE " . mysqli_real_escape_string($connection, $_POST["datenbank"]));
        }

        @$select = mysqli_select_db($connection, $_POST["datenbank"]);

        if ($select == false) {
            die(TRANSLATION_CANT_OPEN_SCHEMA);
        }

        $_SESSION["mysql_host"] = $_POST["servername"];
        $_SESSION["mysql_user"] = $_POST["loginname"];
        $_SESSION["mysql_password"] = $_POST["passwort"];
        $_SESSION["mysql_database"] = $_POST["datenbank"];
        $_SESSION["mysql_prefix"] = $_POST["mysql_prefix"];
    }

    public static function submitInstall() {

        @set_time_limit(60 * 10); // 10 Minuten

        if (!isset($_SESSION["install_index"])) {
            $_SESSION["install_index"] = 0;
        }
        $files = array();
        foreach (glob("../lib/migrations/up/*.sql") as $file) {
            $files[] = $file;
        }
        if (!empty($_SESSION["install_demodata"])) {
            $files[] = "../lib/migrations/up/opt/democontent.full.sql";
        } else {
            $files[] = "../lib/migrations/up/opt/democontent.min.sql";
        }

        $onefile = 100 / floatval(count($files));
        $currentPercent = floatval($_SESSION["install_index"]) * $onefile;

        if ($_SESSION["install_index"] == count($files)) {
            $str = TRANSLATION_INSTALL_X_OF_Y;
            $str = str_ireplace("%x%", $_SESSION["install_index"], $str);
            $str = str_ireplace("%y%", count($files), $str);
            echo '<!--finish--><div style="background-color:green;height:50px; width:' . intval(100) . '%"></div>';
            echo "<div class='info-text-progress'>" . $str . "</div>";
        } else {
            $sql_file = $files[$_SESSION["install_index"]];
            $str = TRANSLATION_INSTALL_X_OF_Y;
            $str = str_ireplace("%x%", $_SESSION["install_index"] + 1, $str);
            $str = str_ireplace("%y%", count($files), $str);
            @$connection = mysqli_connect($_SESSION["mysql_host"], $_SESSION["mysql_user"], $_SESSION["mysql_password"]) or die(TRANSLATION_DB_CONNECTION_FAILED);

            $select = mysqli_select_db($connection, $_SESSION["mysql_database"]);

            mysqli_query($connection, "SET NAMES 'utf8mb4'") or die(mysqli_error($connection));

// sql_mode auf leer setzen, da sich UliCMS nicht im strict_mode betreiben lässt
            mysqli_query($connection, "SET SESSION sql_mode = '';");

            if (!isset($_SESSION["salt"])) {
                $salt = uniqid();
                $_SESSION["salt"] = $salt;
            }

            if (!isset($_SESSION["ga_secret"])) {
                $ga = new PHPGangsta_GoogleAuthenticator();
                $ga_secret = $ga->createSecret();
                $_SESSION["ga_secret"] = $ga_secret;
            }

            if (!isset($_SESSION["encrypted_password"])) {
                $_SESSION["encrypted_password"] = hash("sha512", $_SESSION["salt"] . $_SESSION["admin_password"]);
            }

            $script = file_get_contents($sql_file);
            $prefix = mysqli_real_escape_string($connection, $_SESSION["mysql_prefix"]);
            $language = mysqli_real_escape_string($connection, $_SESSION["language"]);
            $admin_user = mysqli_real_escape_string($connection, $_SESSION["admin_user"]);
            $encrypted_password = mysqli_real_escape_string($connection, $_SESSION["encrypted_password"]);
            $admin_lastname = mysqli_real_escape_string($connection, $_SESSION["admin_lastname"]);
            $admin_firstname = mysqli_real_escape_string($connection, $_SESSION["admin_firstname"]);
            $admin_email = mysqli_real_escape_string($connection, $_SESSION["admin_email"]);
            $salt = mysqli_real_escape_string($connection, $_SESSION["salt"]);
            $script = str_ireplace("{prefix}", $prefix, $script);
            $script = str_ireplace("{language}", $language, $script);
            $script = str_ireplace("{admin_user}", $admin_user, $script);
            $script = str_ireplace("{encrypted_password}", $encrypted_password, $script);
            $script = str_ireplace("{salt}", $salt, $script);
            $script = str_ireplace("{ga_secret}", $_SESSION["ga_secret"], $script);
            $script = str_ireplace("{admin_lastname}", $admin_lastname, $script);
            $script = str_ireplace("{admin_firstname}", $admin_firstname, $script);
            $script = str_ireplace("{admin_email}", $admin_email, $script);
            $script = str_ireplace("{time}", time(), $script);

            mysqli_multi_query($connection, $script);
            while (mysqli_more_results($connection)) {
                mysqli_next_result($connection);
            }

            $sqlFileName = mysqli_real_escape_string($connection, basename($sql_file));

            mysqli_query($connection, "INSERT INTO {$prefix}dbtrack (component, name) values ('core', '$sqlFileName')");

            echo '<!--ok--><div style="background-color:green;height:50px; width:' . intval($currentPercent) . '%"></div>';
            echo "<div class='info-text-progress'>" . $str . "</div>";

            $_SESSION["install_index"] += 1;
        }
    }

    public static function submitCreateConfig() {
        $template_path = "templates/CMSConfig.tpl";
        $content = file_get_contents($template_path);
        $content = str_replace("{prefix}", $_SESSION["mysql_prefix"], $content);
        $content = str_replace("{mysql_host}", $_SESSION["mysql_host"], $content);
        $content = str_replace("{mysql_user}", $_SESSION["mysql_user"], $content);
        $content = str_replace("{mysql_password}", $_SESSION["mysql_password"], $content);
        $content = str_replace("{mysql_database}", $_SESSION["mysql_database"], $content);

        copy("../lib/CMSConfigSample.php", "../CMSConfig.php");

        $defaultConfigFile = "../content/configurations/default.php";

        $configurationDir = dirname($defaultConfigFile);
        if (!is_dir($configurationDir)) {
            mkdir($configurationDir);
        }

        if (file_put_contents($defaultConfigFile, $content)) {
            echo "<!--ok-->";
        } else {
            echo "<!--failed-->" . TRANSLATION_WRITE_CMS_CONFIG_FAILED;
            echo "<p><textarea rows=10 class=\"form-control\" readonly>" . htmlspecialchars($content) . "</textarea></p>";
        }
    }

    public static function submitDemodata() {
        if (isset($_REQUEST["install_demodata"])) {
            $_SESSION["install_demodata"] = "yes";
        } else {
            $_SESSION["install_demodata"] = "";
        }

        header("Location: index.php?step=7");
    }

    public static function SureRemoveDir($dir, $DeleteMe) {
        if (!$dh = @opendir($dir))
            return;
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..')
                continue;
            if (!@unlink($dir . '/' . $obj))
                sureRemoveDir($dir . '/' . $obj, true);
        }

        closedir($dh);
        if ($DeleteMe) {
            @rmdir($dir);
        }
    }

    public static function submitLoginToBackend() {
        $installerDir = "../installer";
        if (is_dir($installerDir)) {
            @sureRemoveDir($installerDir, true);
        }
        @session_destroy();
        header("Location: ../admin/");
    }

}
