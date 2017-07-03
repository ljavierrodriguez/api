<?php

class Location extends \Illuminate\Database\Eloquent\Model 
{
    public function students(){
        return $this->hasMany('Student');
    }
}