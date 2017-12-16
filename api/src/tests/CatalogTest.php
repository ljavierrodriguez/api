<?php
namespace Tests;

class CatalogTest extends BaseTestCase
{
    protected $app;
    
    public function setUp(){
        
        parent::setUp();
        $this->app->addRoutes(['catalog']);
        
    }
    
    public function testForCountry() {
        
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/catalog/countries/']);
        $this->assertSame($responseObj->data->chile, ['Santiago']);
    } 
    
    public function testForTechnologies() {
        
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/catalog/technologies/']);
        $this->assertTrue(is_array($responseObj->data));
        $this->assertFalse(count($responseObj->data) == 0);
    } 
    
    public function testForCohortStages() {
        
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/catalog/cohort_stages/']);
        $this->assertTrue(is_array($responseObj->data));
        $this->assertTrue(in_array('not-started',$responseObj->data));
    } 
    
    public function testForTemplateDificulties() {
        
        $responseObj = $this->mockAPICall(['REQUEST_METHOD' => 'GET','REQUEST_URI' => '/catalog/atemplate_difficulties/']);
        $this->assertTrue(is_array($responseObj->data));
        $this->assertTrue(in_array('begginer',$responseObj->data));
    } 
}