<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Helpers\BCValidator;

class LocationHandler extends MainHandler{
    
    protected $slug = 'Location';
    
    public function createLocationHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $location = new Location();
        $location = $this->setMandatory($location,$data,'name',BCValidator::NAME);
        $location = $this->setMandatory($location,$data,'slug',BCValidator::SLUG);
        $location = $this->setOptional($location,$data,'address');

        if(!empty($data['country']))
        {
            $data['country'] = strtolower($data['country']);
            if(!isset(CatalogHandler::$countries[$data['country']])) throw new Exception('Invalid country value: '.$data['country']);
            else $location->country = $data['country'];
        }

        $location->save();
        
        return $this->success($response,$location);
    }
    
    public function syncLocationHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        if(!isset($data['slug']))  throw new Exception('You have to specify a location slug');
        $location = Location::where('slug', $data['slug'])->first();
        
        if(!$location) $location = new Location();
        $location = $this->setMandatory($location,$data,'name',BCValidator::NAME);
        $location = $this->setMandatory($location,$data,'slug',BCValidator::SLUG);
        $location = $this->setOptional($location,$data,'address');

        if(!empty($data['country']))
        {
            $data['country'] = strtolower($data['country']);
            if(!isset(CatalogHandler::$countries[$data['country']])) throw new Exception('Invalid country value: '.$data['country']);
            else $location->country = $data['country'];
        }
        
        $location->save();
        
        return $this->success($response,$location);
    }
    
    public function updateLocationHandler(Request $request, Response $response) {
        $locationId = $request->getAttribute('location_id');
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $location = Location::find($locationId);
        if(empty($location)) throw new Exception('Location not found');
        
        if(!empty($data['country']))
        {
            $data['country'] = strtolower($data['country']);
            if(!isset(CatalogHandler::$countries[$data['country']])) throw new Exception('Invalid country value: '.$data['country']);
            else $location->country = $data['country'];
        }
        
        $location->name = $data['name'];
        $location->slug = $data['slug'];
        $location->address = $data['address'];
        $location->save();
        
        return $this->success($response,$location);
    }
    
    public function deleteLocationHandler(Request $request, Response $response) {
        $locationId = $request->getAttribute('location_id');
        
        $location = Location::find($locationId);
        if(empty($location)) throw new Exception('Location not found');
        
        $cohorts = $location->cohorts()->get();
        if(count($cohorts)>0) throw new Exception('The location must have 0 cohorts in order to be deleted.');
        
        $location->delete();
        
        return $this->success($response,'Location successfully deleted.');
    }
}