<?php

class Requierments extends \Illuminate\Database\Eloquent\Model 
{
    public function badges()
    {
        return $this->belongsToMany('Badge','requierments')->withTimestamps();
    }
    
    public function specialties()
    {
        return $this->belongsToMany('Specialty','requierments')->withTimestamps();
    }
}