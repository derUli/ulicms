<?php
namespace GDPR\PersonalData;

interface Responder
{

    // $query is an e-mail address or a name as string
    // must return an array of ResponseBlock
    public function getData($query);

    public function deleteData($query);
}