<?php

class Cohort extends \Illuminate\Database\Eloquent\Model 
{
    public function students(){
        return $this->hasMany('Student');
    }

    public function location(){
        return $this->belongsTo('Location');
    }
}