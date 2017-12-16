<?php

namespace Routes;

class AtemplateRoutes{
    
    public function __construct($app, $scopes){

        /**
         * Assignments and AssignmentTemplate
         **/
        $atemplateHandler = new \AtemplateHandler($app);
        $app->get('/atemplates/', array($atemplateHandler, 'getAllHandler'))->add($scopes(['read_basic_info']));
        $app->get('/atemplate/{atemplate_id}', array($atemplateHandler, 'getSingleHandler'))->add($scopes(['super_admin']));
        
        $app->post('/atemplate/sync/{wp_id}', array($atemplateHandler, 'syncFromWPHandler'))->add($scopes(['super_admin']));
        
        $app->post('/atemplate/', array($atemplateHandler, 'createHandler'))->add($scopes(['super_admin']));
        $app->post('/atemplate/{atemplate_id}', array($atemplateHandler, 'updateHandler'))->add($scopes(['super_admin']));
        $app->delete('/atemplate/{atemplate_id}', array($atemplateHandler, 'deleteHandler'))->add($scopes(['super_admin']));
    }
    

}