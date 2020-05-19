ALTER TABLE {prefix}content
ADD CONSTRAINT ix_slug_language UNIQUE KEY(`slug`, `language`);

ALTER TABLE {prefix}languages
ADD CONSTRAINT ix_language_language_code UNIQUE KEY(`language_code`);

ALTER TABLE {prefix}users
ADD CONSTRAINT ix_user_username UNIQUE KEY(`username`);

ALTER TABLE {prefix}content ADD INDEX ix_content_position (`position`);
ALTER TABLE {prefix}content ADD INDEX ix_content_language (`language`);
ALTER TABLE {prefix}content ADD INDEX ix_content_menu (`menu`);
ALTER TABLE {prefix}content ADD INDEX ix_content_active (`active`);
ALTER TABLE {prefix}content ADD INDEX ix_content_deleted_at (`deleted_at`);
ALTER TABLE {prefix}content ADD INDEX ix_content_hidden (`hidden`);
ALTER TABLE {prefix}content ADD INDEX ix_content_type (`type`);

ALTER TABLE {prefix}settings ADD INDEX ix_settings_name (`name`);