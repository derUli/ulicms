ALTER TABLE `{prefix}lists`
  ADD CONSTRAINT fk_lists_language
  FOREIGN KEY (language) 
  REFERENCES `{prefix}languages`(language_code)
  ON DELETE SET NULL;
