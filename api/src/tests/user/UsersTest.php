<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class UsersTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['user']);

    }

    function testCreateUser(){
        $body = [
            "type" => "student",
            "full_name" => "Rafael Esaá",
            "first_name" => "Rafael",
            "last_name" => "Esaá",
            "parent_location_id" => 1,
            "username" => "resaa@4geeks.com"
        ];
        $user = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
                ->expectSuccess()
                ->withPropertiesAndValues($body)
                ->getParsedBody();

        return $user->data;
    }

    /**
     * @depends testCreateUser
     */
    function testGetAllUsers(){
        $users = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/users/'])
            ->expectSuccess()
            ->getParsedBody();
    }

    function testCreateDoubleUser(){
        $body = [
            "type" => "student",
            "full_name" => "Rafael Esaa",
            "username" => "resaa@4geeks.com"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
                ->expectFailure();
    }

    /**
     * @depends testCreateUser
     */
    function testGetUserID($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/user/'.$user->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateUser
     */
    function testGetUserIDEmail($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/user/'.$user->username])
            ->expectSuccess();
    }

    /**
     * @depends testCreateUser
     */
    function testGetUserIDIsChrSpecial($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/user/'.$user->full_name])
            ->expectFailure();
    }

    /**
     * @depends testCreateUser
     */
    function testUpdateUserID($user){
        $body = [
            "full_name" => "Antonio Aparicio",
            "first_name" => "Antonio",
            "last_name" => "Aparicio",
            "parent_location_id" => 1
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateUser
     */
    function testUpdateUserIDIsChrSpecial($user){
        $body = [
            "full_name" => "Antonio"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->full_name], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateUser
     */
    function testUpdateUserFullnameEmpty($user){
        $body = [
            "full_name" => ""
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateUser
     */
    function testUpdateUserTypeEmpty($user){
        $body = [
            "type" => "career-support"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateUser
     */
    function testUpdatedUserUsername($user){
        $body = [
            "username" => ""
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectSuccess();
    }

    /**
     * @depends testCreateUser
     */
    function testGetSettingUserIDIsChrSpecial($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/settings/user/'.$user->full_name])
            ->expectFailure();
    }

    /**
     * @depends testCreateUser
     */
    function testGetSettingUserID($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/settings/user/'.$user->id])
            ->expectSuccess();
    }

    function testCreateCredentialUser(){
        $body = [
            "full_name" => "Rafael Esaa",
            "email" => "resaa@4geekss.com",
            "type" => "student"
        ];
        $credential = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/credentials/user/'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    function testUpdateCredentialUser($user){
        $body = [
            "password" => "123456"
        ];
        $credential = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/credentials/user/'.$user->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    function testUpdateCredentialUserChrSpecial($user){
        $body = [
            "password" => "123456"
        ];
        $credential = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/credentials/user/'.$user->username], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    function testDeleteUser($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE','REQUEST_URI' => '/user/'.$user->id])
            ->expectSuccess();
    }

    /**
     * @depends testCreateUser
     */
    function testDeletedUser($user){
        $this->mockAPICall(['REQUEST_METHOD' => 'DELETE','REQUEST_URI' => '/user/'.$user->id])
            ->expectFailure();
    }
}