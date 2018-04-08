
## Tests

Run all the tests
```
$ ./vendor/bin/phpunit api/src/tests/ --colors
```

Run a particular group of tests
```
$ ./vendor/bin/phpunit api/src/tests/CatalogTest.php --colors
```

## Migrations

Create a migration
```php
php vendor/bin/phinx create MyFirstMigration -c api/phinx-config.php
```

Run the migrations
```php
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php
```

Status of current migrations for enviroment "utest"
```php
php vendor/bin/phinx status -e utest -c api/phinx-config.php
```
Set a breakpoint for environment "utest"
```php
php vendor/bin/phinx breakpoint -t <20171129101240> -e utest -c api/phinx-config.php
```

## Seeders

Create a new seeder
```php
php vendor/bin/phinx seed:create UserSeeder -c api/phinx-config.php -e utest
```

Run the seeder
```php
$ php vendor/bin/phinx seed:run -s TalentTreeSeeder -c api/phinx-config.php -e utest
```