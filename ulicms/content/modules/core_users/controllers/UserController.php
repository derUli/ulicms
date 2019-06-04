<?php

use UliCMS\Security\PermissionChecker;
use UliCMS\Constants\AuditLog;

class UserController extends Controller {

    private $logger;

    public function __construct() {
        $this->logger = LoggerRegistry::get("audit_log");
    }

    public function createPost() {
        $username = $_POST["username"];
        $lastname = $_POST["lastname"];
        $firstname = $_POST["firstname"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $default_language = StringHelper::isNotNullOrWhitespace($_POST["default_language"]) ? $_POST["default_language"] : null;
        $sendMail = isset($_POST["send_mail"]);
        $admin = boolval(isset($_POST["admin"]));
        $locked = boolval(isset($_POST["locked"]));
        $group_id = is_numeric($_POST["group_id"]) ? intval($_POST["group_id"]) : null;
        if ($group_id <= 0) {
            $group_id = null;
        }
        $require_password_change = intval(isset($_POST["require_password_change"]));

        // save secondary groups
        $user = new User();
        $user->setUsername($username);
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setDefaultLanguage($default_language);
        $user->setAdmin($admin);
        $user->setLocked($locked);
        $user->setGroupid($group_id);
        $user->setRequirePasswordChange($require_password_change);
        $secondary_groups = $_POST["secondary_groups"];

        $user->setSecondaryGroups([]);
        if (is_array($secondary_groups)) {
            foreach ($secondary_groups as $group) {
                $user->addSecondaryGroup(new Group($group));
            }
        }
        if ($sendMail) {
            $user->saveAndSendMail($password);
        } else {
            $user->save();
        }

        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Created a new user ({$username})");
        }
        Request::redirect(ModuleHelper::buildActionURL("admins"));
    }

    public function updatePost() {
        $permissionChecker = new PermissionChecker(get_user_id());
        if ($permissionChecker->hasPermission("users_edit") or $_POST["id"] == $_SESSION["login_id"]) {
            $id = intval($_POST["id"]);
            $lastname = $_POST["lastname"];
            $firstname = $_POST["firstname"];
            $password = $_POST["password"];
            $email = $_POST["email"];
            $default_language = StringHelper::isNotNullOrWhitespace($_POST["default_language"]) ? $_POST["default_language"] : null;
            $admin = boolval(isset($_POST["admin"]));
            $locked = boolval(isset($_POST["locked"]));

            $homepage = $_POST["homepage"];
            $about_me = $_POST["about_me"];
            $html_editor = $_POST["html_editor"];
            $group_id = is_numeric($_POST["group_id"]) ? intval($_POST["group_id"]) : null;

            if ($group_id <= 0) {
                $group_id = null;
            }

            $require_password_change = intval(isset($_POST["require_password_change"]));

            $user = new User($id);
            if (!$user->getId() == $id) {
                ExceptionResult(get_translation("not_found"), HttpStatusCode::NOT_FOUND);
            }
            $user->setLastname($lastname);
            $user->setFirstname($firstname);
            $user->setPassword($password);
            $user->setEmail($email);
            $user->setDefaultLanguage($default_language);

            if ($permissionChecker->hasPermission("users_edit")) {
                $user->setAdmin($admin);
                $user->setLocked($locked);
                $user->setGroupid($group_id);
                $user->setSecondaryGroups([]);

                $secondary_groups = $_POST["secondary_groups"];
                if (is_array($secondary_groups)) {
                    foreach ($secondary_groups as $group) {
                        $user->addSecondaryGroup(new Group($group));
                    }
                }
            }

            $user->setRequirePasswordChange($require_password_change);

            $user->setHomepage($homepage);
            $user->setAboutMe($about_me);
            $user->setHTMLEditor($html_editor);
            $user->save();

            if (!$permissionChecker->hasPermission("users")) {
                Request::redirect("index.php");
            } else {
                Request::redirect(ModuleHelper::buildActionURL("admins"));
            }
        } else {
            return ExceptionResult(get_translation("forbidden"), HttpStatusCode::FORBIDDEN);
        }
    }

    public function deletePost() {
        $id = intval($_GET["id"]);

        do_event("before_admin_delete");

        $user = new User($id);
        $user->delete();

        do_event("after_admin_delete");

        if ($this->logger) {
            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;
            $this->logger->debug("User $name - Deleted User with id ($admin)");
        }
        Request::redirect(ModuleHelper::buildActionURL("admins"));
    }

}
