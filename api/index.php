<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Api\Src\Handlers;

use Chadicus\Slim\OAuth2\Routes;
use Chadicus\Slim\OAuth2\Middleware;
use Slim\Http;
use Slim\Views;
use OAuth2\Storage;
use OAuth2\GrantType;

require '../vendor/autoload.php';
require 'dependencies.php';

/**
 * OAuth 2.0 implementation
 * Using externarl library: https://github.com/chadicus/slim-oauth2
 * And PHP Oauth Server: https://bshaffer.github.io/oauth2-server-php-docs/
 **/
$storage = new OAuth2\Storage\Pdo(array('dsn' => 'mysql:host=localhost;dbname=c9', 'username' => 'alesanchezr', 'password' => ''));
$server = new OAuth2\Server($storage,array(
    'access_lifetime' => 86400
));

//Enable Authorization Code credentials to allow request from authorization code.
$server->addGrantType(new GrantType\AuthorizationCode($storage));
//Enable ClientCredentials to allo clients to generate an authorization code.
$server->addGrantType(new GrantType\ClientCredentials($storage));
//Enable user login form
$server->addGrantType(new GrantType\UserCredentials($storage));

//The HTML views for the OAuth Autentication process
$renderer = new Views\PhpRenderer( __DIR__ . '/vendor/chadicus/slim-oauth2-routes/templates');
$app->map(['GET', 'POST'], Routes\Authorize::ROUTE, new Routes\Authorize($server, $renderer))->setName('authorize');
$app->post(Routes\Token::ROUTE, new Routes\Token($server))->setName('token');
$app->map(['GET', 'POST'], Routes\ReceiveCode::ROUTE, new Routes\ReceiveCode($renderer))->setName('receive-code');

//Creating the Middleware to intercept all request and ask for authorization before continuing
$authorization = new Middleware\Authorization($server, $app->getContainer());

/**
 * Everything Related to the student itself
 **/
$studentHandler = new StudentHandler($app);
$app->get('/students/', array($studentHandler, 'getAllStudentsHandler'))->add($authorization);
$app->get('/student/{student_id}', array($studentHandler, 'getStudentHandler'))->add($authorization);
$app->post('/student/', array($studentHandler, 'createOrUpdateStudentHandler'))->add($authorization);
$app->post('/student/{student_id}', array($studentHandler, 'createOrUpdateStudentHandler'))->add($authorization);
$app->delete('/student/{student_id}', array($studentHandler, 'deleteStudentHandler'))->add($authorization);

/**
 * Everything Related to the student badges
 **/
$badgeHandler = new BadgeHandler($app);
$app->get('/badges/', array($badgeHandler, 'getAllBadgesHandler'))->add($authorization);
$app->get('/badges/student/{student_id}', array($badgeHandler, 'getAllStudentBadgesHandler'))->add($authorization);
$app->get('/badge/{badge_id}', array($badgeHandler, 'getBadgeHandler'))->add($authorization);
$app->post('/badge/', array($badgeHandler, 'createOrUpdateBadgeHandler'))->add($authorization);
$app->post('/badge/{badge_id}', array($badgeHandler, 'createOrUpdateBadgeHandler'))->add($authorization);
$app->delete('/badge/{badge_id}', array($badgeHandler, 'deleteBadgeHandler'))->add($authorization);

/**
 * Everything Related to the student activities
 **/
$app->post('/activity/student/{student_id}', array($studentHandler, 'createStudentActivityHandler'))->add($authorization);
$app->get('/activity/student/{student_id}', array($studentHandler, 'getStudentActivityHandler'))->add($authorization);
$app->delete('/activity/{activity_id}', array($studentHandler, 'deleteStudentActivityHandler'))->add($authorization);

/**
 * Everything Related to the student specialties
 **/
$specialtyHandler = new SpecialtyHandler($app);
$app->get('/specialties/profile/{profile_id}', array($specialtyHandler, 'getProfileSpecialtiesHandler'))->add($authorization);
$app->get('/specialties/student/{student_id}', array($specialtyHandler, 'getStudentSpecialtiesHandler'))->add($authorization);
$app->get('/specialty/{specialty_id}', array($specialtyHandler, 'getSpecialtyHandler'))->add($authorization);
$app->post('/specialty/{specialty_id}', array($specialtyHandler, 'createOrUpdateSpecialtyHandler'))->add($authorization);
$app->post('/specialty/', array($specialtyHandler, 'createOrUpdateSpecialtyHandler'))->add($authorization);
$app->delete('/specialty/{specialty_id}', array($studentHandler, 'deleteSpecialtyHandler'))->add($authorization);

$app->run();