<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Helpers\BCValidator;

class CohortHandler extends MainHandler{
    
    protected $slug = 'Cohort';
    
    public function getAllCohortsHandler(Request $request, Response $response) {
        
        $cohorts = Cohort::all();
        
        $data = $request->getParams();
        if(!empty($data))
        {
            $filtered = $cohorts->filter(function ($value, $key) use($data) {
                
                if(!empty($data["language"])) if($value->language != $data["language"]) return false;
                if(!empty($data["location"])) if($value->location_slug != $data["location"]) return false;
                
                return true;
            });
            return $this->success($response,$filtered->values());
        }
        
        return $this->success($response,$cohorts);
    }
    
    public function getSingleCohort(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $cohort = null;
        if(is_numeric($cohortId)) $cohort = Cohort::find($cohortId);
        else $cohort = Cohort::where('slug', $cohortId)->first();
        if(!$cohort) throw new Exception('Invalid badge slug or id: '.$cohortId);
        
        return $this->success($response,$cohort);
    }
    
    public function getAllCohortsFromLocationHandler(Request $request, Response $response) {
        $locationId = $request->getAttribute('location_id');
        
        $location = Location::find($locationId);
        if(!$location) throw new Exception('Invalid location id:'.$locationId);
        
        return $this->success($response,$location->cohorts()->get());
    }
    
    public function getAllCohortsFromTeacherHandler(Request $request, Response $response) {
        $teacherId = $request->getAttribute('teacher_id');
        
        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new Exception('Invalid teacher id:'.$teacherId);
        
        return $this->success($response,$teacher->cohorts()->get());
    }
    
    public function createCohortHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $location = Location::where('slug', $data['location_slug'])->first();
        if(!$location) throw new Exception('Invalid location_slug slug');
        
        $cohort = new Cohort();
        $cohort = $this->setMandatory($cohort,$data,'name',BCValidator::NAME);
        $cohort = $this->setMandatory($cohort,$data,'slug',BCValidator::SLUG);
        $cohort->stage = 'not-started';
        $cohort = $this->setOptional($cohort,$data,'language',BCValidator::SLUG);
        $cohort = $this->setOptional($cohort,$data,'slack-url',BCValidator::URL);
        $cohort = $this->setOptional($cohort,$data,'kickoff-date',BCValidator::DATETIME);
        $location->cohorts()->save($cohort);
        
        return $this->success($response,$cohort);
    }
    
    public function syncCohortHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $location = Location::where('slug', $data['location_slug'])->first();
        if(!$location) throw new Exception('Invalid location_slug slug');
        
        $teacher = Teacher::find($data['instructor_id']);
        if(!$teacher) throw new Exception('Invalid instructor_id: '.$data['instructor_id']);
        
        if(!isset($data['slug']))  throw new Exception('You have to specify a cohort slug');
        $cohort = Cohort::where('slug', $data['slug'])->first();
        if(!$cohort) $cohort = new Cohort();
        
        if(!empty($data['stage']) && !in_array($data['stage'], ['not-started', 'on-prework', 'on-course','on-final-project','finished']))
            throw new Exception('Invalid cohort stage');
        
        $cohort = $this->setMandatory($cohort,$data,'name',BCValidator::NAME);
        $cohort = $this->setMandatory($cohort,$data,'slug',BCValidator::SLUG);
        $cohort = $this->setMandatory($cohort,$data,'stage',BCValidator::SLUG);
        $cohort = $this->setOptional($cohort,$data,'language',BCValidator::SLUG);
        $cohort = $this->setOptional($cohort,$data,'slack-url',BCValidator::URL);
        $cohort = $this->setOptional($cohort,$data,'kickoff-date',BCValidator::DATETIME);
        $location->cohorts()->save($cohort);
        $cohort->teachers()->attach($teacher);
        
        $currentTeachers = $cohort->teachers()->get();
        foreach($currentTeachers as $ct) $cohort->teachers()->updateExistingPivot($ct->id, ['is_instructor'=>false]);
        $cohort->teachers()->updateExistingPivot($teacher->id, ['is_instructor'=>true]);
        
        return $this->success($response,$cohort);
    }
    
    public function updateCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        $data = $request->getParsedBody();
        
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id: '.$cohortId);
        
        if(!empty($data['stage']) && !in_array($data['stage'], ['not-started', 'on-prework', 'on-course','on-final-project','finished']))
            throw new Exception('Invalid cohort stage');

        $cohort = $this->setOptional($cohort,$data,'name',BCValidator::NAME);
        $cohort = $this->setOptional($cohort,$data,'stage',BCValidator::SLUG);
        $cohort = $this->setOptional($cohort,$data,'slug',BCValidator::SLUG);
        $cohort = $this->setOptional($cohort,$data,'language',BCValidator::SLUG);
        $cohort = $this->setOptional($cohort,$data,'slack-url',BCValidator::URL);
        $cohort = $this->setOptional($cohort,$data,'kickoff-date',BCValidator::DATETIME);
        $cohort->save();
        
        return $this->success($response,$cohort);
    }
    
    public function deleteCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id');
        
        $students = $cohort->students()->get();
        if(count($students)>0) throw new Exception('Remove all students from the cohort first.');
        
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
    
    public function addTeacherToCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $teachersArray = $request->getParsedBody();
        if(empty($teachersArray)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        //there can only be a max of one main instructor
        $mainInstructors = [];
        foreach($teachersArray as $t) if(isset($t['is_instructor']) && $t['is_instructor']=='true') $mainInstructors[] = $t['teacher_id'];
        if(count($mainInstructors)>1) throw new Exception('There can only be one main instructor');
       
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id: '.$cohortId);
        
        $auxTeachers = [];
        $currentTeachers = $cohort->teachers()->get();
        foreach($teachersArray as $tea) {
            $teacher = Teacher::find($tea['teacher_id']);
            if(!$teacher) throw new Exception('Invalid teacher id: '.$tea['teacher_id']);
            if(!$currentTeachers->contains($tea['teacher_id'])) $auxTeachers[] = $tea['teacher_id'];
        }

        if($auxTeachers>0) $cohort->teachers()->attach($auxTeachers);
        else throw new Exception('Error retreving Teachers form the body request');
        
        foreach($currentTeachers as $ct) $cohort->teachers()->updateExistingPivot($ct->id, ['is_instructor'=>false]);
        $cohort->teachers()->updateExistingPivot($mainInstructors[0], ['is_instructor'=>true]);
        
        return $this->success($response,$currentTeachers);
    }
    
    public function deleteTeacherFromCohortHandler(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');
        
        $teachersArray = $request->getParsedBody();
        if(empty($teachersArray)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $cohort = Cohort::find($cohortId);
        if(!$cohort) throw new Exception('Invalid cohort id: '.$cohortId);
        
        $auxTeachers = [];
        foreach($teachersArray as $tea) {
            $teacher = Teacher::find($tea['teacher_id']);
            if(!$teacher) throw new Exception('Invalid teacher id: '.$tea['teacher_id']);
            $auxTeachers[] = $tea['teacher_id'];
        }

        if($auxTeachers>0) $cohort->teachers()->detach($auxTeachers);
        else throw new Exception('Error deleting teachers');
        
        return $this->success($response,"There are ".count($cohort->teachers()->get())." teachers in the cohort.");
    }
}