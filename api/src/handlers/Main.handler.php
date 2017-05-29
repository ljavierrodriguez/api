<?php

class MainHandler{
    
    protected $app;
    
    function __construct($app)
    {
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
    
    public function setOptional($model,$data,$key)
    {
        if(isset($data[$key])) $model[$key] = $data[$key];
        return $model;
    }
}