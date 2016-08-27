ALTER TABLE `{prefix}language`
  ADD CONSTRAINT fk_language 
  FOREIGN KEY (language) 
  REFERENCES `{prefix}languages`(language_code) 
  ON DELETE Set NULL;