<?php

class Assignment extends \Illuminate\Database\Eloquent\Model 
{
    protected $hidden = ['student','teacher','template'];
    
    public function student(){
        return $this->belongsTo('Student');
    }
    
    public function teacher(){
        return $this->belongsTo('Teacher');
    }
    
    public function template()
    {
        return $this->belongsTo('Atemplate','atemplate_id');
    }
}