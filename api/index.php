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
$storage = new ExtendedPDO(array('dsn' => 'mysql:host=localhost;dbname='.DATABASE_NAME, 'username' => DATABASE_USERNAME, 'password' => DATABASE_PASSWORD));
$app->storage = $storage;

$server = new OAuth2\Server($storage,array(
    'access_lifetime' => 186400
));

//Enable Authorization Code credentials to allow request from authorization code.
$server->addGrantType(new GrantType\AuthorizationCode($storage));
//Enable ClientCredentials to allo clients to generate an authorization code.
$server->addGrantType(new GrantType\ClientCredentials($storage));
//Enable user login form
$server->addGrantType(new GrantType\UserCredentials($storage));

$getUsernameMiddleware = function ($request, $response, $next) {
    
    $body = $request->getParsedBody();

    if(isset($body['username'])) 
    {
        $user = User::where('username', $body['username'])->first();
        //print_r($body); die();
        if(!$user) throw new Exception('There is now user corresponding to these credentials in the platform: '.$body['username']);
    }
    
    $response = $next($request, $response);//do the next middleware layer action

    return $response;
};

$v = function($candidates)
{
    foreach($candidates as $s)
        if(!in_array($s, GLOBAL_CONFIG['scopes'])) 
            throw new Exception('Invalid scope type: '.$s);
            
    return $candidates;
    
};

//The HTML views for the OAuth Autentication process
$renderer = new Views\PhpRenderer( __DIR__ . '/vendor/chadicus/slim-oauth2-routes/templates');
$app->map(['GET', 'POST'], Routes\Authorize::ROUTE, new Routes\Authorize($server, $renderer))->setName('authorize');
$app->post(Routes\Token::ROUTE, new Routes\Token($server))->setName('token')->add($getUsernameMiddleware);
$app->map(['GET', 'POST'], Routes\ReceiveCode::ROUTE, new Routes\ReceiveCode($renderer))->setName('receive-code');
//Creating the Middleware to intercept all request and ask for authorization before continuing
$authorization = new Middleware\Authorization($server, $app->getContainer());


/**
 * Static Data that will not be managed
 **/
$catalogHandler = new CatalogHandler($app);
$app->get('/technologies/', array($catalogHandler, 'getAllTechnologies'));
$app->get('/countries/', array($catalogHandler, 'getAllCountries'));






/**
 * Main basic stuff
 **/
$mainHandler = new MainHandler($app);
$app->post('/sync/', array($mainHandler, 'syncMainData'))->add($authorization->withRequiredScope($v(['sync_data'])));






/**
 * Every course is meant to trian students in one specific profile, for example: Full Stack Web Developer
 **/
$profileHandler = new ProfileHandler($app);
$app->get('/profiles/', array($profileHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['read_talent_tree'])));
$app->get('/profile/{profile_id}', array($profileHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));


/**
 * Everything Related to the user
 **/
$userHandler = new UserHandler($app);
$app->get('/me', array($userHandler, 'getMe'))->add($authorization);
$app->post('/credentials/user/', array($userHandler, 'createCredentialsHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/user/{user_id}', array($userHandler, 'deleteUser'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/user/sync', array($userHandler, 'syncUserHandler'))->add($authorization->withRequiredScope($v(['sync_data'])));


/**
 * Everything Related to the locations
 **/
$locationHandler = new LocationHandler($app);
$app->get('/locations/', array($locationHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['read_basic_info'])));
$app->get('/location/{location_id}', array($locationHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['read_basic_info'])));

$app->post('/location/', array($locationHandler, 'createLocationHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/location/{location_id}', array($locationHandler, 'updateLocationHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/location/{location_id}', array($locationHandler, 'deleteLocationHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/location/sync/', array($locationHandler, 'syncLocationHandler'))->add($authorization->withRequiredScope($v(['sync_data'])));
 


/**
 * Everything Related to the cohorts
 **/
$cohortHandler = new CohortHandler($app);
$app->get('/cohorts/', array($cohortHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['read_basic_info'])));
$app->get('/cohorts/location/{location_id}', array($cohortHandler, 'getAllCohortsFromLocationHandler'))->add($authorization->withRequiredScope($v(['read_basic_info'])));
$app->get('/cohorts/teacher/{teacher_id}', array($cohortHandler, 'getAllCohortsFromTeacherHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/cohort/{cohort_id}', array($cohortHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['read_basic_info'])));
$app->get('/students/cohort/{cohort_id}', array($cohortHandler, 'getCohortStudentsHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/student/cohort/{cohort_id}', array($cohortHandler, 'addStudentToCohortHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/cohort/', array($cohortHandler, 'createCohortHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/cohort/{cohort_id}', array($cohortHandler, 'updateCohortHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/cohort/{cohort_id}', array($cohortHandler, 'deleteCohortHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/teacher/cohort/{cohort_id}', array($cohortHandler, 'addTeacherToCohortHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/teacher/cohort/{cohort_id}', array($cohortHandler, 'deleteTeacherFromCohortHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/cohort/sync/', array($cohortHandler, 'syncCohortHandler'))->add($authorization->withRequiredScope($v(['sync_data'])));
/**
 * Everything Related to the student itself
 **/
$teacherHandler = new TeacherHandler($app);
$app->get('/teachers/', array($teacherHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/teacher/{teacher_id}', array($teacherHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/teachers/cohort/{cohort_id}', array($teacherHandler, 'getCohortTeachers'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/teacher/', array($teacherHandler, 'createTeacherHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/teacher/{teacher_id}', array($teacherHandler, 'updateTeacherHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/teacher/{teacher_id}', array($teacherHandler, 'deleteTeacherHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));







/**
 * Everything Related to the student itself
 **/
$studentHandler = new StudentHandler($app);
$app->get('/students/', array($studentHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/student/{student_id}', array($studentHandler, 'getStudentHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/student/', array($studentHandler, 'createStudentHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/student/{student_id}', array($studentHandler, 'updateStudentHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/student/{student_id}', array($studentHandler, 'deleteStudentHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->get('/briefing/student/{student_id}', array($studentHandler, 'getStudentBriefing'))->add($authorization->withRequiredScope($v(['super_admin'])));




/**
 * Assignments and AssignmentTemplate
 **/
$atemplateHandler = new AtemplateHandler($app);
$app->get('/atemplates/', array($atemplateHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['read_basic_info'])));
$app->get('/atemplate/{atemplate_id}', array($atemplateHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/atemplate/sync/{wp_id}', array($atemplateHandler, 'syncFromWPHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/atemplate/', array($atemplateHandler, 'createHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/atemplate/{atemplate_id}', array($atemplateHandler, 'updateHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/atemplate/{atemplate_id}', array($atemplateHandler, 'deleteHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));







$assignmentHandler = new AssignmentHandler($app);
$app->get('/student/assignments/', array($assignmentHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/student/assignment/{assignment_id}', array($assignmentHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->get('/assignments/student/{student_id}', array($assignmentHandler, 'getAllStudentAssignmentsHandler'))->add($authorization->withRequiredScope($v(['student_assignments'])));
$app->get('/assignments/teacher/{teacher_id}', array($assignmentHandler, 'getAllTeacherAssignmentsHandler'))->add($authorization->withRequiredScope($v(['teacher_assignments'])));

$app->post('/student/assignment/', array($assignmentHandler, 'createAssignmentHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/student/assignment/{assignment_id}', array($assignmentHandler, 'updateAssignmentHandler'))->add($authorization->withRequiredScope($v(['student_assignments'])));
$app->post('/assignment/cohort/{cohort_id}', array($assignmentHandler, 'createCohortAssignmentHandler'))->add($authorization->withRequiredScope($v($v(['teacher_assignments']))));
$app->delete('/student/assignment/{assignment_id}', array($assignmentHandler, 'deleteAssignmentHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->post('/assignment/sync/', array($assignmentHandler, 'syncFromWPHandler'))->add($authorization->withRequiredScope($v(['sync_data'])));



/**
 * Everything Related to the student badges
 **/
$badgeHandler = new BadgeHandler($app);
$app->get('/badges/', array($badgeHandler, 'getAllHandler'));
$app->get('/badge/{badge_id}', array($badgeHandler, 'getSingleBadge'));

$app->get('/badges/student/{student_id}', array($badgeHandler, 'getAllStudentBadgesHandler'))->add($authorization->withRequiredScope($v(['read_talent_tree'])));
$app->post('/badge/', array($badgeHandler, 'createOrUpdateBadgeHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/badge/{badge_id}', array($badgeHandler, 'createOrUpdateBadgeHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/badge/{badge_id}', array($badgeHandler, 'deleteBadgeHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/badge/image/{badge_id}', array($badgeHandler, 'updateThumbHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));








/**
 * Everything Related to the student activities
 **/
$app->post('/activity/student/{student_id}', array($studentHandler, 'createStudentActivityHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/activity/student/{student_id}', array($studentHandler, 'getStudentActivityHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/activity/{activity_id}', array($studentHandler, 'deleteStudentActivityHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));








/**
 * Everything Related to the student specialties
 **/
$specialtyHandler = new SpecialtyHandler($app);
$app->get('/specialty/{specialty_id}', array($specialtyHandler, 'getSingleHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->get('/specialties/', array($specialtyHandler, 'getAllHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));

$app->get('/specialties/profile/{profile_id}', array($specialtyHandler, 'getProfileSpecialtiesHandler'))->add($authorization->withRequiredScope($v(['read_talent_tree'])));
$app->get('/specialties/student/{student_id}', array($specialtyHandler, 'getStudentSpecialtiesHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/specialty/{specialty_id}', array($specialtyHandler, 'updateSpecialtyHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->post('/specialty/', array($specialtyHandler, 'createSpecialtyHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));
$app->delete('/specialty/{specialty_id}', array($specialtyHandler, 'deleteSpecialtyHandler'))->add($authorization->withRequiredScope($v(['super_admin'])));



/**
 * Runing the app alfter all configuration
 **/
$app->run();
