ALTER TABLE 
`{prefix}contact_book`
ADD FULLTEXT ix_fulltext
(name, vorname, telefon, email);