


## Migrations

Create a migration
```php
php vendor/bin/phinx create MyFirstMigration -c api/phinx-config.php
```

Run the migrations
```php
php vendor/bin/phinx migrate -c api/phinx-config.php
```

Status of current migrations
```php
php vendor/bin/phinx status -e development -c api/phinx-config.php
```
Set a breakpoint
```php
php vendor/bin/phinx breakpoint -t <20171129101240> -e development -c api/phinx-config.php
```

## Seeders

Create a new seeder
```php
php vendor/bin/phinx seed:create UserSeeder -c api/phinx-config.php
```


Run the seeder
```php
$ php vendor/bin/phinx seed:run -s TalentTreeSeeder -c api/phinx-config.php
```