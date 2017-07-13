<?php

class Teacher extends \Illuminate\Database\Eloquent\Model 
{
    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $hidden = ['user_id','user','updated_at'];
    protected $appends = ['url','id','wp_id','cohorts','full_name','avatar_url','bio'];
    
    public function getAvatarURLAttribute(){
        if($this->user) return $this->user->avatar_url;
        else null;
    }
        
    public function getBioAttribute(){
        if($this->user) return $this->user->bio;
        else null;
    }
    
    public function getFullNameAttribute(){
        if($this->user) return $this->user->full_name;
        else null;
    }
    
    public function getWPIdAttribute(){
        if($this->user) return $this->user->wp_id;
        else null;
    }
    
    public function getIdAttribute(){
        return $this->user_id;
    }
    
    public function getURLAttribute(){
        return '/teacher/'.$this->id;
    }
    
    public function getCohortsAttribute(){
        return $this->cohorts()->get()->pluck('slug');
    }
    
    public function user(){
        return $this->belongsTo('User');
    }
    
    public function cohorts(){
        return $this->belongsToMany('Cohort')->withTimestamps();
    }
    
    public function assignments(){
        return $this->hasMany('Assignment');
    }
    
}