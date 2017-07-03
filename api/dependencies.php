<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = [
    'settings' => [
        'displayErrorDetails' => true,

        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
        
        'determineRouteBeforeAppMiddleware' => false,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'c9',
            'username' => 'alesanchezr',
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];

$app = new \Slim\App;
// Database information
$settings = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'c9',
    'username' => 'alesanchezr',
    'password' => '',
    'collation' => 'utf8_general_ci',
    'prefix' => ''
);

// Bootstrap Eloquent ORM
$container = new Illuminate\Container\Container();
$connFactory = new \Illuminate\Database\Connectors\ConnectionFactory($container);
$conn = $connFactory->make($settings);
$app->db = $conn;
$resolver = new \Illuminate\Database\ConnectionResolver();
$resolver->addConnection('default', $conn);
$resolver->setDefaultConnection('default');
\Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);