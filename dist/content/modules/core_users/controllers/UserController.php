<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

class UserController extends \App\Controllers\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function _createPost(): User {
        $username = $_POST['username'];
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $default_language = ! empty($_POST['default_language']) ? $_POST['default_language'] : null;
        $sendMail = isset($_POST['send_mail']);
        $admin = isset($_POST['admin']);
        $locked = isset($_POST['locked']);
        $group_id = is_numeric($_POST['group_id']) ? (int)$_POST['group_id'] : null;
        $require_password_change = (int)isset($_POST['require_password_change']);

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
        $user->setPrimaryGroupId($group_id);
        $user->setRequirePasswordChange($require_password_change);
        $secondary_groups = $_POST['secondary_groups'] ?? [];

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

        return $user;
    }

    public function createPost(): void {
        $this->_createPost();
        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('admins'));
    }

    public function updatePost(): void {
        $permissionChecker = new PermissionChecker(get_user_id());
        if ($permissionChecker->hasPermission('users_edit') || $_POST['id'] == $_SESSION['login_id']) {
            $id = (int)$_POST['id'];
            $lastname = $_POST['lastname'];
            $firstname = $_POST['firstname'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $default_language = ! empty($_POST['default_language']) ? $_POST['default_language'] : null;
            $admin = isset($_POST['admin']);
            $locked = isset($_POST['locked']);

            $homepage = $_POST['homepage'];
            $about_me = $_POST['about_me'];
            $html_editor = $_POST['html_editor'];
            $group_id = is_numeric($_POST['group_id']) ? (int)$_POST['group_id'] : null;

            if ($group_id <= 0) {
                $group_id = null;
            }

            $require_password_change = (int)isset($_POST['require_password_change']);

            $user = new User($id);
            if (! $user->getId() == $id) {
                ExceptionResult(get_translation('not_found'), \App\Constants\HttpStatusCode::NOT_FOUND);
            }

            $user->setLastname($lastname);
            $user->setFirstname($firstname);

            // set new password if changed
            if ($password) {
                $user->setPassword($password);
            }

            $user->setEmail($email);
            $user->setDefaultLanguage($default_language);

            if ($permissionChecker->hasPermission('users_edit')) {
                $user->setAdmin($admin);
                $user->setLocked($locked);
                $user->setPrimaryGroupId($group_id);
                $user->setSecondaryGroups([]);

                $secondary_groups = $_POST['secondary_groups'] ?? [];
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

            if (! empty($_FILES['avatar']['name'])) {
                if (! $user->changeAvatar($_FILES['avatar'])) {
                    ExceptionResult(
                        get_translation('avatar_upload_failed')
                    );
                }
            }

            if (Request::getVar('delete_avatar')) {
                $user->removeAvatar();
            }

            if (! $permissionChecker->hasPermission('users')) {
                Response::redirect('index.php');
            } else {
                Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('admins'));
            }
        }
        ExceptionResult(get_translation('forbidden'), \App\Constants\HttpStatusCode::FORBIDDEN);
    }

    public function deletePost(): void {
        $id = Request::getVar('id', 0, 'int');

        $this->_deletePost($id);
        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('admins'));
    }

    public function _deletePost(int $id): bool {
        do_event('before_admin_delete');

        $user = new User($id);
        if (! $user->isPersistent()) {
            return false;
        }

        $user->delete();

        do_event('after_admin_delete');

        return true;
    }
}
