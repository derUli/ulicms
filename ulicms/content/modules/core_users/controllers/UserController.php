<?php

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

        $user->setSecondaryGroups(array());
        if (is_array($secondary_groups)) {
            foreach ($secondary_groups as $group) {
                $user->addSecondaryGroup(new Group($group));
            }
        }
        if ($sendMail) {
            $user->saveAndSendMail();
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

    /*
      public function updatePost() {
      $id = intval($_POST["id"]);
      $user = new User($id);
      if (!$user->getId() == $id) {
      ExceptionResult(get_translation("not_found"), HttpStatusCode::NOT_FOUND);
      }
      }

     */

    public function updatePost() {
        $acl = new ACL();
        if ($acl->hasPermission("users_edit") or $_POST["id"] == $_SESSION["login_id"]) {
            $id = intval($_POST["id"]);
            $username = db_escape($_POST["username"]);
            $lastname = db_escape($_POST["lastname"]);
            $firstname = db_escape($_POST["firstname"]);
            $email = db_escape($_POST["email"]);
            $password = $_POST["password"];
            // User mit eingeschränkten Rechten darf sich nicht selbst zum Admin machen können
            if ($acl->hasPermission("users")) {
                $admin = intval(isset($_POST["admin"]));
                if (isset($_POST["group_id"])) {
                    $group_id = $_POST["group_id"];
                    if (!is_numeric($group_id)) {
                        $group_id = "NULL";
                    } else {
                        $group_id = intval($group_id);
                    }
                } else {
                    $group_id = $_SESSION["group_id"];
                }
            } else {
                $user = getUserById($id);
                $admin = $user["admin"];
                $group_id = $user["group_id"];
                if (is_null($group_id)) {
                    $group_id = "NULL";
                }
            }
            // FIXME: Das SQL muss raus. Stattdessen das User-Model zum Speichern nutzen.
            $homepage = db_escape($_POST["homepage"]);
            $about_me = db_escape($_POST["about_me"]);
            $html_editor = db_escape($_POST["html_editor"]);
            $require_password_change = intval(isset($_POST["require_password_change"]));
            $locked = intval(isset($_POST["locked"]));

            $default_language = StringHelper::isNotNullOrWhitespace($_POST["default_language"]) ? "'" . Database::escapeValue($_POST["default_language"]) . "'" : "NULL";

            do_event("before_edit_user");
            $sql = "UPDATE " . tbname("users") . " SET username = '$username', `group_id` = " . $group_id . ", `admin` = $admin, firstname='$firstname',
      lastname='$lastname', email='$email',
      about_me = '$about_me', html_editor='$html_editor', require_password_change='$require_password_change', `locked`='$locked', `homepage` = '$homepage' , `default_language` = $default_language WHERE id=$id";

            db_query($sql);

            // save secondary groups
            $user = new User($id);
            $secondary_groups = $_POST["secondary_groups"];

            $user->setSecondaryGroups(array());
            if (is_array($secondary_groups)) {
                foreach ($secondary_groups as $group) {
                    $user->addSecondaryGroup(new Group($group));
                }
            }

            $user->save();

            $user = getUserById(get_user_id());
            $name = isset($user["username"]) ? $user["username"] : AuditLog::UNKNOWN;

            if ($this->logger) {
                $this->logger->debug("User $name - Edited user ({$username})");
            }

            if (!empty($password)) {
                changePassword($password, $id);
                if ($this->logger) {
                    $this->logger->debug("User $name - Changed password of user ({$username})");
                }
            }

            do_event("after_edit_user");

            if (!$acl->hasPermission("users")) {
                Request::redirect("index.php");
            } else {
                Request::redirect(ModuleHelper::buildActionURL("admins"));
            }
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
