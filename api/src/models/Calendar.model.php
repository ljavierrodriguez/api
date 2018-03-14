<?php

class Calendar extends \Illuminate\Database\Eloquent\Model 
{
    protected $hidden = ['cohort'];
    protected $appends = ['location_slug','events','cohort_slug'];
    
    public function cohort(){
        return $this->belongsTo('Cohort');
    }
    
    
    public function location(){
        return $this->belongsTo('Location');
    }
    
    public function events(){
        return $this->hasMany('Calevent');
    }

    public function getEventsAttribute(){
        return $this->events()->get();
    }
    
    public function getLocationSlugAttribute(){
        if($location = $this->location()->first()) return $location->slug;
        else return null;
    }
    
    public function getCohortSlugAttribute(){
        if($cohort = $this->cohort()->first()) return $cohort->slug;
        else return null;
    }
    
}