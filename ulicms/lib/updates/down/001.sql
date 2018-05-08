delete from `{prefix}entity_permissions` where entity_name = 'content' and entity_id in (select id from {prefix}content);
