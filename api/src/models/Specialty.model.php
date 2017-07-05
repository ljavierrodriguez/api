<?php

class Specialty extends \Illuminate\Database\Eloquent\Model 
{
    protected $hidden = ['pivot'];
    protected $appends = ['url','total_points','badges'];
    public function getBadgesAttribute()
    {
        return $this->badges()->get()->pluck('slug');
    }
    public function getURLAttribute()
    {
        return '/specialty/'.$this->id;
    }
    public function getTotalPointsAttribute()
    {
        return $this->badges()->pluck('points_to_achieve')->sum();
    }
    /**
     * The products that belong to the shop.
     */
    public function students()
    {
        return $this->belongsToMany('Student','student_specialty')->withTimestamps();
    }
    
    public function badges()
    {
        return $this->belongsToMany('Badge','requierments')->withTimestamps();
    }
    
    public function profiles()
    {
        return $this->belongsToMany('Profile')->withTimestamps();
    }
}