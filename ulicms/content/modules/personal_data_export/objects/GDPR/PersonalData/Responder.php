<?php
namespace GDPR\PersonalData;

abstract class Responder
{

    public abstract function getData($query);

    public abstract function deleteData($query);
}