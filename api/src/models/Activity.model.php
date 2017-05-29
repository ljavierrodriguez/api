<?php

class Activity extends \Illuminate\Database\Eloquent\Model 
{

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    protected $appends = ['badge_slug','url'];
    protected $hidden = [];
    
    public function getBadgeSlugAttribute()
    {
        return $this->badge()->first()->slug;
    }
    public function getURLAttribute()
    {
        return '/activity/'.$this->id;
    }

    public function student()
    {
        return $this->belongsTo('Student');
    }

    public function badge()
    {
        return $this->belongsTo('Badge');
    }
}