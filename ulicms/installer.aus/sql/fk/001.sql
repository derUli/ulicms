ALTER TABLE `{prefix}audio`
  ADD CONSTRAINT fk_category 
  FOREIGN KEY (category_id) 
  REFERENCES `{prefix}categories`(id) 
  ON DELETE Set NULL;