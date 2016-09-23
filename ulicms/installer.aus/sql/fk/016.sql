ALTER TABLE `{prefix}lists`
  ADD CONSTRAINT fk_lists_content
  FOREIGN KEY (content_id) 
  REFERENCES `{prefix}content`(id)
  ON DELETE CASCADE;
