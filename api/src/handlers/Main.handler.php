<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class MainHandler{
    
    protected $app;
    protected $slug;
    
    function __construct($app){
        
        $this->app = $app;
        
        $c = $this->app->getContainer();
        $c['errorHandler'] = function ($c) {
            return array($this, 'fail');
        };
    }
    
    public function success ($response,$data) {
            
        if(!$data) $data = [];
        $successArray = array(
            "code"=> 200,
            "data"=> $data
            );
            
        return $response->withJson($successArray);
    }
    
    public function fail($request, $response, $args) {
        
        $errorArray = array(
                    "code"=> 500,
                    "msg"=>  $args->getMessage()
                    );
                    
        return $response->withJson($errorArray,500);
    }
    
    public function setOptional($model,$data,$key){
        
        if(isset($data[$key])) $model[$key] = $data[$key];
        return $model;
    }
    
    public function getAllHandler(Request $request, Response $response) {
        $all = call_user_func($this->slug . '::all');
        return $this->success($response,$all);
    }
    
    public function getSingleHandler(Request $request, Response $response) {
        $id = $request->getAttribute(strtolower($this->slug).'_id');
        
        $single = call_user_func_array($this->slug . '::find',[$id]);;
        if(!$single) throw new Exception('Invalid '.strtolower($this->slug).'_id');
        
        return $this->success($response,$single);
    }
}