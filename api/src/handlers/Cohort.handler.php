<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class CohortHandler extends MainHandler{
    
    protected $slug = 'Cohort';
    
    public function getAllCohortsFromLocationHandler(Request $request, Response $response) {
        $locationId = $request->getAttribute('location_id');
        
        $location = Location::find($locationId);
        if(!$location) throw new Exception('Invalid location id:'.$locationId);
        
        return $this->success($response,$location->cohorts()->get());
    }
    
    public function createCohortHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $location = Location::where('slug', $data['location_slug'])->first();
        if(!$location) throw new Exception('Invalid location_slug slug');
        
        $cohort = new Cohort();
        $cohort->name = $data['name'];
        $cohort->stage = 'not-started';
        $cohort->slug = $data['slug'];
        $cohort = $this->setOptional($cohort,$data,'slack-url');
        $location->cohorts()->save($cohort);
        
        return $this->success($response,$cohort);
    }
    
    public function updateCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        $data = $request->getParsedBody();
        
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id: '.$cohortId);

        $cohort = $this->setOptional($cohort,$data,'name');
        $cohort = $this->setOptional($cohort,$data,'stage');
        $cohort = $this->setOptional($cohort,$data,'slug');
        $cohort = $this->setOptional($cohort,$data,'slack-url');
        $cohort->save();
        
        return $this->success($response,$cohort);
    }
    
    public function deleteCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id');
        
        $students = $cohort->students()->get();
        if(count($students)>0) throw new Exception('Remove all students from the cohort first.');
        
        //$teachers = $cohort->teachers()->get();
        //if(count($teachers)>0) throw new Exception('Remove all teachers from the cohort first.');
        
        $cohort->delete();
        
        return $this->success($response,"The cohort was deleted successfully");
    }
    
    public function getCohortStudentsHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id:'.$cohortId);
        
        $students = $cohort->students()->get();
        
        return $this->success($response,$students);
    }
    
    public function addStudentToCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $studentsArray = $request->getParsedBody();
        if(empty($studentsArray)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
       
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id:'.$cohortId);
        
        $auxStudents = [];
        foreach($studentsArray as $stu) $auxStudents[] = $stu['student_id'];

        if($auxStudents>0) $cohort->students()->attach($auxStudents);
        else throw new Exception('Error retreving Students form the body request');
        
        return $this->success($response,"There are ".count($cohort->students())." students in the cohort.");
    }
    
}