ALTER TABLE {prefix}content CHANGE `meta_description` `meta_description` varchar(255) DEFAULT NULL;
update {prefix}content set meta_description = null where meta_description = 'NULL';
