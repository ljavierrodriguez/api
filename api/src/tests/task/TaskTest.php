<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class TaskTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['task']);
        $this->app->addRoutes(['student']);
    }

    function testCreateTaskToStudent(){
        $body = [
            "associated_slug" => "done",
            "type" => "quiz",
            "title" => "task",
            "description" => "description task"
        ];
        $task = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/2/task/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();

        return $task->data;
    }

    function testCreateTaskToStudentTypeEmpty(){
        $body = [
            "associated_slug" => "done",
            "type" => "",
            "title" => "task",
            "description" => "description task"
        ];
        $task = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/2/task/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateTaskToStudentTitleEmpty(){
        $body = [
            "associated_slug" => "done",
            "type" => "quiz",
            "title" => "",
            "description" => "description task"
        ];
        $task = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/2/task/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateTaskToStudentDescriptionEmpty(){
        $body = [
            "associated_slug" => "done",
            "type" => "quiz",
            "title" => "task",
            "description" => ""
        ];
        $task = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/2/task/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateTaskToStudentSlugtionEmpty(){
        $body = [
            "associated_slug" => "",
            "type" => "quiz",
            "title" => "task",
            "description" => "description task"
        ];
        $task = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/2/task/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateTaskToStudentEmpty(){
        $body = [
            "associated_slug" => "",
            "type" => "",
            "title" => "",
            "description" => ""
        ];
        $task = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/student/2/task/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testGetTaskStudentID(){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/2/task/'])
            ->expectSuccess();
    }

    /**
     * @depends testCreateTaskToStudent
     */
    function testCreateUrlgitTask($task){
        $body =[
            "type" => "quiz",
            "status" => "done",
            "github_url" => null
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/task/'.$task->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateTaskToStudent
     */
    function testCreateUrlgitTaskTypeEmpty($task){
        $body =[
            "type" => "",
            "status" => "prueba",
            "github_url" => "https://github.com/RafaelEsaa"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/task/'.$task->id], $body)
            ->expectFailure();
    }

    function testGetStudentTask(){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/2/task/'])
            ->expectSuccess();
    }

    function testGetStudentTaskEmail(){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/student/resaa@geeks.com/task/'])
            ->expectFailure();
    }

    /**
     * @depends testCreateTaskToStudent
     */
    function testDeleteStudentTask($task){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/task/'.$task->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateTaskToStudent
     */
    function testDeletedStudentTask($task){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/task/'.$task->id])
            ->expectFailure();
    }
}