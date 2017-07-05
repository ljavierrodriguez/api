<?php

class Location extends \Illuminate\Database\Eloquent\Model 
{
    public function cohorts(){
        return $this->hasMany('Cohort');
    }
}