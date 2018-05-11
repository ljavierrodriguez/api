<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class BadgeGetTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['badge']);

    }

    function testGetAllBadge(){
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/badges/']);
        $this->assertSame($responseObj->data);
    }

    /*function testGetIsNotEmpty(){
        $id = 16;
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/badge/'.$id])->expectSuccess();
        $this->assertEmpty($responseObj->data);
    }*/
}
?>