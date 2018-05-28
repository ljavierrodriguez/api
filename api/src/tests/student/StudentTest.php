<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class StudentTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['student']);
        $this->app->addRoutes(['location']);
        $this->app->addRoutes(['profile']);
        $this->app->addRoutes(['cohort']);
        $this->app->addRoutes(['badge']);
    }

    function testCreateLocation(){
        $body = [
            "name" => "Caracas - Venezuela",
            "slug" => "nueva-location",
            "address" => "Av Caracas",
            "country" => "venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/location/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();
    }

    function testCreateProfile(){
        $body = [
            "slug" => "nuevo-profile",
            "name" => "Desarrollador",
            "description" => "Web Developer"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();
    }

    function testCreateCohort(){
        $body = [
            "location_slug" => "nueva-location",
            "profile_slug" => "nuevo-profile",
            "name" => "Nuevo Cohort",
            "slug" => "nuevo-cohort",
            "language" => "es",
            "slack_url" => "http://www.asidj.com",
            "kickoff_date" => "2017-04-10"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/cohort/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();
        
        return $cohort->data;
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudent($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeks.com",
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "20"
        ];

        $student = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $student->data;
    }

    function testGetAllStudents(){
        $students = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/students/'])
            ->expectSuccess();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudentEmailChrSpecial($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeks.com!!##",
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "20"
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectSuccess();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudentTotalpointString($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeks.com!!##",
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "prueba de string"
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateDoubleEmailStudent($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeks.com",
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "20"
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudentFullNameEmpty($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeks.com",
            "full_name" => "",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "20"
        ];

        $student = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudentEmailEmpty($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "",
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "20"
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudentEmpty($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "",
            "full_name" => "",
            "avatar_url"=> "",
            "bio" => "",
            "total_points" => ""
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateCohort
     * @depends testCreateStudent
     */
    function testDoubleCreateStudent($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeks.com",
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://holamundo.com",
            "bio" => "webdeveloper",
            "total_points" => "20"
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudent($student){
        $body = [
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e",
            "total_points" => 20,
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentIDIsChrSpecial($student){
        $body = [
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e",
            "total_points" => "20",
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/'.$student->email], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentFullNameEmpty($student){
        $body = [
            "full_name" => "",
            "avatar_url"=> "https://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e",
            "total_points" => "20",
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentAvatarUrlEmpty($student){
        $body = [
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "",
            "total_points" => "20",
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/'.$student->id], $body)
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentBioEmpty($student){
        $body = [
            "full_name" => "Rafael Esaa",
            "avatar_url"=> "https://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e",
            "total_points" => "20"
        ];

        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateStudent
     */
    function testGetStudent($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/'.$student->id])
            ->expectSuccess()
            ->withPropertiesAndValues([
                "full_name" => "Rafael Esaa"
            ]);
    }

    /**
     * @depends testCreateStudent
     */
    function testGetStudentIDIsChrSpecial($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/'.$student->email])
            ->expectSuccess()
            ->withPropertiesAndValues([
                "full_name" => "Rafael Esaa"
            ]);
    }

    /**
     * @depends testCreateStudent
     */
    function updateStudentStatus($student){
        $body = [
            "status" => "currently_active",
            "financial_status" => "fully_paid"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/status/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentIDChrSpecial($student){
        $body = [
            "status" => "currently_active",
            "financial_status" => "fully_paid"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/status/'.$student->email], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentStatusEmpty($student){
        $body = [
            "status" => "",
            "financial_status" => "fully_paid"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/status/'.$student->id], $body)
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentFinancialstatusEmpty($student){
        $body = [
            "status" => "currently_active",
            "financial_status" => ""
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/status/'.$student->id], $body)
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentStatusChrEspecial($student){
        $body = [
            "status" => "!@#!@$",
            "financial_status" => "fully_paid"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/status/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     */
    function testUpdateStudentFinancialstatusChrEspecial($student){
        $body = [
            "status" => "currently_active",
            "financial_status" => "!@#$"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/status/'.$student->id], $body)
            ->expectFailure();
    }








    function testForCreateBadge(){
        $body = [
            "slug" => "identatorr",
            "name" => "Identatior for xxxxxxx",
            "points_to_achieve" => 100,
            "technologies" => "css, html",
            "description" => "wululu"
        ];
        $badge = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)
                ->expectSuccess()
                ->withPropertiesAndValues($body)
                ->getParsedBody();
        
        return $badge->data;
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentActivity($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "nada",
            "description" => "nad descrip",
            "points_earned" => 12
        ];
        $activityStudent = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();

        return $activityStudent->data;
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentTypeChrSpecial($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project!@#",
            "name" => "nada",
            "description" => "nad descrip",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }
    
    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentNameChrSpecial($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "nada!@#",
            "description" => "nad descrip",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentDescriptionChrSpecial($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "nada",
            "description" => "nada descrip !@#",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentPointsearnedChrSpecial($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "nada",
            "description" => "nada descrip",
            "points_earned" => "!@#"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentBadgeslugEmpty($student, $badge){
        $body = [
            "badge_slug" => "",
            "type" => "project",
            "name" => "nada",
            "description" => "nada descrip",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentTypeEmpty($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "",
            "name" => "nada",
            "description" => "nada descrip",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentNameEmpty($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "",
            "description" => "nada descrip",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentDescriptionEmpty($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "nada",
            "description" => "",
            "points_earned" => 12
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     * @depends testForCreateBadge
     */
    function testCreateStudentPointsearnedEmpty($student, $badge){
        $body = [
            "badge_slug" => $badge->slug,
            "type" => "project",
            "name" => "nada",
            "description" => "nada descrip",
            "points_earned" => ""
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/activity/student/'.$student->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     */
    function testGetBriefing($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/briefing/student/'.$student->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudent
     */
    function testGetBriefingIDIsChrSpecial($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/briefing/student/'.$student->email])
            ->expectFailure();
    }

    /**
     * @depends testCreateStudent
     */
    function testGetActivityStudent($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/activity/student/'.$student->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudent
     */
    function testGetActivityStudentChrSpecial($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/activity/student/'.$student->email])
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudentActivity
     */
    function testDeleteActivityStudent($activityStudent){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/activity/'.$activityStudent->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudentActivity
     */
    function testDeletedActivityStudent($activityStudent){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/activity/'.$activityStudent->id])
            ->expectFailure();
    }






    /**
     * @depends testCreateStudent
     */
    /*function testDeleteStudent($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/student/'.$student->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateStudent
     */
    /*function testDeletedStudent($student){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/student/'.$student->id])
            ->expectFailure();
    }*/
}
?>