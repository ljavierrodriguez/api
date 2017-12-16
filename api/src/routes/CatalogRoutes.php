<?php

namespace Routes;

class CatalogRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Static Data that will not be managed
         **/
        $catalogHandler = new \CatalogHandler($app);
        $app->get('/catalog/technologies/', array($catalogHandler, 'getAllTechnologies'));
        $app->get('/catalog/countries/', array($catalogHandler, 'getAllCountries'));
        $app->get('/catalog/cohort_stages/', array($catalogHandler, 'getAllCohortStages'));
        $app->get('/catalog/atemplate_difficulties/', array($catalogHandler, 'getAllAtemplateDifficulties'));//getAllAtemplateDifficulties
    }
    

}