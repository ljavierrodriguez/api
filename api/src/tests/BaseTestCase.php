<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use \BreatheCodeAPI;

class BaseTestCase extends TestCase {
 
    /**
     * Default preparation for each test
     */
    public function setUp()
    {
        parent::setUp();
 
        $this->prepareForTests();
    }
 
    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
    }
 
    /**
     * Migrates the database and set the mailer to 'pretend'.
     * This will cause the tests to run quickly.
     */
    private function prepareForTests(){
        
        $this->app = new \BreatheCodeAPI([
            'settings' => [
                'displayErrorDetails' => true,
                'determineRouteBeforeAppMiddleware' => false,
                'db' => [
                    'driver'   => 'sqlite',
                    'database' => __DIR__ . '/testing.sqlite',
                    'prefix'   => '',
                ]
            ],
        ]);
    }
    
    /**
       [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/catalog/countries/',
       ]
     */
    protected function mockAPICall($params){
        $env = Environment::mock($params);
            
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        
        $this->assertSame($response->getStatusCode(), 200);
        $response = $response->getBody();
        $responseObj = json_decode($response);
        $this->assertSame($responseObj->code, 200);
        
        return $responseObj;
    }
    
    protected function assertContainsProperties($obj, $properties){
        foreach($properties as $prop) if(!property_exists($obj, $prop)) return false;
        
        return true;
    }
}