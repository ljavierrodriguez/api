<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class CohortTest extends BaseTestCase {
 
    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['cohort', 'location', 'profile', 'teacher', 'student']);
    }

    function testCreateLocation(){
        $body = [
            "name" => "Caracas - Venezuela",
            "slug" => "nuevalocation-cohort",
            "address" => "Av Caracas",
            "country" => "Venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/location/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $location->data;
    }

    /**
     * @depends testCreateLocation
     */
    function testCreateProfile(){
        $body = [
            "slug" => "nuevoprofile-cohort",
            "name" => "Desarrollador",
            "description" => "Web Developer".
            "specialties"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateProfile
     */
    function testCreateCohort(){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Nuevo Cohort",
            "slug" => "nuevocohort-cohort",
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

    /*function testCreateDoubleCohort(){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Nuevo Cohort",
            "slug" => "nuevocohort-cohort",
            "language" => "es",
            "slack_url" => "http://www.asidj.com",
            "kickoff_date" => "2017-04-10"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/cohort/'], $body)
            ->expectFailure();
    }*/

    function testCreateTeacher(){
        $body = [
            "email" => "teacherCohort@4geeks.com",
            "full_name" => "Prof Cohort",
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $teacher->data;
    }

    /**
     * @depends testCreateCohort
     */
    function testGetCohortID($cohort){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cohort/'.$cohort->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateCohort
     */
    function testGetCohortIDChcSpecial($cohort){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cohort/'.$cohort->name])
            ->expectFailure();
    }

    /**
     * @depends testCreateCohort
     */
    function testCreateStudent($cohort){
        $body = [
            "cohort_slug" => $cohort->slug,
            "email" => "resaaa@4geeksss.com",
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

    /**
     * @depends testCreateCohort
     * @depends testCreateStudent
     */
    /*function testCreateStudentCohort($cohort, $student){
        $body = [
            "student_id" => 1
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/cohort/'.$cohort->id], $body)
            ->expectSuccess();
    }*/

    /**
     * @depends testCreateCohort
     */
    /*function testGetStudentCohortID($cohort){
        $students = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/students/cohort/'.$cohort->id])
            ->expectSuccess()
            ->getParsedBody();
            $this->assertNotEmpty($students);
    }*/

    function testCreateCohortLanguageChrSpecial(){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Nuevo Cohort",
            "slug" => "nuevocohort-cohort",
            "language" => "es!@#",
            "slack_url" => "http://www.asidj.com",
            "kickoff_date" => "2017-04-10"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/cohort/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateCohortSlugChrSpecial(){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Nuevo Cohort",
            "slug" => "nuevocohort-cohort!@#",
            "language" => "es",
            "slack_url" => "http://www.asidj.com",
            "kickoff_date" => "2017-04-10"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/cohort/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateCohortDateChrSpecial(){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Nuevo Cohort",
            "slug" => "nuevocohort-cohort",
            "language" => "es",
            "slack_url" => "http://www.asidj.com",
            "kickoff_date" => "2017-04-10!@#"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/cohort/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateCohort
     */
    function testGetAllCohorts($cohort){
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cohorts/'])
            ->expectSuccess()
            ->getParsedBody();
            $this->assertNotEmpty($cohort);
    }

    /**
     * @depends testCreateCohort
     */
    function testUpdateCohort($cohort){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Update Cohort",
            "slug" => "updatecohort-cohort",
            "language" => "es",
            "slack_url" => "http://www.holamundo.com",
            "kickoff_date" => "2017-04-10"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/cohort/'.$cohort->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateCohort
     */
    function testUpdateSlugChrSpecial($cohort){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "Update Cohort",
            "slug" => "updatecohort-cohort!@#",
            "language" => "es",
            "slack_url" => "http://www.holamundo.com",
            "kickoff_date" => "2017-04-10"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/cohort/'.$cohort->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateCohort
     */
    function testUpdateCohortIdEmpty($cohort){
        $body = [
            "location_slug" => "nuevalocation-cohort",
            "profile_slug" => "nuevoprofile-cohort",
            "name" => "",
            "slug" => "",
            "language" => "",
            "slack_url" => "",
            "kickoff_date" => ""
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/cohort/'.$cohort->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateLocation
     */
    function testGetCohortLocationID($location){
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cohorts/location/'.$location->id])
            ->expectSuccess()
            ->getParsedBody();
            $this->assertNotEmpty($cohort);
    }

    /**
     * @depends testCreateTeacher
     */
    function testGetCohortTeacherID($teacher){
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cohorts/teacher/'.$teacher->id])
            ->expectSuccess()
            ->getParsedBody();
            $this->assertNotEmpty($cohort);
    }

    /**
     * @depends testCreateCohort
     * @depends testCreateTeacher
     */
    /*function testCreateTeacherCohort($cohort, $teacher){
        $body = [
            "teacher_id" => 1,
            "is_instructor" => true
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/cohort/'.$cohort->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }*/

    /**
     * @depends testCreateCohort
     */
    function testDeleteCohort($cohort){
        
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/cohort/'.$cohort->id])
            ->expectFailure();

        //$this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/cohort/'.$cohortToDelete->id])->expectFailure();
    }

}