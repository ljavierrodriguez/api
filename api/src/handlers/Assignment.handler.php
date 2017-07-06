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
        
        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new Exception('Invalid teacher id');
        
        return $this->success($response,$teacher->assignments()->get());
    }
    
    public function createAssignmentHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $assignments = $this->app->db->table('assignments')
        ->where([
            'assignments.student_user_id' => $data['student_id'],
            'assignments.atemplate_id'=> $data['template_id']
        ])->select('assignments.id')->get();
        if(count($assignments)>0) throw new Exception('There is already an assignment for this student on this project');
        
        $template = Atemplate::find($data['template_id']);
        if(!$template) throw new Exception('Invalid template id');
        
        $student = Student::find($data['student_id']);
        if(!$student) throw new Exception('Invalid student id');
        
        $teacher = Teacher::find($data['teacher_id']);
        if(!$teacher) throw new Exception('Invalid teacher id');
        
        $assignment = new Assignment();
        $assignment->status = 'not-delivered';
        $assignment->project_slug = $template->project_slug;
        $assignment->student()->associate($student);
        $assignment->teacher()->associate($teacher);
        $assignment->template()->associate($template);
        $assignment->save();
        
        return $this->success($response,$assignment);
    }
    
    public function updateAssignmentHandler(Request $request, Response $response) {
        $assignmentId = $request->getAttribute('assignment_id');
        $data = $request->getParsedBody();
        
        $assignment = Assignment::find($assignmentId);
        if(!$assignment) throw new Exception('Invalid assingment id');

        if(!in_array($data['status'],['not-delivered', 'delivered', 'reviewed'])) throw new Exception("The only valid status are 'not-delivered', 'delivered' and 'reviewed'");
        
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
    
}