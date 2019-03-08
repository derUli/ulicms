-- Run this sql script before you upgrade UliCMS 2019.2 to UliCMS 2019.2.1 or later versions
-- Don't run this script you are already using UliCMS 2019.2.1

DELETE FROM `{prefix}dbtrack` WHERE component = 'core';

INSERT INTO `{prefix}dbtrack` (`id`, `component`, `name`, `date`) VALUES (NULL, 'core', '001.sql', CURRENT_TIMESTAMP);