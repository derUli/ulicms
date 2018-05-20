<?php
namespace GDPR\PersonalData\Response;

class ResponseBlock
{

    public $title;

    public $blockData = array();

    // an identifier string Will be used by deleteData() function
    public $identifier;
}