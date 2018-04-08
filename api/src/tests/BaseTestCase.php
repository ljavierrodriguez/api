<?php
namespace Tests;
require('api/config.php');

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
 
        $this->createDatabase();
        $this->runSeeds();
        $this->createVirtualAPI();
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
     * Create the database event if it does not exists 
    **/
    
    private function createDatabase(){
        
    }
    
    /**
     * Create the database event if it does not exists 
    **/
    
    private function runSeeds(){
        
    }
    
    /**
     * Migrates the database and set the mailer to 'pretend'.
     * This will cause the tests to run quickly.
     */
    private function createVirtualAPI(){ 
        
        if(!file_exists(UT_DB_NAME)) throw new \Exception('Database '+UT_DB_NAME+' does not exists');
        $this->app = new \BreatheCodeAPI([
            'authenticate' => false,
            'settings' => [
                'displayErrorDetails' => true,
                'determineRouteBeforeAppMiddleware' => false,
                'db' => [
                    'driver'   => UT_DB_DRIVER,
                    'database' => UT_DB_NAME,
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
    protected function mockAPICall($params, $body=null){
        $env = Environment::mock($params);
        
        $req = Request::createFromEnvironment($env);
        $bodyStream = $req->getBody();
        $bodyStream->write(json_encode($body));
        $bodyStream->rewind();
        $req = $req->withBody($bodyStream);
        $req = $req->withHeader('Content-Type', 'application/json');
        
        $this->app->getContainer()["environment"] = $env;
        $this->app->getContainer()["request"] =$req;
        $response = $this->app->run(true);
        
        $responseBody = $response->getBody();
        $responseObj = json_decode($responseBody);
        
        $this->assertSame($response->getStatusCode(), 200);
        $this->assertSame($responseObj->code, 200);
        
        return $responseObj;
    }
    
    protected function assertContainsProperties($obj, $properties){
        
        foreach($properties as $prop) if(!property_exists($obj, $prop)) return false;
        
        return true;
    }
}