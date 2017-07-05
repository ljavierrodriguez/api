<?php

class Cohort extends \Illuminate\Database\Eloquent\Model 
{
    protected $hidden = ['pivot'];
    protected $appends = ['location_slug'];
    
    public function getLocationSlugAttribute()
    {
        if($location = $this->location()->first()) return $location->slug;
        else return null;
    }
    
    public function students(){
        $students = $this->belongsToMany('Student','cohort_student','cohort_id','student_user_id')->withTimestamps();
        return $students;
    }

    public function location(){
        return $this->belongsTo('Location');
    }
    
    public function teachers()
    {
        return $this->hasMany('Teacher','user_id');
    }
    
    
}