ALTER TABLE `{prefix}content`
  ADD KEY `systemname` (`systemname`(191)),
  ADD KEY `language` (`language`),
  ADD KEY `menu` (`menu`),
  ADD KEY `parent` (`parent`),
  ADD KEY `active` (`active`),
  ADD KEY `deleted_at` (`deleted_at`),
  ADD KEY `hidden` (`hidden`),
  ADD KEY `type` (`type`),
  ADD KEY `fk_category` (`category`),
  ADD KEY `fk_autor` (`author_id`),
  ADD KEY `fk_video` (`video`),
  ADD KEY `fk_audio` (`audio`),
  ADD KEY `fk_link_to_language` (`link_to_language`),
  ADD KEY `fk_content_group_id` (`group_id`);
  
  ALTER TABLE `{prefix}content`
  ADD CONSTRAINT `fk_audio` FOREIGN KEY (`audio`) REFERENCES `{prefix}audio` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_autor` FOREIGN KEY (`author_id`) REFERENCES `{prefix}users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category`) REFERENCES `{prefix}categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_content_group_id` FOREIGN KEY (`group_id`) REFERENCES `{prefix}groups` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_content_language` FOREIGN KEY (`language`) REFERENCES `{prefix}languages` (`language_code`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_link_to_language` FOREIGN KEY (`link_to_language`) REFERENCES `{prefix}languages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_parent_content` FOREIGN KEY (`parent`) REFERENCES `{prefix}content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_video` FOREIGN KEY (`video`) REFERENCES `{prefix}videos` (`id`) ON DELETE SET NULL;