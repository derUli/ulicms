ALTER TABLE `{prefix}forms`
  ADD CONSTRAINT fk_forms_category 
  FOREIGN KEY (category_id) 
  REFERENCES `{prefix}categories`(id) 
  ON DELETE Set NULL;
