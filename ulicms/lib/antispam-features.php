<?php

// String contains chinese chars?
function is_chinese($str)
{
    return AntiSpamHelper::isChinese($str);
}

// checking if this Country is blocked by spamfilter
function isCountryBlocked()
{
    return AntiSpamHelper::isCountryBlocked();
}
