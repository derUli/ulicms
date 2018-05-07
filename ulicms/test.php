<?php
include_once "init.php";
session_start();
Response::safeRedirect("http://www.google.de");
