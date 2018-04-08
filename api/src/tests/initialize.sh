#!/bin/bash -e

#delete test database if exists
[ -e tests.sqlite ] && rm tests.sqlite

#Run the migrations for the unit tests
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20171129101240
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20171129104946
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20171129110420
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180205162021
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180331233506

# Test for Talent Tree
./vendor/bin/phpunit api/src/tests/TalentTreeATest.php --colors
./vendor/bin/phpunit api/src/tests/TalentTreeBTest.php --colors