update `{prefix}settings` set value = 'no' WHERE name = 'logo_disabled' and (
select count(id) from `{prefix}settings` where name = 'logo_image' and value is not null and value <> '') > 0