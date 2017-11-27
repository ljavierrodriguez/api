<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Helpers\BCValidator;

class WorkshopHandler extends MainHandler{
    
    protected $slug = 'Workshop';
    
    public function getAllWorkshopsHandler(Request $request, Response $response) {
        
        $workshops = Workshop::all();
        
        return $this->success($response,$workshops);
    }
    
    public function getAllWorkshopsFromLocationHandler(Request $request, Response $response) {
        $locationId = $request->getAttribute('location_id');
        
        $location = Location::find($locationId);
        if(!$location) throw new Exception('Invalid location id:'.$locationId);
        
        return $this->success($response,$location->workshops()->get());
    }
    
    public function getSingleWorkshop(Request $request, Response $response) {
        $workshopId = $request->getAttribute('workshop_id');
        
        $workshop = null;
        if(is_numeric($workshopId)) $workshop = Workshop::find($workshopId);
        else $workshop = Workshop::where('slug', $workshopId)->first();
        if(!$workshop) throw new Exception('Invalid workshop slug or id: '.$workshopId);
        
        return $this->success($response,$workshop);
    }
    
    public function createWorkshopHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $location = Location::where('slug', $data['location_slug'])->first();
        if(!$location) throw new Exception('Invalid location_slug slug');
        
        $template = Wtemplate::where('slug', $data['wtemplate_slug'])->first();
        if(!$template) throw new Exception('Invalid WorkShop Template slug: '.$data['wtemplate_slug']);
        
        $workshop = new Workshop();
        $workshop = $this->setMandatory($workshop,$data,'name',BCValidator::NAME);
        $workshop = $this->setMandatory($workshop,$data,'slug',BCValidator::SLUG);
        $workshop = $this->setMandatory($workshop,$data,'start-date',BCValidator::DATETIME);
        //$workshop = $this->setOptional($workshop,$data,'language',BCValidator::SLUG);
        $workshop->wtemplate()->associate($template);
        $location->workshops()->save($workshop);
        $workshop->save();
        
        return $this->success($response,$workshop);
    }
    
    public function updateWorkshopHandler(Request $request, Response $response) {
        
        $workshopId = $request->getAttribute('workshop_id');
        $data = $request->getParsedBody();
        
        $workshop = Workshop::find($workshopId);
        if(!$workshop) throw new Exception('Invalid workshop id: '.$workshopId);
        
        if(!empty($data['location_slug']))
        {
            $location = Location::where('slug', $data['location_slug'])->first();
            if(!$location) throw new Exception('Invalid location slug: '.$data['location_slug']);
            $workshop->location()->associate($location);
        }
        
        if(!empty($data['wtemplate_slug']))
        {
            $template = Wtemplate::where('slug', $data['wtemplate_slug'])->first();
            if(!$template) throw new Exception('Invalid WorkShop Template slug: '.$data['wtemplate_slug']);
            $workshop->wtemplate()->associate($template);
        }

        $workshop = $this->setOptional($workshop,$data,'name',BCValidator::NAME);
        $workshop = $this->setOptional($workshop,$data,'slug',BCValidator::SLUG);
        $workshop = $this->setOptional($workshop,$data,'language',BCValidator::SLUG);
        //$workshop = $this->setOptional($workshop,$data,'start-date',BCValidator::DATETIME);
        $workshop->save();
        
        return $this->success($response,$workshop);
    }
    
}