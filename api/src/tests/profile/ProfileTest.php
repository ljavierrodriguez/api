<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class ProfileTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['profile']);

    }

    function testGetAllProfiles(){
        $profiles = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/profiles/'])
            ->expectSuccess();
    }

    function testForCreateProfile(){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS",
            "specialties"=>[
                "Hola", "Mundo"
            ]
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $profile->data;
    }

    /**
     * @depends testForCreateProfile
     */
    /*function testForCreateDoubleProfile(){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }*/

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileID($profile){
        $body = [
            "slug"=> "web-developer",
            "name"=> "HOLAAAAAA",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileIDIsChrSpecial($profile){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->description], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileNameEmpty($profile){
        $body = [
            "slug"=> "web-developer",
            "name"=> "",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileSlugEmpty($profile){
        $body = [
            "slug"=> "",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileDescriptionEmpty($profile){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> ""
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileSlugChrEspecial($profile){
        $body = [
            "slug"=> "web-developer!@#",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileNameChrEspecial($profile){
        $body = [
            "slug"=> "web-developer",
            "name"=> "!@#",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testUpdateProfileDescriptionChrEspecial($profile){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> "!@#"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/profile/'.$profile->id], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testGetProfileID($profile){
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/profile/'.$profile->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testGetProfileIDIsChrSpecial($profile){
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/profile/'.$profile->description])
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateProfileSlugCharacterSpecials(){
        $body = [
            "slug"=> "web-developer!@#",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateProfileEmptyName(){
        $body = [
            "slug"=> "web-developer",
            "name"=> "",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testCreateProfileEmptySlug(){
        $body = [
            "slug"=> "",
            "name"=> "Web Developer",
            "description"=> "Create websites using a CMS"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateProfileEmptySlug
     */
    function testCreateProfileEmptyDescription(){
        $body = [
            "slug"=> "web-developer",
            "name"=> "Web Developer",
            "description"=> ""
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    /**
     * @depends testCreateProfileEmptyDescription
     */
    function testEmptyProfile(){
        $body = [
            "slug"=> "",
            "name"=> "",
            "description"=> ""
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectFailure()
            ->getParsedBody();
    }    

    /**
     * @depends testForCreateProfile
     */
    function testDeleteProfile($profile){
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/profile/'.$profile->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testForCreateProfile
     */
    function testDeletedProfile($profile){
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/profile/'.$profile->id])
            ->expectFailure()
            ->getParsedBody();
    }
    
}
?>