<?php

class Person
{

    public $email;

    public $name;

    // Array of ResponseBlock
    public $blocks = array();

    // an identifier string Will be used by deleteData() function
    public $identifier;
}