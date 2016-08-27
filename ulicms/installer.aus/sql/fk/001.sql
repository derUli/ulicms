ALTER TABLE `{prefix}audio`
  ADD CONSTRAINT fk_audio_category 
  FOREIGN KEY (category_id) 
  REFERENCES `{prefix}categories`(id) 
  ON DELETE Set NULL;
