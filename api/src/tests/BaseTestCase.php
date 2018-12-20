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
        if(!defined('RUNING_TEST')) define('RUNING_TEST',true);
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
                'addContentLengthHeader' => false,
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
        $this->app->getContainer()["request"] = $req;
        $response = $this->app->run();
        $responseBody = $response->getBody();
        $responseObj = json_decode($responseBody);
        
        $assertion =  new AssertResponse($this, $response, $responseObj);
        $this->_logTest($params, $response, $responseObj, $assertion);

        return $assertion;
    }

    function log($msg){
        if(DEBUG){
            echo "\033[33m";
            print_r($msg);
            echo "\033[0m";
        }
    }

    function _logTest($params, $response, $responseObj, $assertion=null){
        if(DEBUG){
            $code = $response->getStatusCode();
            $expected = (!$assertion) ? '' : $assertion->getExpectedRespCode();
            if($code != 200 && $code != 400 && $code != 404){
                if(!empty($responseObj)){
                    $logEntry = "\n \n [ \n".
                    "   [code]     => \033[33m".$responseObj->code."\033[0m \n".
                    "   [msg]      => \033[31m".$responseObj->msg."\033[0m \n".
                    "]\n \n";
                    echo "\033[31m \n ****    FOUND SOME MISMATCHES:    **** \n \033[0m";
                    print_r($logEntry);
                }
                else {
                    echo "\033[31m \n ****    FOUND SOME MISMATCHES:    **** \n \033[0m";
                    echo "   [request]  => \033[36m".$params['REQUEST_METHOD'].": ".$params['REQUEST_URI']."\033[0m \n";
                    echo "   [details]  => \033[33m No details or response was provided \033[0m \n \n";
                }
            }
        }
    }
}

class AssertResponse{
    private $test;
    private $response;
    private $expectedRespCode = null;
    private $responseObj;
    function __construct($test, $response, $responseObj){
        $this->test = $test;
        $this->response = $response;
        $this->responseObj = $responseObj;
    }
    function getExpectedRespCode(){ return $this->expectedRespCode; }
    function expectSuccess($code=200){
        $this->test->assertSame($this->response->getStatusCode(), 200);
        $this->test->assertSame($this->responseObj->code, 200);
        $this->expectedRespCode = $code;
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function expectFailure($code=400){
        $this->test->assertSame($this->response->getStatusCode(), $code);
        $this->test->assertSame($this->responseObj->code, $code);
        $this->expectedRespCode = $code;
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function withProperties($properties){
        $hasProperties = true;
        foreach($properties as $prop){
            $this->test->assertObjectHasAttribute($prop, $this->responseObj->data);
        }
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function withPropertiesAndValues($properties){
        $hasProperties = true;
        foreach($properties as $key => $value){
            $this->test->assertObjectHasAttribute($key, $this->responseObj->data);
            if(property_exists($this->responseObj->data, $key)){
                $data = (array) $this->responseObj->data;
                $this->test->assertSame($data[$key], $value);
            }
        }
                
        return new AssertResponse($this->test, $this->response, $this->responseObj);
    }
    function getParsedBody(){
        return $this->responseObj;
    }
}