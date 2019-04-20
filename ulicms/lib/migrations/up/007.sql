ALTER TABLE {prefix}log CHANGE `zeit` `time` timestamp NOT NULL DEFAULT 
CURRENT_TIMESTAMP;
