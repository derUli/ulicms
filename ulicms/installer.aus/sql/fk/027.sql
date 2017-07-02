ALTER TABLE `{prefix}content`
  ADD CONSTRAINT fk_link_to_language
  FOREIGN KEY (link_to_language)
  REFERENCES `{prefix}languages`(id)
  ON DELETE CASCADE
