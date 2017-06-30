<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class StudentHandler extends MainHandler{
    
    public function getStudentHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $student = Student::find($studentId);
        if(!$student) throw new Exception('Invalid student id');
        
        return $this->success($response,$student);
    }
    
    public function getAllStudentsHandler(Request $request, Response $response) {

        $students = Student::all();
        return $this->success($response,$students);
    }
    
    public function getStudentActivityHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $activities = Activity::where('student_id', $studentId)->get();
        if(!$activities) throw new Exception('Invalid student id:'.$studentId);
        
        return $this->success($response,$activities);
    }
    
    public function createOrUpdateStudentHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        $data = $request->getParsedBody();
        
        if($studentId){
            $student = Student::find($studentId);
            if(!$student) throw new Exception('Invalid student id: '.$studentId);
            
            $student = $this->setOptional($student,$data,'breathecode_id');
            $student = $this->setOptional($student,$data,'email');
            $student = $this->setOptional($student,$data,'full_name');
        } 
        else{
            $student = new Student();
            $student->email = $data['email'];
            $student->full_name = $data['full_name'];
            $student->breathecode_id = $data['breathecode_id'];
        }
        
        $student = $this->setOptional($student,$data,'avatar_url');
        $student = $this->setOptional($student,$data,'total_points');
        $student = $this->setOptional($student,$data,'description');
        $student->save();
        
        return $this->success($response,$student);
    }
    
    public function createStudentActivityHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $badge = Badge::where('slug', $data['badge_slug'])->first();
        if(!$badge) throw new Exception('Invalid badge slug');
        
        $student = Student::find($studentId);
        if(!$student) throw new Exception('Invalid student id');
        
        $activity = new Activity();
        $activity->type = $data['type'];
        $activity->name = $data['name'];
        $activity->description = $data['description'];
        $activity->points_earned = $data['points_earned'];
        $activity->type = $data['type'];
        $activity->student()->associate($student);
        $activity->badge()->associate($badge);
        $activity->save();
        
        $student->updateBasedOnActivity();
        
        return $this->success($response,$activity);
    }
    
    public function deleteStudentActivityHandler(Request $request, Response $response) {
        $activityId = $request->getAttribute('activity_id');
        
        $activity = Activity::find($activityId);
        if(!$activity) throw new Exception('Invalid activity id');
        
        $attributes = $activity->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / (60 * 60 * 24));
        if($daysOld>5) throw new Exception('The activity is to old to delete');
        
        $student = $activity->student()->first();
        $activity->delete();
        $student->updateBasedOnActivity();
        
        return $this->success($response,"ok");
    }
    
    public function deleteStudentHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $student = Student::find($studentId);
        if(!$student) throw new Exception('Invalid student id');
        
        $attributes = $student->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / (60 * 60 * 24));
        if($daysOld>5) throw new Exception('The student is to old to delete');
        
        $student->activities()->delete();
        $student->badges()->delete();
        $student->delete();
        
        return $this->success($response,"ok");
    }
    
}