<?php

namespace Routes;

class LocationRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the locations
         **/
        $locationHandler = new \LocationHandler($app);
        $app->get('/locations/', array($locationHandler, 'getAllHandler'));//->add($scopes(['read_basic_info']));
        $app->get('/location/{location_id}', array($locationHandler, 'getSingleHandler'))->add($scopes(['read_basic_info']));
        
        $app->put('/location/', array($locationHandler, 'createLocationHandler'))->add($scopes(['super_admin']));
        $app->post('/location/{location_id}', array($locationHandler, 'updateLocationHandler'))->add($scopes(['super_admin']));
        $app->delete('/location/{location_id}', array($locationHandler, 'deleteLocationHandler'))->add($scopes(['super_admin']));
        
        $app->post('/location/sync/', array($locationHandler, 'syncLocationHandler'))->add($scopes(['sync_data']));
         
    }
    

}