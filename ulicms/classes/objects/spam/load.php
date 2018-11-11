<?php
$classes = array(
    "ISpamChecker",
    "SpamFilterConfiguration",
    "CommentSpamChecker",
    "SpamDetectionResult"
);
foreach ($classes as $class) {
    require dirname(__FILE__) . "/$class.php";
}
