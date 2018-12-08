<?php

function split_headers($headers)
{
    return Mailer::splitHeaders($headers);
}

function ulicms_mail($to, $subject, $message, $headers = "")
{
    return Mailer::send($to, $subject, $message, $headers);
}
