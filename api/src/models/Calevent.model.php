<?php

class Calevent extends \Illuminate\Database\Eloquent\Model 
{
    //protected $appends = ['location_slug','events','cohort_slug'];
    public static $possibleTypes = ['holiday', 'community', 'academic'];
    
    public function calendar(){
        return $this->belongsTo('Calendar');
    }
    
    public function getCalendarSlugAttribute(){
        if($calendar = $this->calendar()->first()) return $calendar->slug;
        else return null;
    }
    
}