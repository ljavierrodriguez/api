<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class UsersTest extends BaseTestCase {

    /*protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['user']);

    }

    function testCreateUser(){
        $body = [
            "full_name" => "Rafael Esaá",
            "type" => "student",
            "username" => "resaa@4geeks.com",
            "password" => "1234567"
        ];
        $user = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
                ->expectSuccess()
                ->getParsedBody();

        return $user->data;
    }

    /**
     * @depends testCreateUser
     */
    /*function testGetAllUsers(){
        $users = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/users/'])
            ->expectSuccess()
            ->getParsedBody();
    }

    /*function testCreateDoubleUser(){
        $body = [
            "type" => "student",
            "full_name" => "Rafael Esaa",
            "username" => "resaa@4geeks.com"
        ];
        $user = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/user/'], $body)
                ->expectFailure()
                ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testGetUserID($user){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/user/'.$user->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testGetUserIDEmail($user){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/user/'.$user->username])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testGetUserIDIsChrSpecial($user){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/user/'.$user->full_name])
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testUpdateUserID($user){
        $body = [
            "full_name" => "Antonio"
        ];
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testUpdateUserIDIsChrSpecial($user){
        $body = [
            "full_name" => "Antonio"
        ];
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->full_name], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testUpdateUserFullnameEmpty($user){
        $body = [
            "full_name" => ""
        ];
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testUpdateUserTypeEmpty($user){
        $body = [
            "type" => ""
        ];
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testUpdatedUserUsername($user){
        $body = [
            "username" => ""
        ];
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testDeleteUser($user){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE','REQUEST_URI' => '/user/'.$user->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testDeletedUser($user){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE','REQUEST_URI' => '/user/'.$user->id])
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateUser
     */
    /*function testGetSettingUserIDIsChrSpecial($user){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/settings/user/'.$user->full_name])
            ->expectFailure()
            ->getParsedBody();
    }*/

    /**
     * @depends testCreateUser
     */
    /*function testUpdatePassword($user){
        $body=[
            "password" => "123456",
            "repeat" => "123456",
            "token" => "fa70fb538fec6e67f651b027635da84015e16857"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/user/'.$user->id.'/password'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }*/
}
?>