<?php

namespace Routes;

class CatalogRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Static Data that will not be managed
         **/
        $catalogHandler = new \CatalogHandler($app);
        $app->get('/catalogs/', array($catalogHandler, 'getAllCatalogs'));
        $app->get('/catalog/{catalog_slug}/', array($catalogHandler, 'getCatalog'));
    }
    

}