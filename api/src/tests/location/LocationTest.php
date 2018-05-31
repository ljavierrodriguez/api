<?php
namespace Tests;

class LocationTest extends BaseTestCase
{
    protected $app;
    
    public function setUp(){
        
        parent::setUp();
        $this->app->addRoutes(['location']);
        
    }
    
    public function testCreateLocation() {
        $body = [
            "name" => "Caracas",
            "slug" => "slug-caracas",
            "address" => "Caracas",
            "country" => "venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'PUT','REQUEST_URI' => '/location/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();

        return $location->data;
    }

    /**
     * @depends testCreateLocation
     */
    public function testGetLocations($location){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/location/'.$location->id])
            ->expectSuccess();
    }

    public function testCreateLocationSlugCharacterSpecials() {
        $body = [
            "name" => "Caracas",
            "slug" => "slug-caracas!@#",
            "address" => "Caracas",
            "country" => "venezuela"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT','REQUEST_URI' => '/location/'], $body)
            ->expectFailure();
    }

    public function testCreateLocationEmptyName() {
        $body = [
            "name" => "",
            "slug" => "slug-caracass",
            "address" => "Caracas",
            "country" => "venezuela"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT','REQUEST_URI' => '/location/'], $body)
            ->expectFailure();
    }

    public function testCreateLocationEmptyAddress() {
        $body = [
            "name" => "Caracas",
            "slug" => "slug-caracasss",
            "address" => "",
            "country" => "venezuela"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT','REQUEST_URI' => '/location/'], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    public function testCreateLocationEmptySlug() {
        $body = [
            "name" => "Caracas",
            "slug" => "",
            "address" => "Caracas",
            "country" => "venezuela"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT','REQUEST_URI' => '/location/'], $body)
            ->expectFailure();
    }

    public function testEmptyLocation() {
        $body = [
            "name" => "",
            "slug" => "",
            "address" => "",
            "country" => ""
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'PUT','REQUEST_URI' => '/location/'], $body)
            ->expectFailure();
    }

    /**
     * @depends testCreateLocation
     */
    public function testUpdateLocation($location){
        $body = [
            "name" => "update",
            "slug" => "update",
            "address" => "Caracas2",
            "country" => "venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/location/'.$location->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();
    }

    /**
     * @depends testCreateLocation
     */
    public function testUpdateLocationSlugCharacterSpecials($location){
        $body = [
            "name" => "update",
            "slug" => "update!@#",
            "address" => "Caracas2",
            "country" => "venezuela"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/location/'.$location->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body);
    }

    /**
     * @depends testCreateLocation
     */
    public function testUpdateLocationEmptySlug($location){
        $body = [
            "name" => "update",
            "slug" => "",
            "address" => "Caracas2",
            "country" => "venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/location/'.$location->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();
    }

    /**
     * @depends testCreateLocation
     */
    public function testUpdateLocationEmptyName($location){
        $body = [
            "name" => "",
            "slug" => "update",
            "address" => "Caracas2",
            "country" => "venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/location/'.$location->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues([
                "name" => $body["name"],
                "slug" => $body["slug"],
                "address" => $body["address"],
                "country" => $body["country"]
            ])
            ->getParsedBody();
    }

    /**
     * @depends testCreateLocation
     */
    public function testUpdateLocationEmptyAddress($location){
        $body = [
            "name" => "update",
            "slug" => "update",
            "address" => "",
            "country" => "venezuela"
        ];
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'POST','REQUEST_URI' => '/location/'.$location->id], $body)
            ->expectSuccess()
            ->withPropertiesAndValues($body)
            ->getParsedBody();
    }

    /**
     * @depends testCreateLocation
     */
    public function testDeleteLocation($location){
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE','REQUEST_URI' => '/location/'.$location->id])
            ->expectSuccess()
            ->getParsedBody();
    }

    public function testGetAllLocations(){
        $location = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/locations/'])
            ->expectSuccess()
            ->getParsedBody();
    }
}