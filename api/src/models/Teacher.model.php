<?php

class Teacher extends \Illuminate\Database\Eloquent\Model 
{
    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $hidden = ['user_id'];
    protected $appends = ['url','id'];
    
    public function getIdAttribute(){
        return $this->user_id;
    }
    
    public function getURLAttribute(){
        return '/teacher/'.$this->id;
    }
    
    public function user(){
        return $this->belongsTo('User');
    }
    
    public function cohorts(){
        return $this->belongsToMany('Cohort')->withTimestamps();
    }
    
}