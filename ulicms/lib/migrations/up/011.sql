ALTER TABLE {prefix}content CHANGE `meta_description` `meta_description` varchar(255) DEFAULT NULL;
ALTER TABLE {prefix}content CHANGE `meta_keywords` `meta_keywords` varchar(255) DEFAULT NULL;

update {prefix}content set meta_description = null where meta_description = 'NULL';

update {prefix}content set meta_keywords = null where meta_keywords = 'NULL';