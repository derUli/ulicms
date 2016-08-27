ALTER TABLE `{prefix}content`
  ADD CONSTRAINT fk_language 
  FOREIGN KEY (language) 
  REFERENCES `{prefix}languages`(language_code) 
  ON DELETE Set NULL;