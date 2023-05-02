<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Helpers\AntiSpamHelper;
use App\Utils\Mailer;

class Forms {
    public static function getFormByID($id) {
        $retval = null;
        $result = Database::query('select * from `' . Database::tableName('forms') . '` WHERE id = ' . (int)$id);
        if (Database::getNumRows($result) > 0) {
            $retval = Database::fetchAssoc($result);
        }

        return $retval;
    }

    public static function createForm(
        $name,
        $email_to,
        $subject,
        $category_id,
        $fields,
        $required_fields,
        $mail_from_field,
        $target_page_id,
        $enabled
    ) {
        $name = Database::escapeValue($name);
        $enabled = (int)$enabled;
        $email_to = Database::escapeValue($email_to);
        $subject = Database::escapeValue($subject);
        $category_id = (int)$category_id;
        $fields = Database::escapeValue($fields);
        $required_fields = Database::escapeValue($required_fields);
        $mail_from_field = Database::escapeValue($mail_from_field);
        $target_page_id = (int)$target_page_id;
        $created = time();
        $updated = time();
        $sql = "INSERT INTO `{prefix}forms`
            (name, email_to, subject, category_id, `fields`,
                            `required_fields`, mail_from_field, target_page_id,
                            `created`, `updated`, `enabled`)
                                     values
                                    ('{$name}', '{$email_to}', '{$subject}', "
                . "{$category_id}, '{$fields}', '{$required_fields}', "
                . "'{$mail_from_field}', {$target_page_id}, {$created}, "
                . "{$updated}, {$enabled})";

        return Database::query($sql, true);
    }

    public static function editForm($id, $name, $email_to, $subject, $category_id, $fields, $required_fields, $mail_from_field, $target_page_id, $enabled) {
        $name = Database::escapeValue($name);
        $enabled = (int)$enabled;
        $email_to = Database::escapeValue($email_to);
        $subject = Database::escapeValue($subject);
        $category_id = (int)$category_id;
        $fields = Database::escapeValue($fields);
        $required_fields = Database::escapeValue($required_fields);
        $mail_from_field = Database::escapeValue($mail_from_field);
        $target_page_id = (int)$target_page_id;
        $updated = time();
        $id = (int)$id;

        return Database::query(
            'UPDATE `' . Database::tableName('forms') . "` set name='{$name}', "
            . "email_to = '{$email_to}', subject = '{$subject}', "
            . "category_id = {$category_id}, fields = '{$fields}', "
            . "required_fields = '{$required_fields}', "
            . "mail_from_field = '{$mail_from_field}', "
            . "target_page_id = {$target_page_id}, "
            . "`updated` = {$updated}, "
            . "enabled = {$enabled} WHERE id = {$id}"
        );
    }

    public static function getAllForms() {
        $retval = [];
        $result = Database::query('select * from `' . Database::tableName('forms') .
                '` ORDER BY id');
        if (Database::getNumRows($result) > 0) {
            while ($row = Database::fetchAssoc($result)) {
                $retval[] = $row;
            }
        }

        return $retval;
    }

    public static function submitForm($id) {
        $retval = false;
        $form = self::getFormByID($id);
        if ($form) {
            $fields = $form['fields'];
            $fields = Settings::mappingStringToArray($fields);
            $required_fields = \App\Helpers\StringHelper::linesFromString(
                $form['required_fields']
            );
            foreach ($required_fields as $field) {
                $fieldName = $fields[$field] ?? $field;
                if (! (isset($_POST[$field]) && ! empty($_POST[$field]))) {
                    \App\Storages\ViewBag::set('exception', get_translation(
                        'please_fill_all_required_fields',
                        [
                            '%field%' => _esc($fieldName)
                        ]
                    ));
                    $html = Template::executeDefaultOrOwnTemplate(
                        'exception.php'
                    );

                    HTMLResult($html, HttpStatusCode::BAD_REQUEST);
                }
            }
            $html = '<!DOCTYPE html>';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<meta http-equiv="content-type" content="text/html; '
                    . 'charset=utf-8">';
            $html .= '<meta charset="utf-8">';
            $html .= '</head>';
            $html .= '<body>';
            $html .= '<table border="1"';
            foreach ($fields as $name => $label) {
                $html .= '<tr>';
                $html .= '<td><strong>' . _esc($label) . '</strong></td>';
                $html .= '<td>' . nl2br(_esc($_POST[$name])) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            $html .= '</body>';
            $html .= '</html>';

            $email_to = $form['email_to'];
            $subject = $form['subject'];
            $target_page_id = $form['target_page_id'];
            $target_page_slug = getPageSlugByID($target_page_id);
            $redirect_url = buildSEOUrl($target_page_slug);

            $mail_from_field = $form['mail_from_field'];

            $email_from = $_POST[$mail_from_field];

            // if dns mx check is enabled check the mail domain
            if (! empty($email_from) &&
                    Settings::get('check_mx_of_mail_address') && ! AntiSpamHelper::checkMailDomain($email_from)) {
                ExceptionResult(
                    get_translation('mail_address_has_invalid_mx_entry'),
                    HttpStatusCode::BAD_REQUEST
                );
            }

            $mail_from = ! empty(
                $mail_from_field
            ) ?
                    [
                        $_POST[$mail_from_field]
                    ] : [
                        Settings::get('email')
                    ];

            sanitize_headers($mail_from);

            $headers = 'From: ' . $mail_from[0] . "\n";
            $headers .= 'Content-Type: text/html; charset=utf-8';

            if (Mailer::send($email_to, $subject, $html, $headers)) {
                Response::redirect($redirect_url);
                $retval = true;
            } else {
                translate('error_send_mail_form_failed');
                exit();
            }
        }
        return $retval;
    }

    public static function deleteForm($id) {
        $id = (int)$id;
        return Database::query('DELETE FROM ' . Database::tableName('forms') . " WHERE id = {$id}");
    }
}
