<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class TeacherTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['teacher']);
        $this->app->addRoutes(['cohort']);
    }

    function testCreateTeacher(){
        $body = [
            "email" => "teacher@4geeks.com",
            "full_name" => "Prof chapatin",
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $teacher->data;
    }

    /**
     * @depends testCreateTeacher
     */
    function testGetAllTeachers(){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/teachers/'])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testGetTeacherID($teacher){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/teacher/'.$teacher->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testGetTeacherIDIsChrSpecial($teacher){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/teacher/'.$teacher->username])
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testCreateDoubleTeacher(){
        $body = [
            "email" => "teacher@4geeks.com",
            "full_name" => "Prof chapatin",
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testCreateDoubleTeacherEmail(){
        $body = [
            "email" => "teacher@4geeks.com",
            "full_name" => "Prof jirafales"
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testCreateTeacherEmailEmpty(){
        $body = [
            "email" => "",
            "full_name" => "Prof jirafales 2"
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testCreateTeacherFullnameEmpty(){
        $body = [
            "email" => "teacher2@4geeks.com",
            "full_name" => ""
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacher($teacher){
        $body = [
            "full_name" => "Prof chapatin",
            "avatar_url" => "https://holamundo.com",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherFullnameEmpty($teacher){
        $body = [
            "full_name" => "",
            "avatar_url" => "https://holamundo.com",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherAvatarurlEmpty($teacher){
        $body = [
            "full_name" => "Prof chapatin",
            "avatar_url" => "",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherBioEmpty($teacher){
        $body = [
            "full_name" => "Prof chapatin",
            "avatar_url" => "https://holamundo.com",
            "bio" => ""
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherWpidString($teacher){
        $body = [
            "full_name" => "Prof chapatin",
            "avatar_url" => "https://holamundo.com",
            "bio" => "Bio del profesor chapatin",
            "wp_id" => "wpstring"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    function testCreateCohort(){
        $body = [
            "location_slug" => "nueva-location",
            "profile_slug" => "nuevo-profile",
            "name" => "Nuevo Cohort",
            "slug" => "nuevoo-cohort",
            "language" => "es",
            "slack_url" => "http://www.asidj.com",
            "kickoff_date" => "2017-04-10"
        ];
        $cohort = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/cohort/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $cohort->data;
    }

    /**
     * @depends testCreateCohort
     */
    function testGetTeacherCohortID($cohort){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/teachers/cohort/'.$cohort->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testDeleteTeacher($teacher){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/teacher/'.$teacher->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testDeletedTeacher($teacher){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/teacher/'.$teacher->id])
            ->expectFailure()
            ->getParsedBody();
    }
}