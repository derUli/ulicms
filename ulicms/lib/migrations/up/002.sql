
CREATE PROCEDURE prepareDbFor2019_2()
	BEGIN
		IF EXISTS ( SELECT * FROM information_schema.columns WHERE table_name = '{prefix}users' AND column_name = 'skype_id' AND table_schema = DATABASE() ) THEN
			ALTER TABLE `{prefix}users` DROP COLUMN `skype_id`;
		END IF;

		IF EXISTS ( SELECT * FROM information_schema.columns WHERE table_name = '{prefix}users' AND column_name = 'twitter' AND table_schema = DATABASE() ) THEN
			ALTER TABLE `{prefix}users` DROP COLUMN `twitter`;
		END IF;

		IF EXISTS ( SELECT * FROM information_schema.columns WHERE table_name = '{prefix}users' AND column_name = 'notify_on_login' AND table_schema = DATABASE() ) THEN
			ALTER TABLE `{prefix}users` DROP COLUMN `notify_on_login`;
		END IF;

		IF NOT EXISTS ( SELECT * FROM information_schema.columns WHERE table_name = '{prefix}comments' AND column_name = 'read' AND table_schema = DATABASE() ) THEN
			ALTER TABLE `{prefix}comments` add COLUMN `read` tinyint(1) NOT NULL DEFAULT '0';
		END IF;

END;

CALL prepareDbFor2019_2;

DROP PROCEDURE prepareDbFor2019_2;