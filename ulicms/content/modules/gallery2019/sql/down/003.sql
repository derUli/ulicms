ALTER TABLE `{prefix}gallery` DROP FOREIGN KEY `fk_gallery_createdby`;
ALTER TABLE `{prefix}gallery` DROP FOREIGN KEY `fk_gallery_lastchangedby`;
ALTER TABLE `{prefix}gallery_images` DROP FOREIGN KEY `fk_gallery_images_gallery_id`;