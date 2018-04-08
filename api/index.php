<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
require '../vendor/autoload.php';

$origins = [
    'https://assets.breatheco.de/',
    'https://coding-editor-alesanchezr.c9users.io'
];
if(isset($_SERVER['HTTP_ORIGIN'])){
    foreach($origins as $o)
        if($_SERVER['HTTP_ORIGIN'] == $o) header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
}

// Run app
$app = new \BreatheCodeAPI([
    'authenticate' => true,
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
]);

$app->addRoutes([
    'assignment','atemplate','badge','catalog','cohort','location','profile',
    'specialty','student','task','teacher','user','util','workshop','wtemplate',
    'calendar']);
$app->run();