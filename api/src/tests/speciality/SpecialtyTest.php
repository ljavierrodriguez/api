<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class SpecialtyTest extends BaseTestCase {

    protected $app;
    public function setUp()
    {
        parent::setUp();
        $this->app->addRoutes(['specialty']);

    }
    
    // ---- Creacion de Specialty en Badges -----
    // ---- Get specialty from profile en Profiles -----

    function testGetAllSpecialtyIsNotEmpty(){
        $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/specialties/'])
            ->expectSuccess();
    }

    function testGetSpecialtyIsNotEmpty(){
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/specialty/1'])
            ->expectSuccess();
            $this->assertNotEmpty($specialty);
    }

    function testGetSpecialtyIDIsChrSpecial(){
        $specialty = $this->mockAPICall(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/specialty/!@#'])
            ->expectFailure();
    }
}
?>