<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Helpers\Mailer;

class UserHandler extends MainHandler{
    
    public function getMe(Request $request, Response $response) {
        
        $data = $request->getQueryParams();
        if(!empty($data['access_token']))
        {
            $storage = $this->app->storage;
            $user = $storage->getUserFromToken($data['access_token']);
            if(!empty($user) and isset($user['username'])) 
            {
                $bcUser = User::where('username', $user['username'])->first();
                if(!$bcUser) throw new Exception('This token does not correspond to any users');
                
                if($bcUser->type == 'student')
                {
                    return $this->success($response,$bcUser->student);
                }

                return $this->success($response,$bcUser);
                
            }else throw new Exception('This token does not correspond to any users');
        }else throw new Exception('No access_token provided');
        
        return $this->success($response,null);
        
    }   
    
    public function getUserHandler(Request $request, Response $response) {
        $breathecodeId = $request->getAttribute('user_id');
        
        if(is_numeric($breathecodeId)) $badge = User::find($breathecodeId);
        else $user = User::where('username', $breathecodeId)->first();
        if(!$user) throw new Exception('Invalid user email or id: '.$breathecodeId);
        
        return $this->success($response,$user);
    }
    
    public function syncUserHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');

        if(!isset($data['email'])) throw new Exception('You have to specify the user email');
        if(!in_array($data['type'],['teacher','student'])) throw new Exception('The user type has to be a "teacher" or "student", "'.$data['type'].'" given');
    
        $cohortIds = [];
        if(!isset($data['cohorts']) && $data['type']=='student') throw new Exception('You have to specify the user cohorts');
        
        if(isset($data['cohorts'])) foreach($data['cohorts'] as $cohortSlug){
            $auxCohort = Cohort::where('slug', $cohortSlug)->first();
            if(!$auxCohort) throw new Exception('The cohort '.$cohortSlug.' is invalid.');
            $cohortIds[] = $auxCohort->id;
        }
        
    
        $user = User::where('username', $data['email'])->first();
        if(!$user) $user = new User;
        
        $user->wp_id = $data['wp_id'];
        $user->type = $data['type'];
        $user->username = $data['email'];
        $user = $this->setOptional($user,$data,'full_name');
        $user->save();
        
        $studentOrTeacher = $this->generateStudentOrTeacher($user);
        
        $oldCohorts = $studentOrTeacher->cohorts()->get()->pluck('id');
        if(count($cohortIds)>0){
            $studentOrTeacher->cohorts()->detach($oldCohorts);
            $studentOrTeacher->cohorts()->attach($cohortIds);
        }
        
        if(isset($data['password']))
        {
            $storage = $this->app->storage;
            $oauthUser = $storage->setUserWithoutHash($data['email'], $data['password'], null, null);
            if(empty($oauthUser)){
                $user->delete();
                throw new Exception('Unable to create UserCredentials');
            }
        }

        return $this->success($response,$user);
    }    
    
    public function createCredentialsHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');

        if(!in_array($data['type'],['teacher','student'])) throw new Exception('The user type has to be a "teacher" or "student", "'.$data['type'].'" given');
    
        $user = User::where('username', $data['email'])->first();
        if(!$user)
        {
            $user = new User;
            $user->wp_id = $data['wp_id'];
            $user->username = $data['email'];
            $user->type = $data['type'];
            $user->save();
        }
        
        $storage = $this->app->storage;
        $oauthUser = $storage->setUserWithoutHash($data['email'], $data['password'], null, null);
        if(empty($oauthUser)){
            $user->delete();
            throw new Exception('Unable to create UserCredentials');
        }

        return $this->success($response,$user);
    }    
    
    public function updateCredentialsHandler(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id');
        if(empty($userId)) throw new Exception('There was an error retrieving the user_id');
        
        $data = $request->getParsedBody();
        if(empty($data) || empty($data['password'])) throw new Exception('You need to specify a password');

        $user = User::find($userId);
        if(!$user) throw new Exception('Invalid user id: '.$userId);
        
        $storage = $this->app->storage;
        $oauthUser = $storage->setUserWithoutHash($user->username, $data['password'], null, null);
        
        if(empty($oauthUser)) throw new Exception('Unable to update User credentials');

        return $this->success($response,$user);
    }    
    
    public function remindPassword(Request $request, Response $response) {
        $userEmail = $request->getAttribute('user_email');
        if(empty($userEmail)) throw new Exception('There was an error retrieving the user_email');
        
        else $user = User::where('username', $userEmail)->first();
        if(!$user) throw new Exception('Invalid user email: '.$userEmail);
        
        $storage = $this->app->storage;
        $newPassword = $this->randomPassword();
        
        $mailer = new Mailer();
        $result = $mailer->sendAPI("password_reminder", ["email"=> $user->username, "name"=> "Random User"]);
        
        if($result){
            $oauthUser = $storage->setUserWithoutHash($user->username, $newPassword, null, null);
            if(empty($oauthUser)) throw new Exception('Unable to update User credentials');
        }
        else throw new Exception('Unable to change password');

        return $this->success($response,$user);
    }    
    
    public function updateUserSettings(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id');
        $data = $request->getParsedBody();
        
        $user = User::find($userId);
        if(!$user) throw new Exception('Invalid student id: '.$userId);

        if(!$this->_validateSettings($data)) throw new Exception('Invalid user settings');
        
        $settings = $user->getUserSettings();
        foreach($data as $key => $val) $settings[$key] = $val;
        
        unset($settings["client_id"]);
        unset($settings["client_secret"]);
        unset($settings["access_token"]);
        $user->setUserSettings($settings);
        $user->save();
        
        return $this->success($response,$settings);
    }
    
    public function getUserSettings(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id');
        
        $user = User::find($userId);
        if(!$user) throw new Exception('Invalid student id: '.$userId);

        $settings = $user->getUserSettings();
        
        return $this->success($response,$settings);
    }
    
    public function deleteUser(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id');
        if(empty($userId)) throw new Exception('There was an error retrieving the user_id');
    
        $user = User::find($userId);
        if(!$user) throw new Exception('User not found');
        
        $user->delete();

        return $this->success($response,'The user was deleted successfully');
    }  
    
    private function generateStudentOrTeacher($user){

        switch($user->type)
        {
            case "teacher":
                $teacher = $user->teacher()->first();
                if(!$teacher)
                {
                    $teacher = new Teacher();
                    $user->teacher()->save($teacher);
                }
                return $teacher;
            break;
            case "student":
                $student = $user->student()->first();
                if(!$student)
                {
                    $student = new Student();
                    $user->student()->save($student);
                }
                return $student;
            break;
        }
    }
    
    private function _validateSettings($settings){
        
        return true;
        
        $settingsExample = [
            "notification-new-badge" => true
        ];
        
    }
    
    private function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}