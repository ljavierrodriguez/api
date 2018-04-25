# ![alt text](https://assets.breatheco.de/apis/img/images.php?blob&random&cat=icon&tags=breathecode,32) BreatheCode API

This repository contains all the endpoints available in the main BreatheCode API
you can find a details explanation of all the endpoints here:

[https://documenter.getpostman.com/view/1757920/talent-tree-api/RVu2kq1k](https://documenter.getpostman.com/view/1757920/talent-tree-api/RVu2kq1k)

### Requierments:

1. PHP 7 ([click here if you are using cloud 9](https://community.c9.io/t/how-to-upgrade-a-php-workspace-to-version-7/8570)
2. Composer
3. MySQL (for now)

### [Installation Instructions](./INSTALL.md)
### [Project Structue](./STRUCTURE.md)
 
### Tests

Run all the tests
```
$ ./vendor/bin/phpunit api/src/tests/ --colors
```

Run a particular group of tests
```
$ ./vendor/bin/phpunit api/src/tests/CatalogTest.php --colors
```

## Migrations

***Create a migration***
```php
php vendor/bin/phinx create MyFirstMigration -c api/phinx-config.php
```
You need to create the content of the files manually.

***Run the migrations***
```php
php vendor/bin/phinx migrate -e utest -c api/phinx-config.php -e utest
```
***Status of current migrations for enviroment "utest"***
```php
php vendor/bin/phinx status -c api/phinx-config.php -e utest
```
***Set a breakpoint for environment "utest"***
```php
php vendor/bin/phinx breakpoint -t <20171129101240> -c api/phinx-config.php -e utest
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

### Running PHP on Sitegrground
```
For PHP 7 : /usr/local/php70/bin/php-cli
For PHP 7.1: /usr/local/php70/bin/php-cli

/usr/local/php70/bin/php-cli vendor/bin/phinx migrate -e utest -c api/phinx-config.php
```