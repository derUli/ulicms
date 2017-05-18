ALTER TABLE `{prefix}lists`
  ADD CONSTRAINT fk_lists_category
  FOREIGN KEY (category_id) 
  REFERENCES `{prefix}categories`(id)
  ON DELETE SET NULL;
