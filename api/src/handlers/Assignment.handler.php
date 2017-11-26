<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class AssignmentHandler extends MainHandler{
    
    protected $slug = 'Assignment';
    
    public function getAllStudentAssignmentsHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $student = Student::find($studentId);
        if(!$student) throw new Exception('Invalid student id');
        
        return $this->success($response,$student->assignments()->get());
    }
    
    public function getAllTeacherAssignmentsHandler(Request $request, Response $response) {
        $teacherId = $request->getAttribute('teacher_id');
        $data = $request->getQueryParams();
        
        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new Exception('Invalid teacher id: '.$teacherId);
        $results = $teacher->assignments()->get();

        if(!empty($data['cohort_slug'])){
            $data['cohort_slug'] = strtolower($data['cohort_slug']);
            
            $filtered = $results->filter(function($assigntment) use ($data){
                if($student = Student::find($assigntment->student_user_id))
                {
                    return in_array($data['cohort_slug'],$student->cohorts->toArray());
                }
                
                return false;
            });
            $results = $filtered->all();
        }
        
        return $this->success($response,$results);
    }
    
    public function createAssignmentHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $template = Atemplate::find($data['template_id']);
        if(!$template) throw new Exception('Invalid template id');
        
        $student = Student::find($data['student_id']);
        if(!$student) throw new Exception('Invalid student id');
        
        $teacher = Teacher::find($data['teacher_id']);
        if(!$teacher) throw new Exception('Invalid teacher id');
        
        $assignments = $this->app->db->table('assignments')
        ->where([
            'assignments.student_user_id' => $student->user_id,
            'assignments.atemplate_id' => $template->id
        ])->select('assignments.id')->get();
        if(count($assignments)>0) throw new Exception('There is already an assignment for this student on template: '.$template->id);
        
        $this->_createStudentAssignment($student,$template,$teacher,$data['duedate']);
        
        return $this->success($response,$assignment);
    }
    
    public function syncFromWPHandler(Request $request, Response $response){
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $template = Atemplate::where('project_slug', $data['template_slug'])->first();
        if(!$template) throw new Exception('Invalid template slug: '.$data['template_slug']);
        
        $student = Student::find($data['student_id']);
        if(!$student) throw new Exception('Invalid student id: '.$data['student_id']);
        
        $teacher = Teacher::find($data['teacher_id']);
        if(!$teacher) throw new Exception('Invalid teacher id: '.$data['teacher_id']);
        
        $assignment = $this->_createStudentAssignment($student,$template,$teacher->user_id,$data['duedate'], $data['status']);
        
        return $this->success($response,$assignment);
    }
    
    public function createCohortAssignmentHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $duedate = date('Y-m-d',strtotime($data['duedate']));
        if(!$duedate) throw new Exception('Invalid date format');
        
        $cohort_id = $request->getAttribute('cohort_id');
        
        $template = Atemplate::find($data['template_id']);
        if(!$template) throw new Exception('Invalid template id');
        
        //retrieve the cohor by slug or id
        $cohort = null;
        if(is_numeric($cohort_id)) $cohort = Cohort::find($cohort_id);
        else $cohort = Cohort::where('slug', $cohort_id)->first();
        if(!$cohort) throw new Exception('Invalid cohort id: '.$cohort_id);
        
        $teachers = $this->app->db->table('cohort_teacher')
        ->where([
            'cohort_teacher.cohort_id' => $cohort->id,
            'cohort_teacher.is_instructor' => true
        ])->select('cohort_teacher.teacher_user_id')->get();
        if(count($teachers)==0) throw new Exception('There cohort needs a main teacher in order to accept assignments');
        
        $students = $cohort->students()->get();
        foreach($students as $student) $this->_createStudentAssignment($student,$template,$teachers->first()->teacher_user_id,$duedate);
        
        return $this->success($response,'Ok');
    }
    
    public function updateAssignmentHandler(Request $request, Response $response) {
        $assignmentId = $request->getAttribute('assignment_id');
        $data = $request->getParsedBody();
        
        $assignment = Assignment::find($assignmentId);
        if(!$assignment) throw new Exception('Invalid assingment id');

        if(!in_array($data['status'],Assignment::$possibleStages)) throw new Exception("Invalid status ".$data['status'].", the only valid status are: ".implode(',',Assignment::$possibleStages));

        if($data['status'] == 'delivered') 
        {
            if(empty($data['github_url'])) throw new Exception('You need to specify a github URL to deliver an assignment');
            $assignment->github_url = $data['github_url'];
        }

        if($data['status'] == 'rejected') 
        {
            if(empty($data['reject_reason'])) throw new Exception('You need to specify a the reason for this rejection');
            $assignment->reject_reason = $data['reject_reason'];
        }
        
        if(isset($data['badges'])){
            foreach($data['badges'] as $slug => $points){
                $badge = Badge::where('slug', $slug)->first();
                if(!$badge) throw new Exception('The badge '.$slug.' does not exists and it is associateed to this project');
            }
        } 

        $savedBadges = [];
        if($data['status'] == 'reviewed') 
        {
            $student = $assignment->student()->get()->first();
            if(!$data['badges']) throw new Exception('You need to specify what badges have been earned by the student');
            
            try{
                foreach($data['badges'] as $slug => $points){
                    $stuff = ['name'=> $assignment->template->title, 'points'=>$points];
                    $savedBadges[] = $this->_updateStudentActivity($slug,$student, $stuff);
                }
            }
            catch(Exception $e)
            {
                $this->_deleteBadges($savedBadges, $student);
                throw $e;
            }
            
            $student->updateBasedOnActivity();
        }
        
        
        try{
            $assignment->status = $data['status'];
            $assignment->save();
        }
        catch(Exception $e){
            if($data['status'] == 'reviewed') $this->_deleteBadges($savedBadges, $student);
            throw $e;
        }
        
        return $this->success($response,$assignment);
    }
    
    public function deleteAssignmentHandler(Request $request, Response $response) {
        $assignmentId = $request->getAttribute('assignment_id');
        
        $assignment = Assignment::find($assignmentId);
        if(!$assignment) throw new Exception('Invalid assignment id');
        
        $assignment->delete();
        
        return $this->success($response,"The assignment was successfully deleted.");
    }
    
    private function _createStudentAssignment($student,$template,$teacherId,$duedate,$status='not-delivered'){
        
        $assignments = $this->app->db->table('assignments')
        ->where([
            'assignments.student_user_id' => $student->user_id,
            'assignments.atemplate_id' => $template->id
        ])->select('assignments.id')->get();
        if(count($assignments)>0) throw new Exception('There is already an assignment using this template and student');

        $assignment = new Assignment();
        $assignment->status = $status;
        $assignment->duedate = $duedate;
        $assignment->student()->associate($student->user_id);
        $assignment->teacher()->associate($teacherId);
        $assignment->template()->associate($template->id);
        $assignment->save();
        
        return $assignment;
    }
    
    private function _updateStudentActivity($badgeSlug, $student, $data){
        
        $badge = Badge::where('slug', $badgeSlug)->first();
        if(!$badge) throw new Exception('Invalid badge slug: '.$badgeSlug);
        
        $activity = new Activity();
        $activity->type = 'project';
        $activity->name = $data['name'];
        $activity->description = 'Assignment '.$data['name'].' was finished successfully!';
        $activity->points_earned = $data['points'];
        $activity->student()->associate($student);
        $activity->badge()->associate($badge);
        $activity->save();
        
        return $activity;
    }
    
    private function _deleteBadges($savedBadges, $student){
        foreach($savedBadges as $badge) $badge->delete();
        if(count($savedBadges)>0) $student->updateBasedOnActivity();
    }
    
}