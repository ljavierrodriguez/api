<?php
namespace Tests;

class TalentTreeBTest extends BaseTestCase
{
    protected $app;
    
    //$this->assertSame(x, y);
    //$this->assertTrue(x);
    //$this->assertFalse(y);
    public function setUp(){
        
        parent::setUp();
        $this->app->addRoutes(['badge']);
        
    }
    
    public function testForGetAll(){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/badges/']);
        $this->assertTrue(is_array($responseObj->data)); //needs to be an array
        $this->assertTrue(isset($responseObj->data[0])); //needs to be an array
        return $responseObj->data;
    }
    /**
     * @depends testForGetAll
     */
    public function testForDeleteAll($badges) {
        
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'DELETE', 'REQUEST_URI' => '/badge/'.$badges[0]->id]);
        $this->assertTrue($responseObj->data == 'ok');
    } 
    
    
}