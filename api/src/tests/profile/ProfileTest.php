<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class ProfileTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['profile']);
        $this->app->addRoutes(['specialty']);

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
                "rtf-master"
            ]
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'PUT', 'REQUEST_URI' => '/profile/'], $body)
            ->expectSuccess()
            ->getParsedBody();

        return $profile->data;
    }

    // -------- Update Specialty --------
    function testUpdateSpecialty(){
        $body = [
            "name" => "update specialty",
            "profile_slug" => "web-developer",
            "slug" => "rtf-master"
        ];
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/1'], $body)
            ->expectSuccess();
    }

    function testUpdateSpecialtyNameEmpty(){
        $body = [
            "name" => "",
            "profile_slug" => "web-developer",
            "slug" => "rtf-master"
        ];
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/1'], $body)
            ->expectFailure();
    }

    function testUpdateSpecialtySlugEmpty(){
        $body = [
            "name" => "update specialty",
            "profile_slug" => "web-developer",
            "slug" => ""
        ];
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/1'], $body)
            ->expectFailure();
    }

    // -------- Get Specialty from Profile ---------

    function testGetSpecialtyProfile(){
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/specialties/profile/2'])
            ->expectSuccess()
            ->getParsedBody();
        $this->assertNotEmpty($specialty);
    }

    function testGetSpecialtyIDIsChrSpecial(){
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/specialties/profile/!@#'])
            ->expectFailure()
            ->getParsedBody();
    }

    // -------- Post Specialty from Profile ---------

    function testUpdateSpecialtyProfile(){
        $body = [
            "profile_slug" => "web-developer",
            "slug" => "specialty",
            "name" => "specialty name"
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/1'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    // ------ Specialty for profile

    function testUpdateSpecialtyProfiles(){
        $body = [
            "specialties" => ["1", "2"]
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/profile/2'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    function testUpdateSpecialtyProfilesEmpty(){
        $body = [
            "specialties" => []
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/specialty/profile/2'], $body)
            ->expectFailure()
            ->getParsedBody();
    }

    function testDeleteSpecialtyProfile(){
        $body = [
            "specialties" => ["1", "2"]
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/specialty/profile/2'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    /**
     * @depends testDeleteSpecialtyProfile
     */
    function testDeletedSpecialtyProfile(){
        $body = [
            "specialties" => ["1", "2"]
        ];
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/specialty/profile/2'], $body)
            ->expectSuccess()
            ->getParsedBody();
    }

    function testDeleteSpecialty(){
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/specialty/2'])
            ->expectSuccess()
            ->getParsedBody();
    }

    function testDeletedSpecialty(){
        $profile = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/specialty/2'])
            ->expectFailure()
            ->getParsedBody();
    }

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