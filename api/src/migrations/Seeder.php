<?php

namespace Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Seed\AbstractSeed;

class Seeder extends AbstractSeed {
    /** @var \Illuminate\Database\Capsule\Manager $capsule */
    public $app;
    /** @var \Illuminate\Database\Schema\Builder $capsule */
    public $schema;

    public function init()  {
        $this->app = new Capsule;
        $this->app->addConnection([
          'driver'    => DATABASE_DRIVER,
          'host'      => DATABASE_HOST,
          'port'      => DATABASE_PORT,
          'database'  => DATABASE_NAME,
          'username'  => DATABASE_USERNAME,
          'password'  => DATABASE_PASSWORD,
          'charset'   => 'utf8',
          'collation' => 'utf8_unicode_ci',
        ]);

        $this->app->bootEloquent();
        $this->app->setAsGlobal();
        $this->schema = $this->app->schema();
    }
}