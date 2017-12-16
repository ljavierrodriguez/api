<?php
namespace Tests;

class BadgeTest extends BaseTestCase
{
    protected $app;
    
    //$this->assertSame(x, y);
    //$this->assertTrue(x);
    //$this->assertFalse(y);
    public function setUp(){
        
        parent::setUp();
        $this->app->addRoutes(['badges']);
        
    }
    
    public function testForGetAllBadges() {
        
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/badges/']);
        $this->assertContainsProperties($responseObj->data, ['slug']);
    } 
    
}