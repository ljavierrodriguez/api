<?php

class Profile extends \Illuminate\Database\Eloquent\Model 
{
    protected $appends = ['total_points','specialties'];
    
    public function getSpecialtiesAttribute(){
        return $this->specialties()->get()->pluck('slug');
    }
    
    public function getTotalPointsAttribute(){
        
        $totalPoints = 0;
        $specialties = $this->specialties()->get();
        foreach($specialties as $sp)
        {
            $includedBadges = [];
            $specialtyBadges = $sp->badges()->get();
            foreach($specialtyBadges as $bg){
                if(!isset($includedBadges[$bg->slug])){
                    $totalPoints += $bg->points_to_achieve;
                    $includedBadges[$bg->slug] = $bg->points_to_achieve;
                }
            }
        }
        
        return $totalPoints;
    }
    
    public function specialties(){
        
        return $this->belongsToMany('Specialty')->withTimestamps();
    }
    
    public function cohorts(){
        
        return $this->hasMany('Cohort');
    }
}