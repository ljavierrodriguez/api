<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Helpers\ArgumentException;

class TeacherHandler extends MainHandler{
    
    protected $slug = 'Teacher';
    
    public function getCohortTeachers(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');

        //retrieve the cohort by slug or id
        $cohort = null;
        if(is_numeric($cohortId)) $cohort = Cohort::find($cohortId);
        else $cohort = Cohort::where('slug', $cohort_id)->first();
        if(!$cohort) throw new ArgumentException('Invalid cohort id: '.$cohortId);
        
        return $this->success($response,$cohort->teachers()->get());
    }
    
    public function createTeacherHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new ArgumentException('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $user = User::where('username', $data['email'])->first();
        if($user && $user->teacher) throw new ArgumentException('There is already a user with this email on te API');
        $created = false;
        if(!$user)
        {
            $user = new User;
            $user->username = $data['email'];
            $user->type = 'teacher';
            $user->full_name = $data['full_name'];
            $user = $this->setOptional($user,$data,'wp_id');
            $user->save();
            $created = true;
        }
        
        if($user)
        {
            if(!$created)
            {
                $user = $this->setOptional($user,$data,'full_name');
                $user = $this->setOptional($user,$data,'avatar_url');
                $user = $this->setOptional($user,$data,'bio');
                $user->save();
            }
            
            $teacher = $user->teacher()->get()->first();
            if(!$teacher)
            {
                $teacher = new Teacher();
                $user->teacher()->save($teacher);
            }
        }
        
        return $this->success($response,$teacher);
    }
    
    public function updateTeacherHandler(Request $request, Response $response) {
        $teacherId = $request->getAttribute('teacher_id');
        $data = $request->getParsedBody();
        
        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new ArgumentException('Invalid teacher id: '.$teacherId);

        if($data['email']) throw new ArgumentException('Teacher emails cannot be updated through this service');
        
        $teacher->user = $this->setOptional($teacher->user,$data,'wp_id');
        $teacher->user = $this->setOptional($teacher->user,$data,'full_name');
        $teacher->user = $this->setOptional($teacher->user,$data,'avatar_url');
        $teacher->user = $this->setOptional($teacher->user,$data,'bio');
        $teacher->user->save();
        
        return $this->success($response,$teacher);
    }
    
    public function deleteTeacherHandler(Request $request, Response $response) {
        $teacherId = $request->getAttribute('teacher_id');
        
        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new ArgumentException('Invalid teacher id');
        
        /*
        $attributes = $teacher->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new ArgumentException('The teacher is to old to delete');
        */
        $teacher->delete();
        
        return $this->success($response,"ok");
    }
    
}