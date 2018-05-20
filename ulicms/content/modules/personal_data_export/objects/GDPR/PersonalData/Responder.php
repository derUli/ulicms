<?php
namespace GDPR\PersonalData;

interface Responder
{

    // $query is an e-mail address or a name as string
    // must return an array of Persons
    public function getData($query);

    public function deleteData($query);
    
    // returns an array of persons
    public function searchPerson($query);
}