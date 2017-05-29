<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Api\Src\Handlers;

require '../vendor/autoload.php';
require 'dependencies.php';

$studentHandler = new StudentHandler($app);
$badgeHandler = new BadgeHandler($app);
$app->get('/student/{student_id}', array($studentHandler, 'getStudentHandler'));
$app->post('/student/', array($studentHandler, 'createOrUpdateStudentHandler'));
$app->post('/student/{student_id}', array($studentHandler, 'createOrUpdateStudentHandler'));
$app->delete('/student/{student_id}', array($studentHandler, 'deleteStudentHandler'));

$app->get('/badges/student/{student_id}', array($badgeHandler, 'getAllStudentBadgesHandler'));
$app->get('/badge/{badge_id}', array($badgeHandler, 'getBadgeHandler'));
$app->post('/badge/', array($badgeHandler, 'createOrUpdateBadgeHandler'));
$app->post('/badge/{badge_id}', array($badgeHandler, 'createOrUpdateBadgeHandler'));
$app->delete('/badge/{badge_id}', array($badgeHandler, 'deleteBadgeHandler'));

$app->post('/activity/student/{student_id}', array($studentHandler, 'createStudentActivityHandler'));
$app->get('/activity/student/{student_id}', array($studentHandler, 'getStudentActivityHandler'));
$app->delete('/activity/{activity_id}', array($studentHandler, 'deleteStudentActivityHandler'));

$specialtyHandler = new SpecialtyHandler($app);
$app->get('/specialties/profile/{profile_id}', array($specialtyHandler, 'getProfileSpecialtiesHandler'));
$app->get('/specialties/student/{student_id}', array($specialtyHandler, 'getStudentSpecialtiesHandler'));
$app->get('/specialty/{specialty_id}', array($studentHandler, 'getSpecialtyHandler'));
$app->post('/specialty/{specialty_id}', array($studentHandler, 'createOrUpdateSpecialtyHandler'));
$app->post('/specialty/', array($studentHandler, 'createOrUpdateSpecialtyHandler'));
$app->delete('/specialty/{specialty_id}', array($studentHandler, 'deleteSpecialtyHandler'));

$app->run();