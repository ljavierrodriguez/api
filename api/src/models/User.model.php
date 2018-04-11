<?php

class User extends \Illuminate\Database\Eloquent\Model 
{
    protected $appends = ['avatar_url'];
    protected $hidden = ['settings'];

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public function getAvatarUrlAttribute(){
        
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $this->email ) ) );
        
        return $url;
    }
    public function setUserSettings($settings){
        $this->settings = serialize($settings);
    }
    public function getUserSettings(){
        return unserialize($this->settings);
    }
    public function student(){
        return $this->hasOne('Student');
    }
    
    public function teacher(){
        return $this->hasOne('Teacher');
    }
    
    public function passtokens(){
        return $this->hasMany('Passtoken');
    }
}