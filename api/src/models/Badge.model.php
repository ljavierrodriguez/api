<?php


class Badge extends \Illuminate\Database\Eloquent\Model  
{
    protected $appends = ['url'];
    protected $hidden = ['pivot'];
    
    public function getURLAttribute()
    {
        return '/badge/'.$this->id;
    }
    
    public function getImageUrlAttribute()
    {
        if(empty($this->image_url)) return "/public/img/badge/rand/chevron-".rand(1,21).".png";
        else return $this->image_url;
    }
    /**
     * The products that belong to the shop.
     */
    public function students()
    {
        return $this->belongsToMany('Student')->withPivot('points_acumulated')->withTimestamps();
    }
    
    /**
     * The products that belong to the shop.
     */
    public function activities()
    {
        return $this->hasMany('Activity');
    }
    
    public function specialties()
    {
        return $this->belongsToMany('Specialty','requierments')->withTimestamps();
    }
}