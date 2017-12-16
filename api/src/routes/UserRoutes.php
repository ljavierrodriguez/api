<?php

namespace Routes;

class UserRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the user
         **/
        $userHandler = new \UserHandler($app);
        $app->get('/me', array($userHandler, 'getMe'))->add($scopes([]));
        $app->post('/credentials/user/', array($userHandler, 'createCredentialsHandler'))->add($scopes(['super_admin']));
        $app->post('/credentials/user/{user_id}', array($userHandler, 'updateCredentialsHandler'))->add($scopes(['sync_data']));
        $app->delete('/user/{user_id}', array($userHandler, 'deleteUser'))->add($scopes(['super_admin']));
        
        $app->post('/user/sync', array($userHandler, 'syncUserHandler'))->add($scopes(['sync_data']));
        $app->post('/settings/user/{user_id}', array($userHandler, 'updateUserSettings'))->add($scopes(['user_profile']));
        $app->get('/settings/user/{user_id}', array($userHandler, 'getUserSettings'))->add($scopes(['user_profile']));

    }
    

}