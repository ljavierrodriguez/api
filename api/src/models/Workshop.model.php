<?php

class Workshop extends \Illuminate\Database\Eloquent\Model 
{
    protected $hidden = ['pivot'];
    protected $appends = ['location_slug', 'wtemplate_slug'];
    public function getLocationSlugAttribute(){
        if($location = $this->location()->first()) return $location->slug;
        else return null;
    }
    public function getWtemplateSlugAttribute(){
        if($wtemplate = $this->wtemplate()->first()) return $wtemplate->slug;
        else return null;
    }
    
    public function students(){
        $students = $this->belongsToMany('Student','workshop_student','workshop_id','student_user_id')->withTimestamps();
        return $students;
    }

    public function location(){
        return $this->belongsTo('Location');
    }

    public function wtemplate(){
        return $this->belongsTo('Wtemplate');
    }
    /*
    TODO: add teachers to the cohorts
    public function teachers(){
        
        $teachers = $this->belongsToMany('Teacher','cohort_teacher','cohort_id','teacher_user_id')->withPivot('is_instructor')->withTimestamps();
        return $teachers;
    }
    
    */
    
    
}