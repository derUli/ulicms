ALTER TABLE `{prefix}lists`
  ADD CONSTRAINT fk_content
  FOREIGN KEY (content_id) 
  REFERENCES `{prefix}content`(id)
  ON DELETE CASCADE;