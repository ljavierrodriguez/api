<?php
require 'config.php';

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
            'database' => DATABASE_NAME,
            'username' => DATABASE_USERNAME,
            'password' => DATABASE_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];

$app = new \Slim\App($config);
// Database information
$settings = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => DATABASE_NAME,
    'username' => DATABASE_USERNAME,
    'password' => DATABASE_PASSWORD,
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

define('GLOBAL_CONFIG',[
    "scopes" => [
        'sync_data',
        'read_basic_info',//very basic info like locations
        'user_profile',
        'read_talent_tree',
        'student_assignments',
        
        'teacher_assignments',
        'super_admin'//delete or update the most sensitive data
    ]
]);
    