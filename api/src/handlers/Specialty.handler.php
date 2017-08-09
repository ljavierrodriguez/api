<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class SpecialtyHandler extends MainHandler{
    
    protected $slug = 'Specialty';
    
    public function getStudentSpecialtiesHandler(Request $request, Response $response) {
        $studentId = $request->getAttribute('student_id');

        $student = Student::find($studentId);
        if(!$student) throw new Exception('Student not found');
        
        $specialties = $student->specialties()->get();
        if(!$specialties) throw new Exception('Invalid student id');
        
        return $this->success($response,$specialties);
    }
    
    public function getProfileSpecialtiesHandler(Request $request, Response $response) {
        $profileId = $request->getAttribute('profile_id');

        $profile = null;
        if(is_numeric($profileId)) $profile = Profile::find($profileId);
        else $profile = Profile::where('slug', $profileId)->first();
        if(!$profile) throw new Exception('Invalid profile id: '.$profileId);
        
        $specialties = $profile->specialties()->get();
        //if(!$specialties) throw new Exception('Invalid profile id');
        
        return $this->success($response,$specialties);
    }
    
    public function createSpecialtyHandler(Request $request, Response $response) {
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        $profile = Profile::where('slug', $data['profile_slug'])->first();
        if(!$profile) throw new Exception('Invalid profile slug');
        
        if(count($data['badges'])<2) throw new Exception('A specialty must be created with at least two badges');
        
        $specialty = new Specialty();
        $badges = [];
        foreach($data['badges'] as $bslug)
        {
            $badge = Badge::where('slug', $bslug)->first();
            if($badge) $badges[] = $badge->id;
            else throw new Exception('Invalid badge: '.$bslug);
        }
        $specialty->slug = $data['slug'];
        $specialty->name = $data['name'];
        $specialty->image_url = $data['image_url'];
        $specialty->points_to_achieve = $data['points_to_achieve'];
        $specialty->description = $data['description'];
        $specialty->save();
        
        try{
            $specialty->profiles()->attach($profile);
            $specialty->badges()->attach($badges);
        }
        catch(Exception $e)
        {
            $specialty->delete();
            throw new Exception($e->getMessage());
        }
        
        return $this->success($response,$specialty);
    }
    
    public function updateSpecialtyHandler(Request $request, Response $response) {
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
        
        $badges = $specialty->badges()->get();
        if(count($badges)>0) throw new Exception('Remove all specialty badges first');
        
        $attributes = $specialty->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new Exception('The specialty is too old to delete');
        $specialty->delete();
        
        return $this->success($response,"ok");
    }
    
}