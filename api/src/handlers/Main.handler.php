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
    
    public function syncMainData(Request $request, Response $response) {
        
        $log = [];
        
        $fullstack = Profile::where('slug', "full-stack-web")->first();
        if(!$fullstack){
            $fullstack = new Profile();
            $fullstack->name = "Full-Stack Web Developer";
            $fullstack->slug = "full-stack-web";
            $fullstack->description = "Manages front-end and back-end side of the web";
            $fullstack->save();
            
            $log[] = "The profile full-stack-web was created to train Full Stack Web Developers";
            
        }else $log[] = "The profile full-stack-web was already created.";
        
        $this->_createOauthClients();
        $log[] = "nbernal and ogarcia clients created.";
        
        return $this->success($response,$log);
    }
    
    private function _createOauthClients(){
        $oscar = $this->app->db->table('oauth_clients')->where([
            'oauth_clients.client_id' => 'ogarcia'
        ])->select('oauth_clients.client_id');
        
        if(count($oscar)==0) $this->app->db->table('oauth_clients')->insert(array(
        		'client_id' => "ogarcia",
        		'client_secret' => "8ca0854a441cc4c201f925d6bfb36dafa48829c4",
        		'redirect_uri' => "http://fake/",
        		'scope' => "admin",
        	));
        	
        $nilson = $this->app->db->table('oauth_clients')->where([
            'oauth_clients.client_id' => 'nbernal'
        ])->select('oauth_clients.client_id');
        	
        if(count($nilson)==0) $this->app->db->table('oauth_clients')->insert(array(
        		'client_id' => "nbernal",
        		'client_secret' => "8ca0854a441cc4c201f925d6bfb36dafa48829c5",
        		'redirect_uri' => "http://fake/",
        		'scope' => "admin",
        	));
    }
}