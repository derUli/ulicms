@echo off
set ULICMS_ENVIRONMENT=test
vendor/bin/phpunit --bootstrap .\init.php tests/
