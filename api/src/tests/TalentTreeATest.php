<?php
namespace Tests;

class TalentTreeATest extends BaseTestCase
{
    protected $app;
    
    //$this->assertSame(x, y);
    //$this->assertTrue(x);
    //$this->assertFalse(y);
    public function setUp(){
        
        parent::setUp();
        $this->app->addRoutes(['badge']);
        
    }
    
    /*public function testForAddBadge(){
        $body = [
              "slug" => "identator",
              "name" => "Identatior for oscar",
              "points_to_achieve" => 100,
              "technologies" => "css, html",
              "description" => "wululu"
        ];
        $response = $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'], $body)
                        ->expectSuccess()
                        ->getParsedBody();
        return $response->data;
    }*/
    
    /**
     * @depends testForAddBadge
     */
    public function testForUpdate($badge){
        $this->assertTrue(isset($badge->slug)); //needs to be an array
        $body = [
              "name" => "Tag master for oscarcito",
              "points_to_achieve" => 99,
              "technologies" => "css",
              "description" => "welele"
        ];
        $this->mockAPICall(['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/badge/'.$badge->id], $body)
                        ->expectSuccess()
                        ->withPropertiesAndValues([
                                "name" => $body["name"],
                                "points_to_achieve" => $body["points_to_achieve"],
                                "technologies" => $body["technologies"],
                                "description" => $body["description"],
                            ]);
    }
    
}