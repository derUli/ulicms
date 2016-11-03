ALTER TABLE `{prefix}custom_fields`
  ADD CONSTRAINT `fk_content_id`
  FOREIGN KEY (`content_id`) 
  REFERENCES `{prefix}content`(id)
  ON DELETE CASCADE;
