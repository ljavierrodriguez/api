<?php

class Task extends \Illuminate\Database\Eloquent\Model
{

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public static $possibleTypes = ['assignment', 'quiz', 'challenge', 'lesson', 'replit'];
    public static $possibleStages = ['pending','done'];
    public static $revisionStages = ['pending','approved','rejected'];

    protected $hidden = ['user_id'];
    protected $appends = ['student'];

    public function getURLAttribute(){

        return '/actionable/'.$this->id;
    }

    public function student(){
        return $this->belongsTo('Student');
    }

    public function getStudentAttribute(){
        return $this->student()->first();
    }

}