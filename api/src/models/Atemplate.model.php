<?php

class Atemplate extends \Illuminate\Database\Eloquent\Model 
{
    public function assignments(){
        return $this->hasMany('Assignment');
    }
}