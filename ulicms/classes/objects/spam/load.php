<?php
$classes = array(
    "ISpamChecker",
    "SpamFilterConfiguration",
    "CommentSpamChecker",
    "SpamDetectionResult"
);
foreach ($classes as $class) {
    include_once dirname(__file__) . "/$class.php";
}
