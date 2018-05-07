ALTER TABLE `{prefix}content` 
ADD `only_admins_can_edit` tinyint(1) NOT NULL DEFAULT '0',
ADD `only_group_can_edit` tinyint(1) NOT NULL DEFAULT '0',
ADD `only_owner_can_edit` tinyint(1) NOT NULL DEFAULT '0',
ADD `only_others_can_edit` tinyint(1) NOT NULL DEFAULT '0'