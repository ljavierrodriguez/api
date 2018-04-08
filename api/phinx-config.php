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
    'utest' => [
      'adapter' => UT_DB_DRIVER,
      'host' => UT_DB_HOST,
      'name' => UT_DB_NAME,
      'user' => UT_DB_USERNAME,
      'pass' => UT_DB_PASSWORD,
      'port' => UT_DB_PORT
    ],
    'dev' => [
      'adapter' => DATABASE_DRIVER,
      'host' => DATABASE_HOST,
      'name' => DATABASE_NAME,
      'user' => DATABASE_USERNAME,
      'pass' => DATABASE_PASSWORD,
      'port' => DATABASE_PORT
    ],
    'prod' => [
      'adapter' => DATABASE_DRIVER,
      'host' => DATABASE_HOST,
      'name' => DATABASE_NAME,
      'user' => DATABASE_USERNAME,
      'pass' => DATABASE_PASSWORD,
      'port' => DATABASE_PORT
    ]
  ]
];