ALTER TABLE `{prefix}group_languages`
  ADD CONSTRAINT fk_language
  FOREIGN KEY (language_id)
  REFERENCES `{prefix}languages`(id)
  ON DELETE CASCADE
