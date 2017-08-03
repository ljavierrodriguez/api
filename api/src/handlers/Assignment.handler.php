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
        
        $this->_createStudentAssignment($student,$template,$teacher,$data['duedate']);
        
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
        foreach($students as $student) $this->_createStudentAssignment($student,$template,$teachers->first(),$duedate);
        
        return $this->success($response,'Ok');
    }
    
    public function updateAssignmentHandler(Request $request, Response $response) {
        $assignmentId = $request->getAttribute('assignment_id');
        $data = $request->getParsedBody();
        
        $assignment = Assignment::find($assignmentId);
        if(!$assignment) throw new Exception('Invalid assingment id');

        if(!in_array($data['status'],['not-delivered', 'delivered', 'reviewed'])) throw new Exception("Invalid status ".$data['status'].", the only valid status are 'not-delivered', 'delivered' and 'reviewed'");

        if($data['status'] == 'delivered') 
        {
            if(!$data['github_url']) throw new Exception('You need to specify a github URL to deliver an assignment');
            $assignment->github_url = $data['github_url'];
        }
        
        $assignment->status = $data['status'];
        $assignment->save();
        
        return $this->success($response,$assignment);
    }
    
    public function deleteAssignmentHandler(Request $request, Response $response) {
        $assignmentId = $request->getAttribute('assignment_id');
        
        $assignment = Assignment::find($assignmentId);
        if(!$assignment) throw new Exception('Invalid assignment id');
        
        $assignment->delete();
        
        return $this->success($response,"The assignment was successfully deleted.");
    }
    
    private function _createStudentAssignment($student,$template,$teacher,$duedate){
        $assignments = $this->app->db->table('assignments')
        ->where([
            'assignments.student_user_id' => $student->user_id,
            'assignments.atemplate_id' => $template->id
        ])->select('assignments.id')->get();
        if(count($assignments)>0) throw new Exception('There is already an assignment for this student on template: '.$template->id);
        
        $assignment = new Assignment();
        $assignment->status = 'not-delivered';
        $assignment->duedate = $duedate;
        $assignment->student()->associate($student->user_id);
        $assignment->teacher()->associate($teacher->teacher_user_id);
        $assignment->template()->associate($template->id);
        $assignment->save();
        
        return $assignment;
    }
}