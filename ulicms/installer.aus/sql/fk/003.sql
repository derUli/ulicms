ALTER TABLE `{prefix}banner`
  ADD CONSTRAINT fk_category_id 
  FOREIGN KEY (category) 
  REFERENCES `{prefix}categories`(id) 
  ON DELETE Set NULL;
