<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class TeacherHandler extends MainHandler{
    
    protected $slug = 'Teacher';
    
    public function createTeacherHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $user = User::where('username', $data['email'])->first();
        if($user && $user->teacher) throw new Exception('There is already a user with this email on te API');
        else if(!$user)
        {
            $user = new User;
            $user->username = $data['email'];
            $user->type = 'teacher';
            $user->full_name = $data['full_name'];
            $user = $this->setOptional($user,$data,'wp_id');
            $user->save();
        }

        if($user)
        {
            $user = $this->setOptional($user,$data,'full_name');
            $user = $this->setOptional($user,$data,'avatar_url');
            $user = $this->setOptional($user,$data,'bio');
            $user->save();
            
            $teacher = new Teacher();
            $user->teacher()->save($teacher);
        }
        
        return $this->success($response,$teacher);
    }
    
    public function updateTeacherHandler(Request $request, Response $response) {
        $teacherId = $request->getAttribute('teacher_id');
        $data = $request->getParsedBody();
        
        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new Exception('Invalid teacher id: '.$teacherId);

        if($data['email']) throw new Exception('Teacher emails cannot be updated through this service');
        
        $teacher->user = $this->setOptional($teacher->user,$data,'wp_id');
        $teacher->user = $this->setOptional($teacher->user,$data,'full_name');
        $teacher->user = $this->setOptional($teacher->user,$data,'avatar_url');
        $teacher->user = $this->setOptional($teacher->user,$data,'bio');
        $teacher->user->save();
        
        return $this->success($response,$teacher);
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