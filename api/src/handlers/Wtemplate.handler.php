<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Helpers\BCValidator;
use Helpers\ArgumentException;

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
        if(!$wtemplate) throw new ArgumentException('Invalid wtemplate slug or id: '.$wtemplateId);
        
        return $this->success($response,$wtemplate);
    }
    
    public function createWtemplateHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new ArgumentException('There was an error retrieving the request content, it needs to be a valid JSON');
        
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
        if(!$wtemplate) throw new ArgumentException('Invalid wtemplate id: '.$wtemplateId);
        
        $wtemplate = $this->setOptional($wtemplate,$data,'name',BCValidator::NAME);
        $wtemplate = $this->setOptional($wtemplate,$data,'slug',BCValidator::SLUG);
        //$workshop = $this->setOptional($workshop,$data,'language',BCValidator::SLUG);
        $wtemplate->save();
        
        return $this->success($response,$wtemplate);
    }
    
    public function deleteHandler(Request $request, Response $response) {
        $wtId = $request->getAttribute('wtemplate_id');
        
        $wtemplate = Wtemplate::find($wtId);
        if(!$wtemplate) throw new ArgumentException('Invalid WorkshopTemplate id: '.$wtId);
        
        $workshops = $wtemplate->workshops()->get();
        if(count($workshops)>0) throw new ArgumentException('The WorkshopTemplate cannot be deleted because it has workshops');
        
        $wtemplate->delete();
        
        return $this->success($response,"The WorkshopTemplate was deleted");
    }
    
}