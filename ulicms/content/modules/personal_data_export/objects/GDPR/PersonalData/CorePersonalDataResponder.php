<?php
namespace GDPR\PersonalData;

use Database;
use StringHelper;

class CorePersonalDataResponder implements Responder
{

    public function getData($query)
    {
        throw new \NotImplementedException();
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
            // TODO: search in the {prefix}mails table
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