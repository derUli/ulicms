<?php
namespace GDPR\PersonalData;

abstract class Responder
{

    // $query is an e-mail address or a name as string
    // must return an array of ResponseBlock
    public abstract function getData($query);

    public abstract function deleteData($query);
}