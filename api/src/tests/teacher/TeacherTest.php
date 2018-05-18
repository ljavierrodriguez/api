<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class TeacherTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['teacher']);
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

    function testGetAllTeachers(){
        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/teachers/'])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateTeacher
     */
    function testUpdateTeacherIsNotEmpty($teacher){
        $body = [
            "full_name" => "Prof chapatin",
            "avatar_url" => "https://holamundo.com",
            "bio" => "Bio del Prof chapatin"
        ];

        $teacher = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/teacher/'.$teacher->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }
}
?>