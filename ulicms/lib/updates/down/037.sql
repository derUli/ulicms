ALTER TABLE `{prefix}entity_permissions`
  DROP PRIMARY KEY,
   ADD PRIMARY KEY(
     `entity_name`,
     `entity_id`);