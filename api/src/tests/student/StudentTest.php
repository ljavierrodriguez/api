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
    }

    function testGetAllStudents(){
        $students = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/students/'])
            ->expectSuccess();
    }

    function testCreateLocation(){
        $body = [
            "name" => "Caracas - Venezuela",
            "slug" => "nueva-location",
            "address" => "Av Caracas",
            "country" => "Venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/location/'], $body)
            ->expectSuccess()
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
            ->getParsedBody();
    }

    function testCreateStudent(){
        $body = [
            "cohort_slug" => "nuevo-cohort",
            "email" => "resaa@4geeks.co",
            "full_name" => "Rafael Esaa"
        ];
        $student = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/student/'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }
}
?>