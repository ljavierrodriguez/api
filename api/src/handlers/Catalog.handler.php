<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Helpers\ArgumentException;

class CatalogHandler extends MainHandler{
    
    public static $countries = [
        "venezuela" => ["Caracas", "Maracaibo", "Valencia"],
        "usa" => ["Miami", "Orlando", "New York"],
        "mexico" => ["Mexico City", "Guadalajara", 'Monterrey'],
        "chile" => ["Santiago"],
        "peru" => ["Lima"],
        "ecuador" => ["Quito", "Guayaquil"],
        "spain" => ["Madrid", "Barcelona", 'Galicia', 'Valencia'],
        "colombia" => ["Bogota", "Medellin"],
        "guatemala" => ["Guatemala City"]
        ];
    
    public function getAllTechnologies(Request $request, Response $response) {
        $technologies = ['CSS3','JS','PHP','GIT','C9','HTML5','REACT','WORDPRESS','AJAX','DJANGO','MYSQL','MONGODB'];
        return $this->success($response,$technologies);
    }  
    public function getAllCohortStages(Request $request, Response $response) {
        $cohort_stages = Cohort::$possibleStages;
        return $this->success($response,$cohort_stages);
    }  
    public function getAllAtemplateDifficulties(Request $request, Response $response) {
        $difficulties = Atemplate::$possibleDifficulties;
        return $this->success($response,$difficulties);
    }  
    /*
    public function getAllCountries(Request $request, Response $response) {
        $aux = [];
        foreach($this->countries as $key => $val) $aux[] = strtolower($key);
        return $this->success($response,arsort($aux));
    } */ 
    
    public function getAllCountries(Request $request, Response $response) {
        $aux = [];
        foreach(self::$countries as $key => $val){
            if(!isset($aux[strtolower($key)])) $aux[strtolower($key)] = [];
            foreach(self::$countries[$key] as $val) $aux[strtolower($key)][] = ucwords($val);
        }
        
        ksort($aux);
        return $this->success($response,$aux);
    }  
}