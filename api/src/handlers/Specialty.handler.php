<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class SpecialtyHandler extends MainHandler{
    
    public function getStudentSpecialtiesHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');

        $specialties = Student::find($studentId)->specialties()->get();
        if(!$specialties) throw new Exception('Invalid student id');
        
        return $this->success($response,$specialties);
    }
    
    public function getProfileSpecialtiesHandler(Request $request, Response $response) {
        $profileId = $request->getAttribute('profile_id');

        $specialties = Profile::find($profileId)->specialties()->get();
        if(!$specialties) throw new Exception('Invalid profile id');
        
        return $this->success($response,$specialties);
    }
    
    public function getSpecialtyHandler(Request $request, Response $response) {
        $specialtyId = $request->getAttribute('specialty_id');
        
        $specialty = Specialty::find($specialtyId);
        if(!$specialty) throw new Exception('Invalid specialty id');
        
        return $this->success($response,$specialty);
    }
    
    public function createOrUpdateSpecialtyHandler(Request $request, Response $response) {
        $specialtyId = $request->getAttribute('specialty_id');
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $profile = Profile::where('slug', $data['profile_slug'])->first();
        if(!$profile) throw new Exception('Invalid profile slug');
        
        if($specialtyId){
            $specialty = Specialty::find($specialtyId);
            if(!$specialty) throw new Exception('Invalid specialty id: '.$specialtyId);
            
            $specialty = $this->setOptional($specialty,$data,'slug');
            $specialty = $this->setOptional($specialty,$data,'name');
            $specialty = $this->setOptional($specialty,$data,'image_url');
            $specialty = $this->setOptional($specialty,$data,'points_to_achieve');
            $specialty = $this->setOptional($specialty,$data,'description');
        } 
        else{
            if(count($data['badges'])<2) throw new Exception('A specialty must be created with at least two badges');
            
            $specialty = new Specialty();
            $badges = [];
            foreach($data['badges'] as $bslug)
            {
                $badge = Badge::where('slug', $bslug)->first();
                if($badge) $badges[] = $badge;
                else throw new Exception('Invalid badge: '.$bslug);
            }
            $specialty->slug = $data['slug'];
            $specialty->name = $data['name'];
            $specialty->image_url = $data['image_url'];
            $specialty->points_to_achieve = $data['points_to_achieve'];
            $specialty->description = $data['description'];
            $specialty->save();
            $specialty->profiles()->attach($profile);
            $specialty->badges()->attach($badges);
        }
        
        $specialty->save();
        
        return $this->success($response,$specialty);
    }

    public function deleteSpecialtyHandler(Request $request, Response $response) {
        $specialtyId = $request->getAttribute('specialty_id');
        
        $specialty = Specialty::find($specialtyId);
        if(!$specialty) throw new Exception('Invalid specialty id: '.$specialtyId);
        
        $attributes = $badge->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / (60 * 60 * 24));
        if($daysOld>5) throw new Exception('The specialty is too old to delete');
        $specialty->delete();
        
        return $this->success($response,"ok");
    }
    
}