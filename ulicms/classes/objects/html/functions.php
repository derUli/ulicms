<?php
namespace UliCMS\HTML;

function text($str){
    return n2lbr(_esc($str));
}