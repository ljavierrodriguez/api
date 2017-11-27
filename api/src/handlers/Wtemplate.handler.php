<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Helpers\BCValidator;

class WtemplateHandler extends MainHandler{
    
    protected $slug = 'Wtemplate';
    
    public function getAllWtemplatesHandler(Request $request, Response $response) {
        
        $wtemplates = Wtemplate::all();
        
        return $this->success($response,$wtemplates);
    }
    
    public function getSingleWtemplate(Request $request, Response $response) {
        $wtemplateId = $request->getAttribute('wtemplate_id');
        
        $wtemplate = null;
        if(is_numeric($wtemplateId)) $wtemplate = Wtemplate::find($wtemplateId);
        else $wtemplate = Wtemplate::where('slug', $wtemplateId)->first();
        if(!$wtemplate) throw new Exception('Invalid wtemplate slug or id: '.$wtemplateId);
        
        return $this->success($response,$wtemplate);
    }
    
    public function createWtemplateHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $wtemplate = new Wtemplate();
        $wtemplate = $this->setMandatory($wtemplate,$data,'name',BCValidator::NAME);
        $wtemplate = $this->setMandatory($wtemplate,$data,'slug',BCValidator::SLUG);
        //$workshop = $this->setOptional($workshop,$data,'language',BCValidator::SLUG);
        $wtemplate->save();
        
        return $this->success($response,$wtemplate);
    }
    
    public function updateWtempalteHandler(Request $request, Response $response) {
        
        $wtemplateId = $request->getAttribute('wtemplate_id');
        $data = $request->getParsedBody();
        
        $wtemplate = Wtemplate::find($workshopId);
        if(!$wtemplate) throw new Exception('Invalid wtemplate id: '.$wtemplateId);
        
        $wtemplate = $this->setOptional($wtemplate,$data,'name',BCValidator::NAME);
        $wtemplate = $this->setOptional($wtemplate,$data,'slug',BCValidator::SLUG);
        //$workshop = $this->setOptional($workshop,$data,'language',BCValidator::SLUG);
        $wtemplate->save();
        
        return $this->success($response,$wtemplate);
    }
    
}