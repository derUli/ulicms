<?php
namespace GDPR\PersonalData;

use Database;
use StringHelper;
use GDPR\PersonalData\Response\ResponseBlock;
use GDPR\PersonalData\Response\BlockData;

class CorePersonalDataResponder implements Responder
{

    public function getData($query)
    {
        $person = new \Person();
        
        $userQuery = Database::pQuery("select * from `{prefix}users` where email = ?", array(
            trim($query)
        ), true);
        if (Database::getNumRows($userQuery)) {
            $row = Database::fetchObject($userQuery);
            $person->email = $row->email;
            if (StringHelper::IsNotNullOrWhitespace($row->lastname) and StringHelper::IsNotNullOrWhitespace($row->firstname)) {
                $person->name = "{$row->lastname}, {$row->firstname}";
            } else if (StringHelper::IsNotNullOrWhitespace($row->lastname)) {
                $person->name = $row->lastname;
            } else {
                $person->name = $row->username;
            }
            $person->identifier = "{$row->email}";
            $block = new ResponseBlock();
            $block->title = get_translation("data_export");
            // TODO: Output only human readable fields (about_me, skype_id, etc.)
            foreach ((array) $row as $key => $value) {
                $data = new BlockData();
                $data->title = get_translation("user_profile");
                $translatedKey = get_translation($key);
                $data->data[$translatedKey] = $value;
                $block->blockData[] = $data;
            }
            $person->blocks[] = $block;
        }
        $mailQuery = Database::pQuery("select * from {prefix}mails where `to` = ?", array(
            trim($query)
        
        ), true);
        $block = new ResponseBlock();
        $block->title = get_translation("emails");
        if (Database::getNumRows($mailQuery)) {
            $block = new ResponseBlock();
            $block->title = get_translation("email");
            while ($mail = Database::fetchObject($mailQuery)) {
                foreach ((array) $mail as $key => $value) {
                    $data = new BlockData();
                    $data->title = get_translation("user_profile");
                    $data->data[$key] = $value;
                    $block->blockData[] = $data;
                }
            }
            $person->blocks[] = $block;
        }
        
        return $person;
    }

    public function deleteData($query)
    {
        throw new \NotImplementedException();
    }

    public function searchPerson($query)
    {
        $results = array();
        $dbResult = null;
        if (str_contains("@", $query)) {
            $dbResult = Database::pQuery("select id, username, lastname, firstname, email 
                            from {prefix}users where email = ? or username = ?", array(
                $query,
                $query
            ), true);
        } else if (str_contains(", ", $query)) {
            $splitted = explode(", ", $query);
            $dbResult = Database::pQuery("select id, username, lastname, firstname, email
                            from {prefix}users where lastname = ? or firstname = ?", array(
                trim($splitted[0]),
                trim($splitted[1])
            ), true);
        } else {
            $dbResult = Database::pQuery("select id, username, lastname, firstname, email
                            from {prefix}users where lastname = ? or username = ?", array(
                trim($query),
                trim($query)
            ), true);
        }
        if ($dbResult and Database::getNumRows($dbResult) > 0) {
            $row = Database::fetchObject($dbResult);
            $person = new \Person();
            $person->email = $row->email;
            if (StringHelper::IsNotNullOrWhitespace($row->lastname) and StringHelper::IsNotNullOrWhitespace($row->firstname)) {
                $person->name = "{$row->lastname}, {$row->firstname}";
            } else if (StringHelper::IsNotNullOrWhitespace($row->lastname)) {
                $person->name = $row->lastname;
            } else {
                $person->name = $row->username;
            }
            $person->identifier = "{$row->email}";
            $results[] = $person;
        } else if (str_contains("@", $query)) {
            $dbResult = Database::pQuery("select `to` as email
                            from {prefix}mails where `to` = ?", array(
                trim($query)
            ), true);
            if (Database::getNumRows($dbResult)) {
                $row = Database::fetchObject($dbResult);
                $person = new \Person();
                $person->email = $row->email;
                $person->name = "-";
                $person->identifier = "{$row->email}";
                $results[] = $person;
            }
        }
        
        return $results;
    }
}