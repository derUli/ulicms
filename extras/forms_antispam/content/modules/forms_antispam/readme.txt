This anti spam module is based on the condition that most spambots are "dumb".
They try to fill out all fields.

1. You have to add a text field to your mail form.
2. Position it outside the visible area using css so users don't see it, but spambots do.
3. Add a configuration settings "antispam_field_name" that must be the name of your
field. Default value for "antispam_field_name" if not set is "fax".

If this field isn't empty, then it's a spam message and will be blocked.
