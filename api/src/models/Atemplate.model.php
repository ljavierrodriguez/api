<?php

class Atemplate extends \Illuminate\Database\Eloquent\Model 
{
    
    public static $possibleDifficulties = ['not-defined', 'begginer', 'junior','intermediate','semi-senior','senior'];
    
    public function assignments(){
        return $this->hasMany('Assignment');
    }
}