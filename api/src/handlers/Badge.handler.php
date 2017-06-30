<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class BadgeHandler extends MainHandler{
    
    public function getAllStudentBadgesHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');

        $badges = $this->app->db->table('badges')
        ->join('badge_student','badge_student.badge_id','=','badges.id')
        ->where('badge_student.student_id',$studentId)
        ->select('badges.*','badge_student.is_achieved','badge_student.points_acumulated')->get();
        if(!$badges) throw new Exception('Invalid student id');
        
        return $this->success($response,$badges);
    }
    
    public function getBadgeHandler(Request $request, Response $response) {
        $badgeId = $request->getAttribute('badge_id');
        
        $badges = Badge::find($badgeId);
        if(!$badges) throw new Exception('Invalid badge id');
        
        return $this->success($response,$badges);
    }
    
    public function getAllBadgesHandler(Request $request, Response $response) {
        $badges = Badge::all();
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
            $badge = $this->setOptional($badge,$data,'image_url');
            $badge = $this->setOptional($badge,$data,'technologies');
            $badge = $this->setOptional($badge,$data,'description');
            $badge = $this->setOptional($badge,$data,'points_to_achieve');
        } 
        else{
            $badge = new Badge();
            $badge->slug = $data['slug'];
            $badge->name = $data['name'];
            $badge->image_url = $data['image_url'];
            $badge->points_to_achieve = $data['points_to_achieve'];
            $badge->description = $data['description'];
            $badge->technologies = $data['technologies'];
        }
        $badge->save();
        
        return $this->success($response,$badge);
    }

    public function deleteBadgeHandler(Request $request, Response $response) {
        $badgeId = $request->getAttribute('badge_id');
        
        $badge = Badge::find($badgeId);
        if(!$badge) throw new Exception('Invalid badge id: '.$badgeId);
        
        $attributes = $badge->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / (60 * 60 * 24));
        if($daysOld>5) throw new Exception('The badge is too old to delete');
        $badge->delete();
        
        return $this->success($response,"ok");
    }
    
}