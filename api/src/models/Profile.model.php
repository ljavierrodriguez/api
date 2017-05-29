<?php

class Profile extends \Illuminate\Database\Eloquent\Model 
{
    public function specialties()
    {
        return $this->belongsToMany('Specialty')->withTimestamps();
    }
}