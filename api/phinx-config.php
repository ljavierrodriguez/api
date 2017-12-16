<?php
require 'config.php';
return [
  'paths' => [
    'migrations' => 'api/migrations',
    'seeds' => 'api/seeds'
  ],
  'migration_base_class' => 'Migrations\Migration',
  'seed_base_class' => 'Migrations\Seeder',
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_database' => 'dev',
    'dev' => [
      'adapter' => DATABASE_DRIVER,
      'host' => DATABASE_HOST,
      'name' => DATABASE_NAME,
      'user' => DATABASE_USERNAME,
      'pass' => DATABASE_PASSWORD,
      'port' => DATABASE_PORT
    ]
  ]
];