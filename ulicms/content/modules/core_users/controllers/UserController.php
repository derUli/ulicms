<?php
class UserController extends Controller {
	public function createPost() {
		$username = $_POST ["admin_username"];
		$lastname = $_POST ["admin_lastname"];
		$firstname = $_POST ["admin_firstname"];
		$password = $_POST ["admin_password"];
		$email = $_POST ["admin_email"];
		$default_language = StringHelper::isNotNullOrWhitespace ( $_POST ["default_language"] ) ? $_POST ["default_language"] : null;
		$sendMail = isset ( $_POST ["send_mail"] );
		$admin = intval ( isset ( $_POST ["admin"] ) );
		$locked = intval ( isset ( $_POST ["locked"] ) );
		$group_id = is_numeric ( $_POST ["group_id"] ) ? intval ( $_POST ["group_id"] ) : null;
		if ($group_id <= 0) {
			$group_id = null;
		}
		$require_password_change = intval ( isset ( $_POST ["require_password_change"] ) );
		adduser ( $username, $lastname, $firstname, $email, $password, $sendMail, $group_id, $require_password_change, $admin, $locked, $default_language );
		Request::redirect ( ModuleHelper::buildActionURL ( "admins" ) );
	}
	public function updatePost() {
		$acl = new ACL ();
		if ($acl->hasPermission ( "users_edit" ) or $_POST ["id"] == $_SESSION ["login_id"]) {
			$id = intval ( $_POST ["id"] );
			$username = db_escape ( $_POST ["admin_username"] );
			$lastname = db_escape ( $_POST ["admin_lastname"] );
			$firstname = db_escape ( $_POST ["admin_firstname"] );
			$email = db_escape ( $_POST ["admin_email"] );
			$password = $_POST ["admin_password"];
			// User mit eingeschränkten Rechten darf sich nicht selbst zum Admin machen können
			if ($acl->hasPermission ( "users" )) {
				$admin = intval ( isset ( $_POST ["admin"] ) );
				if (isset ( $_POST ["group_id"] )) {
					$group_id = $_POST ["group_id"];
					if (! is_numeric ( $group_id )) {
						$group_id = "NULL";
					} else {
						$group_id = intval ( $group_id );
					}
				} else {
					$group_id = $_SESSION ["group_id"];
				}
			} else {
				$user = getUserById ( $id );
				$admin = $user ["admin"];
				$group_id = $user ["group_id"];
				if (is_null ( $group_id )) {
					$group_id = "NULL";
				}
			}
			// FIXME: Das SQL muss raus. Stattdessen das User-Model zum Speichern nutzen.
			$notify_on_login = intval ( isset ( $_POST ["notify_on_login"] ) );
			$twitter = db_escape ( $_POST ["twitter"] );
			$homepage = db_escape ( $_POST ["homepage"] );
			$skype_id = db_escape ( $_POST ["skype_id"] );
			$about_me = db_escape ( $_POST ["about_me"] );
			$html_editor = db_escape ( $_POST ["html_editor"] );
			$require_password_change = intval ( isset ( $_POST ["require_password_change"] ) );
			$locked = intval ( isset ( $_POST ["locked"] ) );
			
			$default_language = StringHelper::isNotNullOrWhitespace ( $_POST ["default_language"] ) ? "'" . Database::escapeValue ( $_POST ["default_language"] ) . "'" : "NULL";
			
			add_hook ( "before_edit_user" );
			$sql = "UPDATE " . tbname ( "users" ) . " SET username = '$username', `group_id` = " . $group_id . ", `admin` = $admin, firstname='$firstname',
lastname='$lastname', notify_on_login='$notify_on_login', email='$email', skype_id = '$skype_id',
about_me = '$about_me', html_editor='$html_editor', require_password_change='$require_password_change', `locked`='$locked', `twitter` = '$twitter', `homepage` = '$homepage' , `default_language` = $default_language WHERE id=$id";
			
			db_query ( $sql );
			
			if (! empty ( $password )) {
				changePassword ( $password, $id );
			}
			
			add_hook ( "after_edit_user" );
			;
			if (! $acl->hasPermission ( "users" )) {
				Request::redirect ( "index.php" );
			} else {
				Request::redirect ( ModuleHelper::buildActionURL ( "admins" ) );
			}
		}
	}
	public function deletePost() {
		$admin = intval ( $_GET ["admin"] );
		add_hook ( "before_admin_delete" );
		$query = db_query ( "DELETE FROM " . tbname ( "users" ) . " WHERE id='$admin'", $connection );
		add_hook ( "after_admin_delete" );
		Request::redirect ( ModuleHelper::buildActionURL ( "admins" ) );
	}
}