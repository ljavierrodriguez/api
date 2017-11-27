<?php

class Wtemplate extends \Illuminate\Database\Eloquent\Model 
{

    public function workshops(){
        return $this->hasMany('Workshop');
    }
    
    
}