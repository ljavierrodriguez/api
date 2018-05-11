<?php

namespace Routes;

class ProfileRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Every course is meant to trian students in one specific profile, for example: Full Stack Web Developer
         **/
        $profileHandler = new \ProfileHandler($app);
        $app->get('/profiles/', array($profileHandler, 'getAllHandler'));//->add($scopes(['read_talent_tree']));
        $app->get('/profile/{profile_id}', array($profileHandler, 'getSingleHandler'))->add($scopes(['super_admin']));
        
        $app->post('/profile/{profile_id}', array($profileHandler, 'updateProfileHandler'))->add($scopes(['super_admin']));
        $app->put('/profile/', array($profileHandler, 'createProfileHandler'))->add($scopes(['super_admin']));
        $app->delete('/profile/{profile_id}', array($profileHandler, 'deleteProfileHandler'))->add($scopes(['super_admin']));

    }
    

}