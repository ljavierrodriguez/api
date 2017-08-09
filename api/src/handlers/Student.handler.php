<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Carbon\Carbon;

class StudentHandler extends MainHandler{
    
    protected $slug = 'Student';
    
    public function getStudentHandler(Request $request, Response $response) {
        $breathecodeId = $request->getAttribute('student_id');
        
        $user = User::find($breathecodeId);
        if(!$user or !$user->student) throw new Exception('Invalid student_id');
        
        return $this->success($response,$user->student);
    }
    
    public function getStudentActivityHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $activities = Activity::where('student_user_id', $studentId)->get();
        if(!$activities) throw new Exception('Invalid student id:'.$studentId);
        
        return $this->success($response,$activities);
    }
    
    public function getStudentBriefing(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        
        $student = Student::find($studentId);
        if(!$student) throw new Exception('Invalid student id: '.$studentId);
        
        $acumulatedPoints = $this->app->db->table('activities')->where([
            'activities.student_user_id' => $studentId
        ])->sum('activities.points_earned');
        $result['acumulated_points'] = $acumulatedPoints;
        
        $creation = $student->created_at;
        $result['creation_date'] = $creation->format('Y-m-d');

        $count = $this->_daysBetween($creation);
        $result['days'] = $count;
        
        $profile = Profile::find(1);
        $result['profile'] = $profile;
        
        return $this->success($response,$result);
    }
    
    public function createStudentHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $cohort = Cohort::where('slug', $data['cohort_slug'])->first();
        if(!$cohort) throw new Exception('Invalid cohort slug');
        
        $user = User::where('username', $data['email'])->first();
        if($user && $user->student) throw new Exception('There is already a student with this email on te API');
        else if(!$user)
        {
            $user = new User;
            $user->username = $data['email'];
            $user->type = 'student';
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
            
            $student = new Student();
            $student = $this->setOptional($student,$data,'total_points');
            $user->student()->save($student);
            $student->cohorts()->save($cohort);
            
        }
        
        return $this->success($response,$student);
    }
    
    public function updateStudentHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');
        $data = $request->getParsedBody();
        
        $student = Student::find($studentId);
        if(!$student) throw new Exception('Invalid student id: '.$studentId);

        if($data['email']) throw new Exception('Students emails cannot be updated through this service');
        
        $user = $student->user;
        $user = $this->setOptional($user,$data,'full_name');
        $user = $this->setOptional($user,$data,'avatar_url');
        $user = $this->setOptional($user,$data,'description');
        $user->save();
        $student = $this->setOptional($student,$data,'total_points');
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
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
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
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new Exception('The student is to old to delete');
        
        $student->activities()->delete();
        $student->badges()->delete();
        $student->delete();
        
        return $this->success($response,"ok");
    }
    
    private function _daysBetween($date1, $date2=null){
        
        if(!$date2) $date2 = Carbon::now('America/Vancouver');
        return $date1->diffInDays($date2);
    }
    
}