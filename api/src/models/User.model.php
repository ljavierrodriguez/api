<?php

class User extends \Illuminate\Database\Eloquent\Model 
{
    public function student(){
        return $this->hasOne('Student');
    }
    
    public function teacher(){
        return $this->hasOne('Teacher');
    }
}