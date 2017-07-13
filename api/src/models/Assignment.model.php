<?php

class Assignment extends \Illuminate\Database\Eloquent\Model 
{
    protected $hidden = ['student','teacher','atemplate_id'];
    protected $appends = ['template'];
    
    public function getTemplateAttribute(){
        return $this->template()->first();
    }
    
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