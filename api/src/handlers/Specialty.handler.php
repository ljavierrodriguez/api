<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Helpers\BCValidator;

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
        $specialty = $this->setMandatory($specialty,$data,'slug',BCValidator::SLUG);
        $specialty = $this->setMandatory($specialty,$data,'name',BCValidator::NAME);
        $specialty = $this->setMandatory($specialty,$data,'points_to_achieve',BCValidator::POINTS);
        $specialty = $this->setMandatory($specialty,$data,'description',BCValidator::DESCRIPTION);
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
        
        if(is_numeric($specialtyId)) $specialty = Specialty::find($specialtyId);
        else $specialty = Specialty::where('slug', $specialtyId)->first();
        if(!$specialty) throw new Exception('Invalid specialty slug or id: '.$specialtyId);
        
        if(is_numeric($specialtyId)) $specialty->slug = $data['slug'];
        $specialty = $this->setOptional($specialty,$data,'slug',BCValidator::SLUG);
        $specialty = $this->setOptional($specialty,$data,'name',BCValidator::NAME);
        $specialty = $this->setOptional($specialty,$data,'points_to_achieve',BCValidator::POINTS);
        $specialty = $this->setOptional($specialty,$data,'description',BCValidator::DESCRIPTION);

        if(isset($data['badges']))
        {
            if(count($data['badges'])<2) throw new Exception('A specialty must have at least two badges');
            $badges = [];
            foreach($data['badges'] as $bslug)
            {
                $badge = Badge::where('slug', $bslug)->first();
                if($badge) $badges[] = $badge->id;
                else throw new Exception('Invalid badge: '.$bslug);
            }
            
            $currentBadges = $specialty->badges()->get();
            $specialty->badges()->detach($currentBadges);
            $specialty->badges()->attach($badges);
        }
        
        $currentProfiles = $specialty->profiles()->get();
        $specialty->profiles()->detach($currentProfiles);
        $specialty->profiles()->attach($profile);
        $specialty->save();
        
        return $this->success($response,$specialty);
    }

    public function deleteSpecialtyHandler(Request $request, Response $response) {
        $specialtyId = $request->getAttribute('specialty_id');
        
        $specialty = Specialty::find($specialtyId);
        if(!$specialty) throw new Exception('Invalid specialty id: '.$specialtyId);
        
        $badges = $specialty->badges()->get();
        if(count($badges)>2) throw new Exception('Only specialties with 2 or less badges can be removed, please remove badges from this specialty first');

        /*
        $attributes = $specialty->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new Exception('The specialty is too old to delete');
        */
        $currentBadges = $specialty->badges()->get();
        $specialty->badges()->detach($currentBadges);
        $specialty->delete();
        
        return $this->success($response,"ok");
    }
    
    private function uploadThumb($badge,$request){
        $files = $request->getUploadedFiles();
        //print_r($files); die();
        if (empty($files['thumb'])) return false;

        if(is_dir(PUBLIC_URL))
        {
            if(!is_dir(PUBLIC_URL.'img/')) mkdir(PUBLIC_URL.'img/');
            if(!is_dir(PUBLIC_URL.'img/specialty/')) mkdir(PUBLIC_URL.'img/specialty/');

            $destination = PUBLIC_URL.'img/specialty/';
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
        $specialtyId = $request->getAttribute('specialty_id');
        
        $specialty = Specialty::find($specialtyId);
        if(!$specialty) throw new Exception('Invalid specialty id: '.$specialtyId);
        
        $imageUrl = $this->uploadThumb($specialty,$request);
        if(empty($imageUrl)) throw new Exception('Unable to upload thumb');
        $specialty->icon = substr($imageUrl,2);
        $specialty->save();
        
        return $this->success($response,$specialty);
    }
    
    public function addSpecialtiesToProfileHandler(Request $request, Response $response) {
        $profileId = $request->getAttribute('profile_id');
        
        $specialtiesObj = $request->getParsedBody();
        if(empty($specialtiesObj['specialties'])) throw new Exception('There was an error retrieving the badges');
        
        $profile = Profile::find($profileId);
        if(!$profile) throw new Exception('Invalid specialty id: '.$profileId);
        
        $definitiveSpecialties = [];
        $currentSpecialties = $profile->specialties()->get();
        foreach($specialtiesObj['specialties'] as $specialtyId) {
            $specialty = Specialty::find($specialtyId);
            if(!$specialty) throw new Exception('Invalid specialty id: '.$specialtyId);
            if(!$currentSpecialties->contains($specialtyId)) $definitiveSpecialties[] = $specialtyId;
        }
        
        if($definitiveSpecialties>0) $profile->specialties()->attach($definitiveSpecialties);
        
        return $this->success($response,$profile);
    }
    
    public function deleteSpecialtiesFromProfileHandler(Request $request, Response $response) {
        $profileId = $request->getAttribute('profile_id');
        
        $specialtiesObj = $request->getParsedBody();
        if(empty($specialtiesObj['specialties'])) throw new Exception('There was an error retrieving the specialties');
        foreach($specialtiesObj['specialties'] as $specialtyId)
        {
            $specialty = Specialty::find($specialtyId);
            if(!$specialty) throw new Exception('There is no specialty with ID '.$specialtyId);
        }
        
        $profile = Profile::find($profileId);
        if(!$profile) throw new Exception('Invalid profile id: '.$profileId);
        
        if($specialtiesObj['specialties']>0) $profile->specialties()->detach($specialtiesObj['specialties']);
        else throw new Exception('The specialties array is empty');
        
        return $this->success($response,$profile);
    }
}