<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Helpers\BCValidator;

class ProfileHandler extends MainHandler{
    
    protected $slug = 'Profile';
    
    public function createProfileHandler(Request $request, Response $response) {
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        if(count($data['specialties'])<2) throw new Exception('A profile must be created with at least two specialties');
        
        $profile = new Profile();
        $specialties = [];
        foreach($data['specialties'] as $bslug)
        {
            $specialty = Specialty::where('slug', $bslug)->first();
            if($specialty) $specialties[] = $specialty->id;
            else throw new Exception('Invalid specialty slug: '.$bslug);
        }
        $profile = $this->setMandatory($profile,$data,'slug',BCValidator::SLUG);
        $profile = $this->setMandatory($profile,$data,'name',BCValidator::NAME);
        $profile = $this->setMandatory($profile,$data,'description',BCValidator::DESCRIPTION);
        $profile->save();
        
        try{
            $profile->specialties()->attach($specialties);
        }
        catch(Exception $e)
        {
            $profile->delete();
            throw new Exception($e->getMessage());
        }
        
        return $this->success($response,$profile);
    }
    
    public function updateProfileHandler(Request $request, Response $response) {
        $profileId = $request->getAttribute('profile_id');
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        if(is_numeric($profileId)) $profile = Profile::find($profileId);
        else $profile = Profile::where('slug', $profileId)->first();
        if(!$profile) throw new Exception('Invalid profile slug or id: '.$profileId);
        
        if(is_numeric($profileId)) $profile->slug = $data['slug'];
        $profile = $this->setOptional($profile,$data,'slug',BCValidator::SLUG);
        $profile = $this->setOptional($profile,$data,'name',BCValidator::NAME);
        $profile = $this->setOptional($profile,$data,'description',BCValidator::DESCRIPTION);
        
        if(isset($data['specialties']))
        {
            if(count($data['specialties'])<2) throw new Exception('A profile must have at least two specialties');
            $specialties = [];
            foreach($data['specialties'] as $bslug)
            {
                $specialty = Specialty::where('slug', $bslug)->first();
                if($specialty) $specialties[] = $specialty->id;
                else throw new Exception('Invalid specialty slug: '.$bslug);
            }
            
            $currentSpecialties = $profile->specialties()->get();
            $profile->specialties()->detach($currentSpecialties);
            $profile->specialties()->attach($specialties);
        }
        
        $profile->save();
        
        return $this->success($response,$profile);
    }

    public function deleteProfileHandler(Request $request, Response $response) {
        $profileId = $request->getAttribute('profile_id');
        
        $profile = Profile::find($profileId);
        if(!$profile) throw new Exception('Invalid profile id: '.$profileId);
        
        $specialties = $profile->specialties()->get();
        if(count($specialties)>2) throw new Exception('Only profiles wit 2 or less specialties can be removed, please remove specialties from this profile first');
        
        $cohorts = $profile->cohorts()->get();
        if(count($cohorts)>0) throw new Exception('Only profiles with no cohorts can be deleted');
        /*
        $attributes = $specialty->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new Exception('The specialty is too old to delete');
        */
        
        $currentSpecialties = $profile->specialties()->get();
        $profile->specialties()->detach($currentSpecialties);
        $profile->delete();
        
        return $this->success($response,"ok");
    }
    
}