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
        
        //if($response->getStatusCode() == 500){ print_r($responseBody); die(); }
        return new AssertResponse($this, $response, $responseObj);
    }
}

class AssertResponse{
    private $test;
    private $response;
    private $responseObj;
    function __construct($test, $response, $responseObj){
        $this->test = $test;
        $this->response = $response;
        $this->responseObj = $responseObj;
    }
    function expectSuccess($code=200){
        $this->test->assertSame($this->response->getStatusCode(), 200);
        $this->test->assertSame($this->responseObj->code, 200);
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function expectFailure($code=400){
        $this->test->assertSame($this->response->getStatusCode(), $code);
        $this->test->assertSame($this->responseObj->code, $code);
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function withProperties($properties){
        $hasProperties = true;
        foreach($properties as $prop) 
            if(!property_exists($this->responseObj->data, $prop)) $hasProperties = false;
        $this->test->assertTrue($hasProperties);
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function withPropertiesAndValues($properties){
        $hasProperties = true;
        foreach($properties as $key => $value) 
            if(!property_exists($this->responseObj->data, $key))
                $this->test->assertTrue($this->responseObj->data[$key] == $value);
                
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function getParsedBody(){
        return $this->responseObj;
    }
}