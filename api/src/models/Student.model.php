<?php

class Student extends \Illuminate\Database\Eloquent\Model 
{
    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $hidden = ['user_id','user','updated_at','pivot'];
    protected $appends = ['cohorts', 'url','badges','id','email','wp_id','full_name','avatar_url','bio'];
    
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
        return '/student/'.$this->id;
    }
    
    public function getEmailAttribute(){
        return $this->user->username;
    }
    
    public function getCohortsAttribute(){
        return $this->cohorts()->get()->pluck('slug');
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
    
    public function user(){
        return $this->belongsTo('User');
    }
    
    public function assignments(){
        return $this->hasMany('Assignment');
    }
    
    public function badges(){
        return $this->belongsToMany('Badge')->withPivot('points_acumulated')->withTimestamps();
    }

    public function specialties(){
        return $this->belongsToMany('Specialty','student_specialty')->withTimestamps();
    }

    public function cohorts(){
        $cohorts = $this->belongsToMany('Cohort','cohort_student','student_user_id','cohort_id')->withTimestamps();
        return $cohorts;
    }
    
    public function activities(){
        return $this->hasMany('Activity');
    }
}