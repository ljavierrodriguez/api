<?php

class Location extends \Illuminate\Database\Eloquent\Model 
{
    public function cohorts(){
        return $this->hasMany('Cohort');
    }
    
    public function workshops(){
        return $this->hasMany('Workshop');
    }
}