ALTER TABLE `{prefix}content`
  ADD CONSTRAINT `fk_parent_content`
  FOREIGN KEY (`parent`) 
  REFERENCES `{prefix}content`(id)
  ON DELETE CASCADE;
