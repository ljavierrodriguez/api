<?php

namespace Routes;

class WtemplateRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the workshop templates
         **/
        $wtemplateHandler = new \WtemplateHandler($app);
        $app->get('/wtemplates/', array($wtemplateHandler, 'getAllWtemplatesHandler'));//->add($scopes(['read_basic_info']));
        $app->get('/wtemplate/{wtemplate_id}', array($wtemplateHandler, 'getSingleWtemplate'))->add($scopes(['read_basic_info']));
        $app->post('/wtemplate/', array($wtemplateHandler, 'createWtemplateHandler'))->add($scopes(['super_admin']));
        $app->post('/wtemplate/{wtemplate_id}', array($wtemplateHandler, 'updateWtemplateHandler'))->add($scopes(['super_admin']));
        $app->delete('/wtemplate/{wtemplate_id}', array($wtemplateHandler, 'deleteHandler'))->add($scopes(['super_admin']));
    }
    

}