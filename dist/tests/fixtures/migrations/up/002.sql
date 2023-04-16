ALTER TABLE {prefix}employees
ADD email varchar(255);

update {prefix}employees set email = 'foo@bar.de';