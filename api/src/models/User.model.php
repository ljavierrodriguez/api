<?php

class User extends \Illuminate\Database\Eloquent\Model 
{
    public function students(){
        return $this->hasOne('Student');
    }
    
    public function teacher(){
        return $this->hasOne('Student');
    }
}