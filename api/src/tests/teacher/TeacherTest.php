<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Location;
use Profile;
use Teacher;

class TeacherTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['teacher']);
        $this->app->addRoutes(['cohort']);
        $this->app->addRoutes(['user']);
    }

    function testCreateTeacher(){
        $body = [
            "username" => "teacher@4geeks.com",
            "type" => "teacher",
            "first_name" => "Prof",
            "last_name" => "Chapatin",
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
            ->expectSuccess()
            ->getParsedBody();
            
        // $found = Teacher::find($teacher->data->id);
        // $this->assertNotEmpty($found);

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
            "username" => "teacher@4geeks.com",
            "type" => "teacher",
            "first_name" => "Prof",
            "last_name" => "Chapatin",
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testCreateTeacherEmailEmpty(){
        $body = [
            "username" => "",
            "teacher" => "teacher",
            "first_name" => "Prof",
            "last_name" => "Jirafales",
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testCreateTeacherFullnameEmpty(){
        $body = [
            "username" => "teacher2@4geeks.com",
            "first_name" => "",
            "last_name" => "",
            "type" => "teacher"
        ];
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacher($teacher){
        $body = [
            "first_name" => "Prof",
            "last_name" => "Chapatin",
            "avatar_url" => "https://holamundo.com",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/user/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherFullnameEmpty($teacher){
        $body = [
            "first_name" => "",
            "last_name" => "",
            "avatar_url" => "https://holamundo.com",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/user/'.$teacher->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherAvatarurlEmpty($teacher){
        $body = [
            "first_name" => "Prof",
            "last_name" => "Chapatin",
            "avatar_url" => "",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/user/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherBioEmpty($teacher){
        $body = [
            "first_name" => "Prof",
            "last_name" => "Chapatin",
            "avatar_url" => "https://holamundo.com",
            "bio" => ""
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/user/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }


    function testCreateCohort(){
        $l = new Location();
        $l->slug = 'the-super-location';
        $l->name = 'The Super location';
        $l->country = 'United States';
        $l->address = '363 Aragon';
        $l->save();
        $l = new Profile();
        $l->slug = 'nuevo-profile';
        $l->name = 'Full Stack FT';
        $l->description = 'Full Stack Full-Time';
        $l->save();
        
        $body = [
            "location_slug" => "the-super-location",
            "profile_slug" => "nuevo-profile",
            "name" => "Nuevo Cohort 2",
            "slug" => "nuevoo-cohor2t",
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
     * @depends testCreateCohort
     */
    function testAddTeacherToCohort($teacher, $cohort){
        $body = [
            [ "is_instructor" => "true", "teacher_id" => $teacher->id ]
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/cohort/'.$cohort->id], $body)
            ->expectSuccess()
            ->getParsedBody();

        $data = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/teacher/'.$teacher->id])->expectSuccess()->getParsedBody();
        $this->assertTrue(in_array($cohort->slug, $data->data->cohorts));
            
    }

    /**
     * @depends testCreateTeacher
     */
    function testDeleteTeacher($teacher){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/user/'.$teacher->id])
            ->expectSuccess()
            ->getParsedBody();
    }

}