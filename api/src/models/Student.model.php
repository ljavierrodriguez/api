<?php

class Student extends \Illuminate\Database\Eloquent\Model 
{
    protected $appends = ['url','badges'];
    public function getURLAttribute()
    {
        return '/student/'.$this->id;
    }
    
    public function updateBasedOnActivity(){
        
        $pointsPerBadge = array();
        $this->total_points = 0;
        $activities = $this->activities()->get();
        $updatedUserBadges = array();
        foreach($activities as $item){
            $this->total_points += $item->points_earned;
            
            $activityBadge = $item->badge;
            $userBadge = $this->badges()->get()->where('slug', $activityBadge->slug)->first();
            if(!$userBadge)
            {
                $this->badges()->attach($activityBadge, ['points_acumulated' => $item->points_earned]);
            }
            else{
                if(!in_array($userBadge->slug, $updatedUserBadges))
                {
                    $userBadge->pivot->points_acumulated = 0;
                    array_push($updatedUserBadges, $userBadge->slug);
                }
                $userBadge->pivot->points_acumulated += $item->points_earned;
                if($activityBadge->points_to_achieve <= $userBadge->pivot->points_acumulated)
                    $userBadge->pivot->is_achieved = true;
                $userBadge->pivot->save();
            }
        }
        
        $this->save();
    }
    
    public function getBadgesAttribute(){
        return $this->badges()->wherePivot('is_achieved',true)->pluck('slug');
    }
    
    public function badges()
    {
        return $this->belongsToMany('Badge')->withPivot('points_acumulated')->withTimestamps();
    }

    public function specialties()
    {
        return $this->belongsToMany('Specialty','student_specialty')->withTimestamps();
    }
    
    public function activities()
    {
        return $this->hasMany('Activity');
    }
}