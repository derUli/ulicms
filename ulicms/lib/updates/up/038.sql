insert into `{prefix}entity_permissions` 
(`entity_name`, `entity_id`, `owner_user_id`, `owner_group_id`, 
`only_admins_can_edit`, `only_group_can_edit`, 
`only_owner_can_edit`, `only_others_can_edit`) 
select 'content', id, autor, group_id, `only_admins_can_edit`,
`only_group_can_edit`, `only_owner_can_edit`, `only_others_can_edit` 
from foo_content