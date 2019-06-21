<?php

class Forms {

    public static function getFormByID($id) {
        $retval = null;
        $query = db_query("select * from `" . tbname("forms") . "` WHERE id = " . intval($id));
        if (db_num_rows($query) > 0) {
            $retval = db_fetch_assoc($query);
        }

        return $retval;
    }

    public static function deleteForm($id) {
        $id = intval($id);
        return db_query("DELETE FROM " . tbname("forms") . " WHERE id = $id");
    }

    public static function createForm($name, $email_to, $subject, $category_id, $fields, $required_fields, $mail_from_field, $target_page_id, $enabled) {
        $name = db_escape($name);
        $enabled = intval($enabled);
        $email_to = db_escape($email_to);
        $subject = db_escape($subject);
        $category_id = intval($category_id);
        $fields = db_escape($fields);
        $required_fields = db_escape($required_fields);
        $mail_from_field = db_escape($mail_from_field);
        $target_page_id = intval($target_page_id);
        $created = time();
        $updated = time();

        return Database::query("INSERT INTO `" . tbname("forms") . "` (name, email_to, subject, category_id, `fields`, `required_fields`,
									 mail_from_field, target_page_id, `created`, `updated`, `enabled`)
                                     values
                                    ('$name', '$email_to', '$subject', $category_id, '$fields',
                                     '$required_fields',
									 '$mail_from_field', $target_page_id, $created, $updated, $enabled)", false) or die(Database::error());
    }

    public static function editForm($id, $name, $email_to, $subject, $category_id, $fields, $required_fields, $mail_from_field, $target_page_id, $enabled) {
        $name = db_escape($name);
        $enabled = intval($enabled);
        $email_to = db_escape($email_to);
        $subject = db_escape($subject);
        $category_id = intval($category_id);
        $fields = db_escape($fields);
        $required_fields = db_escape($required_fields);
        $mail_from_field = db_escape($mail_from_field);
        $target_page_id = intval($target_page_id);
        $updated = time();
        $id = intval($id);

        return db_query("UPDATE `" . tbname("forms") . "` set name='$name', email_to = '$email_to', subject = '$subject', category_id = $category_id,
									 fields = '$fields', required_fields = '$required_fields', mail_from_field = '$mail_from_field', target_page_id = $target_page_id, `updated` = $updated, enabled = $enabled WHERE id = $id");
    }

    public static function getAllForms() {
        $retval = [];
        $query = db_query("select * from `" . tbname("forms") . "` ORDER BY id");
        if (db_num_rows($query) > 0) {
            while ($row = db_fetch_assoc($query)) {
                $retval[] = $row;
            }
        }

        return $retval;
    }

    public static function submitForm($id) {
        $retval = false;
        $form = self::getFormByID($id);
        if ($form) {
            $fields = $form["fields"];
            $fields = Settings::mappingStringToArray($fields);
            $required_fields = StringHelper::linesFromString($form["required_fields"]);
            foreach ($required_fields as $field) {
                $fieldName = isset($fields[$field]) ? $fields[$field] : $field;
                if (!(isset($_POST[$field]) and ! empty($_POST[$field]))) {
                    ViewBag::set("exception", get_translation("please_fill_all_required_fields", array(
                        "%field%" => _esc($fieldName)
                    )));
                    $html = Template::executeDefaultOrOwnTemplate("exception.php");
                    HTMLResult($html, HttpStatusCode::BAD_REQUEST);
                }
            }
            $html = "<!DOCTYPE html>";
            $html .= "<html>";
            $html .= "<head>";
            $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
            $html .= '<meta charset="utf-8">';
            $html .= "</head>";
            $html .= "<body>";
            $html .= "<table border=\"1\"";
            foreach ($fields as $name => $label) {
                $html .= "<tr>";
                $html .= "<td><strong>" . _esc($label) . "</strong></td>";
                $html .= "<td>" . nl2br(_esc($_POST[$name])) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
            $html .= "</body>";
            $html .= "</html>";

            $email_to = $form["email_to"];
            $subject = $form["subject"];
            $target_page_id = $form["target_page_id"];
            $target_page_slug = getPageSlugByID($target_page_id);
            $redirect_url = buildSEOUrl($target_page_slug);

            $mail_from_field = $form["mail_from_field"];

            $email_from = $_POST[$mail_from_field];

            // if dns mx check is enabled check the mail domain
            if (!StringHelper::isNullOrEmpty($email_from) and Settings::get("check_mx_of_mail_address") and ! AntiSpamHelper::checkMailDomain($email_from)) {
                ExceptionResult(get_translation("mail_address_has_invalid_mx_entry"), HttpStatusCode::BAD_REQUEST);
            }

            $mail_from = StringHelper::isNotNullOrWhitespace($mail_from_field) ? array(
                $_POST[$mail_from_field]
                    ) : array(
                Settings::get("email")
            );
            sanitize($mail_from);

            $headers = "From: " . $mail_from[0] . "\n";
            $headers .= "Content-Type: text/html; charset=utf-8";

            if (Mailer::send($email_to, $subject, $html, $headers)) {
                Request::redirect($redirect_url);
                $retval = true;
            } else {
                translate("error_send_mail_form_failed");
                die();
            }
        }
        return $retval;
    }

}
