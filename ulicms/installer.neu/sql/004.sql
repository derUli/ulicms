INSERT INTO `{prefix}users` (`id`, `old_encryption`,  `username`, `lastname`, `firstname`, `email`, `password`, `group`, `group_id`, `password_changed`, `admin`) VALUES
(1, 0, '{admin_user}', '{lastname}', '{firstname}', '{email}', '{encrypted_password}',50, 1, NOW(), 1);"
