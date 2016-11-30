ALTER TABLE `{prefix}banner`
  ADD CONSTRAINT fk_banner_language
  FOREIGN KEY (`language`)
  REFERENCES `{prefix}languages`(`language_code`) 
  ON DELETE Set NULL;
