<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class BadgeHandler extends MainHandler{
    
    protected $slug = 'Badge';
    
    public function getSingleBadge(Request $request, Response $response) {
        $id = $request->getAttribute('badge_id');
        
        $single = null;
        if(is_numeric($id)) $single = Badge::find($id);
        else $single = Badge::where('slug', $id)->first();
        
        if(!$single) throw new Exception('Invalid '.strtolower($this->slug).'_id');
        
        return $this->success($response,$single);
    }

    public function getAllStudentBadgesHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');

        $badges = $this->app->db->table('badges')
        ->join('badge_student','badge_student.badge_id','=','badges.id')
        ->where('badge_student.student_user_id',$studentId)
        ->select('badges.*','badge_student.is_achieved','badge_student.points_acumulated')->get();
        if(!$badges) throw new Exception('Invalid student id');
        
        return $this->success($response,$badges);
    }
    
    public function createOrUpdateBadgeHandler(Request $request, Response $response) {
        $badgeId = $request->getAttribute('badge_id');
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        if($badgeId){
            $badge = Badge::find($badgeId);
            if(!$badge) throw new Exception('Invalid badge id: '.$badgeId);
            
            $badge = $this->setOptional($badge,$data,'slug');
            $badge = $this->setOptional($badge,$data,'name');
            $badge = $this->setOptional($badge,$data,'technologies');
            $badge = $this->setOptional($badge,$data,'description');
            $badge = $this->setOptional($badge,$data,'points_to_achieve');
        } 
        else{
            
            $imageUrl = $this->uploadThumb($badge,$request);
            if(!$imageUrl) $imageUrl = PUBLIC_URL.'img/badge/rand/chevron-'.rand(1,21).'.png';
            
            $badge = new Badge();
            $badge->slug = $data['slug'];
            $badge->name = $data['name'];
            $badge->image_url = $imageUrl;
            $badge->points_to_achieve = $data['points_to_achieve'];
            $badge->description = $data['description'];
            $badge->technologies = $data['technologies'];
        }
        $badge->save();
        
        return $this->success($response,$badge);
    }
    
    private function uploadThumb($badge,$request){
        $files = $request->getUploadedFiles();
        //print_r($files); die();
        if (empty($files['thumb'])) return false;

        if(is_dir(PUBLIC_URL))
        {
            if(!is_dir(PUBLIC_URL.'img/')) mkdir(PUBLIC_URL.'img/');
            if(!is_dir(PUBLIC_URL.'img/badge/')) mkdir(PUBLIC_URL.'img/badge/');

            $destination = PUBLIC_URL.'img/badge/';
            if(is_dir($destination))
            {
                $newfile = $files['thumb'];
                
                $oldName = $newfile->getClientFilename();
                $name_parts = explode(".", $oldName);
                $ext = end($name_parts);
                if(!in_array($ext, VALID_IMG_EXTENSIONS)) throw new Exception('Invalid image thumb extension: '.$ext);
                
                $newURL = $destination.$badge->slug.'.'.$ext;
                //print_r($newURL); die();
                $newfile->moveTo($newURL);
                return $newURL;
                
            }else throw new Exception('Invalid thumb file destination: '.$destination);
            
        }else throw new Exception('Invalid PUBLIC_URL destination: '.PUBLIC_URL);
        
        return false;
    }
    
    public function updateThumbHandler(Request $request, Response $response) {
        $badgeId = $request->getAttribute('badge_id');
        
        $badge = Badge::find($badgeId);
        if(!$badge) throw new Exception('Invalid badge id: '.$badgeId);
        
        $imageUrl = $this->uploadThumb($badge,$request);
        if(empty($imageUrl)) throw new Exception('Unable to upload thumb');
        
        $badge->image_url = $imageUrl;
        $badge->save();
        
        return $this->success($response,"Thumb updated");
    }

    public function deleteBadgeHandler(Request $request, Response $response) {
        $badgeId = $request->getAttribute('badge_id');
        
        $badge = Badge::find($badgeId);
        if(!$badge) throw new Exception('Invalid badge id: '.$badgeId);
        
        $attributes = $badge->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new Exception('The badge is too old to delete');
        $badge->delete();
        
        return $this->success($response,"ok");
    }
    
}