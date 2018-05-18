<?php
namespace UliCMS\HTML;

function text($str){
    return \nl2br(\_esc($str));
}