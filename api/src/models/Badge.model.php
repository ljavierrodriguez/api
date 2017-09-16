<?php


class Badge extends \Illuminate\Database\Eloquent\Model  
{
    protected $appends = ['specialties','url','image_url'];
    protected $hidden = ['pivot', 'icon'];
    
    public function getURLAttribute()
    {
        return '/badge/'.$this->id;
    }

    public function getImageUrlAttribute($value)
    {
        if(empty($this->icon)) return "/public/img/badge/rand/chevron-".rand(1,21).".png";
        else return $this->icon;
    }
    

    public function getSpecialtiesAttribute($value)
    {
        return $this->specialties()->get()->pluck('slug');
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