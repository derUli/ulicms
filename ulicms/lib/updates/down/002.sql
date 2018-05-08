ALTER TABLE `{prefix}content`
  ADD CONSTRAINT fk_content_group_id
  FOREIGN KEY (`group_id`)
  REFERENCES `{prefix}groups`(id)
  ON DELETE NO ACTION;