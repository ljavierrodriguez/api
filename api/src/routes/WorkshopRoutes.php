<?php

namespace Routes;

class WorkshopRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the workshops
         **/
        $workshopHandler = new \WorkshopHandler($app);
        $app->get('/workshops/', array($workshopHandler, 'getAllWorkshopsHandler'));//->add($scopes(['read_basic_info']));
        $app->get('/workshop/location/{location_id}', array($workshopHandler, 'getAllWorkshopsFromLocationHandler'))->add($scopes(['read_basic_info']));
        $app->get('/workshop/{workshop_id}', array($workshopHandler, 'getSingleWorkshop'))->add($scopes(['read_basic_info']));
        
        $app->post('/workshop/', array($workshopHandler, 'createWorkshopHandler'))->add($scopes(['super_admin']));
        $app->post('/workshop/{workshop_id}', array($workshopHandler, 'updateWorkshopHandler'))->add($scopes(['super_admin']));
        $app->delete('/workshop/{workshop_id}', array($workshopHandler, 'deleteHandler'))->add($scopes(['super_admin']));
    }
    

}