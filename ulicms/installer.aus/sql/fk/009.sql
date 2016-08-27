ALTER TABLE `{prefix}language`
  ADD CONSTRAINT fk_audio
  FOREIGN KEY (audio) 
  REFERENCES `{prefix}audio`(id)
  ON DELETE Set NULL;