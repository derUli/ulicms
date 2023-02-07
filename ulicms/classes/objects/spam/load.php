<?php

$classes = array(
    "ISpamChecker",
    "SpamFilterConfiguration",
    "CommentSpamChecker",
    "SpamDetectionResult"
);
foreach ($classes as $class) {
    require_once dirname(__FILE__) . "/$class.php";
}
