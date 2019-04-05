#!/bin/bash -e

#delete test database if exists
[ -e tests.sqlite ] && rm tests.sqlite

#Run the migrations for the unit tests

php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180507130418
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180507130506
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180507130548
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180507130637
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -t 20180507130720

# Test for the api
./vendor/bin/phpunit api/src/tests/teacher/TeacherTest.php --colors