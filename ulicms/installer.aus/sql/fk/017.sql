ALTER TABLE `{prefix}lists`
  ADD CONSTRAINT fk_parent
  FOREIGN KEY (parent_id) 
  REFERENCES `{prefix}content`(id)
  ON DELETE SET NULL;