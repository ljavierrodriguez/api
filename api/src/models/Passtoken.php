<?php

class Passtoken extends \Illuminate\Database\Eloquent\Model 
{
    public function user(){
        
        return $this->belongsTo('User');
    }
}