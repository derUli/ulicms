<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Backend\UliCMSVersion;
use App\Storages\Settings\DotEnvLoader;

// TODO: Make it work without this
mysqli_report(MYSQLI_REPORT_OFF);

class InstallerController
{
    public static function getStep()
    {
        $step = 1;
        if (isset($_REQUEST['step']) && ! empty($_REQUEST['step'])) {
            $step = (int)$_REQUEST['step'];
        }
        if ($step > 10) {
            Response::redirect('index.php?step=10');
        }

        return $step;
    }

    public static function initSessionVars()
    {
        $vars = [
            'mysql_user',
            'mysql_host',
            'mysql_password',
            'mysql_database',
            'mysql_prefix',
            'language',
            'admin_password',
            'admin_user',
            'admin_email',
            'admin_lastname',
            'admin_firstname',
            'install_demodata'
        ];

        foreach ($vars as $var) {
            if (! isset($_SESSION[$var])) {
                $_SESSION[$var] = '';
                switch ($var) {
                    case 'install_demodata':
                        $_SESSION[$var] = 'yes';
                        break;
                    case 'mysql_host':
                        $_SESSION[$var] = 'localhost';
                        break;
                    case 'mysql_prefix':
                        $_SESSION[$var] = 'ulicms_';
                        break;
                }
            }
        }
    }

    public static function loadLanguageFile($lang)
    {
        include_once 'lang/' . $lang . '.php';
        include_once 'lang/all.php';
    }

    public static function getLanguage()
    {
        if (isset($_SESSION['language']) && ! empty($_SESSION['language'])) {
            return basename($_SESSION['language']);
        }
            $_SESSION['language'] = 'en';
            return 'en';

    }

    public static function getTitle()
    {
        return constant('TRANSLATION_TITLE_STEP_' . self::getStep());
    }

    public static function getFooter()
    {
        $version = new UliCMSVersion();
        return '&copy; 2011 - ' . $version->getReleaseYear() .
                ' by <a href="http://www.ulicms.de" '
                . 'target="_blank">UliCMS</a>';
    }

    public static function submitAdminData()
    {
        $_SESSION['admin_password'] = $_POST['admin_password'];
        $_SESSION['admin_user'] = $_POST['admin_user'];
        $_SESSION['admin_email'] = $_POST['admin_email'];
        $_SESSION['admin_lastname'] = $_POST['admin_lastname'];
        $_SESSION['admin_firstname'] = $_POST['admin_firstname'];
        header('Location: index.php?step=7');
    }

    public static function submitTryConnect()
    {
        @$connection = mysqli_connect(
            $_POST['servername'],
            $_POST['loginname'],
            $_POST['passwort']
        );

        if ($connection == false) {
            exit(TRANSLATION_DB_CONNECTION_FAILED);
        }

        // Check if database is present else try to create it.
        $query = mysqli_query($connection, 'SHOW DATABASES');
        $databases = [];
        while ($row = mysqli_fetch_array($query)) {
            $databases[] = $row[0];
        }

        if (! in_array($_POST['datenbank'], $databases)) {
            // Try to create database if it not exists
            mysqli_query(
                $connection,
                'CREATE DATABASE ' .
                mysqli_real_escape_string(
                    $connection,
                    $_POST['datenbank']
                )
            );
        }

        @$select = mysqli_select_db($connection, $_POST['datenbank']);

        if ($select == false) {
            exit(TRANSLATION_CANT_OPEN_SCHEMA);
        }

        $_SESSION['mysql_host'] = $_POST['servername'];
        $_SESSION['mysql_user'] = $_POST['loginname'];
        $_SESSION['mysql_password'] = $_POST['passwort'];
        $_SESSION['mysql_database'] = $_POST['datenbank'];
        $_SESSION['mysql_prefix'] = $_POST['mysql_prefix'];
    }

    public static function submitInstall()
    {
        @set_time_limit(60 * 10); // 10 Minuten

        if (! isset($_SESSION['install_index'])) {
            $_SESSION['install_index'] = 0;
        }
        $files = [];
        foreach (glob(ULICMS_ROOT . '/lib/migrations/up/*.sql') as $file) {
            $files[] = $file;
        }
        if (! empty($_SESSION['install_demodata'])) {
            $files[] = ULICMS_ROOT . '/lib/migrations/up/opt/democontent.full.sql';
        } else {
            $files[] = ULICMS_ROOT . '/lib/migrations/up/opt/democontent.min.sql';
        }

        $allSteps = count($files);
        $currentStep = (int)$_SESSION['install_index'];

        echo $currentStep >= $allSteps - 1 ?
                '<!--finish-->' : ' <!--ok-->';

        $sql_file = $files[$currentStep];

        @$connection = mysqli_connect(
            $_SESSION['mysql_host'],
            $_SESSION['mysql_user'],
            $_SESSION['mysql_password']
        );

        if ($connection == false) {
            exit(TRANSLATION_DB_CONNECTION_FAILED);
        }

        $select = mysqli_select_db($connection, $_SESSION['mysql_database']);

        mysqli_query($connection, "SET NAMES 'utf8mb4'");

        // sql_mode auf leer setzen, da sich UliCMS nicht im strict_mode betreiben lässt
        mysqli_query($connection, "SET SESSION sql_mode = '';");

        if (! isset($_SESSION['salt'])) {
            $salt = uniqid();
            $_SESSION['salt'] = $salt;
        }

        if (! isset($_SESSION['ga_secret'])) {
            $ga = new PHPGangsta_GoogleAuthenticator();
            $ga_secret = $ga->createSecret();
            $_SESSION['ga_secret'] = $ga_secret;
        }

        if (! isset($_SESSION['encrypted_password'])) {
            $_SESSION['encrypted_password'] = hash(
                'sha512',
                $_SESSION['salt'] . $_SESSION['admin_password']
            );
        }

        $script = is_file($sql_file) ? file_get_contents($sql_file) : '';
        $prefix = mysqli_real_escape_string(
            $connection,
            $_SESSION['mysql_prefix']
        );
        $language = mysqli_real_escape_string(
            $connection,
            $_SESSION['language']
        );
        $admin_user = mysqli_real_escape_string(
            $connection,
            $_SESSION['admin_user']
        );
        $encrypted_password = mysqli_real_escape_string(
            $connection,
            $_SESSION['encrypted_password']
        );
        $admin_lastname = mysqli_real_escape_string(
            $connection,
            $_SESSION['admin_lastname']
        );
        $admin_firstname = mysqli_real_escape_string(
            $connection,
            $_SESSION['admin_firstname']
        );
        $admin_email = mysqli_real_escape_string(
            $connection,
            $_SESSION['admin_email']
        );
        $salt = mysqli_real_escape_string(
            $connection,
            $_SESSION['salt']
        );
        $script = str_ireplace('{prefix}', $prefix, $script);
        $script = str_ireplace('{language}', $language, $script);
        $script = str_ireplace('{admin_user}', $admin_user, $script);
        $script = str_ireplace(
            '{encrypted_password}',
            $encrypted_password,
            $script
        );
        $script = str_ireplace('{salt}', $salt, $script);
        $script = str_ireplace('{ga_secret}', $_SESSION['ga_secret'], $script);
        $script = str_ireplace('{admin_lastname}', $admin_lastname, $script);
        $script = str_ireplace('{admin_firstname}', $admin_firstname, $script);
        $script = str_ireplace('{admin_email}', $admin_email, $script);
        $script = str_ireplace('{time}', time(), $script);

        $version = new UliCMSVersion();
        $script = str_ireplace(
            '{ulicms_version}',
            $version->getInternalVersionAsString(),
            $script
        );

        mysqli_multi_query($connection, $script);
        while (mysqli_more_results($connection)) {
            mysqli_next_result($connection);
        }

        $sqlFileName = mysqli_real_escape_string(
            $connection,
            basename($sql_file)
        );

        mysqli_query($connection, "INSERT INTO {$prefix
                }dbtrack (component, name) values ('core', '{$sqlFileName}')");

        echo "<progress value='{$currentStep}' max='{$allSteps}'>";
        $_SESSION['install_index'] += 1;
    }

    public static function submitCreateConfig()
    {

        $targetConfig = ULICMS_ROOT . '/.env';

        $loader = DotEnvLoader::fromEnvironment(ULICMS_ROOT, 'example');
        $loader->load();

        $_ENV['APP_ENV'] = 'default';

        // Database credentials
        $_ENV['DB_PREFIX'] = $_SESSION['mysql_prefix'];
        $_ENV['DB_SERVER'] = $_SESSION['mysql_host'];
        $_ENV['DB_USER'] = $_SESSION['mysql_user'];
        $_ENV['DB_PASSWORD'] = $_SESSION['mysql_password'];
        $_ENV['DB_DATABASE'] = $_SESSION['mysql_database'];

        $dotEnvContent = '';
        foreach($_ENV as $key => $value) {

            if(is_bool($value)){
                $value = strbool($value);
            }

            $dotEnvContent .= "{$key}={$value}" . PHP_EOL;
        }

        if (file_put_contents($targetConfig, $dotEnvContent)) {
            echo '<!--ok-->';
        } else {
            echo '<!--failed-->' . TRANSLATION_WRITE_CMS_CONFIG_FAILED;
            echo '<p><textarea rows=10 class="form-control" readonly>' .
            htmlspecialchars($dotEnvContent) . '</textarea></p>';
        }
    }

    public static function submitDemodata()
    {
        if (isset($_REQUEST['install_demodata'])) {
            $_SESSION['install_demodata'] = 'yes';
        } else {
            $_SESSION['install_demodata'] = '';
        }

        header('Location: index.php?step=8');
    }

    public static function SureRemoveDir($dir, $DeleteMe)
    {
        if (! $dh = @opendir($dir)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..') {
                continue;
            }
            if (! @unlink($dir . '/' . $obj)) {
                sureRemoveDir($dir . '/' . $obj, true);
            }
        }

        closedir($dh);
        if ($DeleteMe) {
            @rmdir($dir);
        }
    }

    public static function submitLoginToBackend()
    {
        $installerDir = '../installer';
        if (is_dir($installerDir)) {
            @sureRemoveDir($installerDir, true);
        }

        @session_destroy();
        header('Location: ../admin/');
    }
}
