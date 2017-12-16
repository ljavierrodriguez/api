<?php

namespace Routes;

class SpecialtyRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the student specialties
         **/
        $specialtyHandler = new \SpecialtyHandler($app);
        $app->get('/specialty/{specialty_id}', array($specialtyHandler, 'getSingleHandler'))->add($scopes(['super_admin']));
        $app->get('/specialties/', array($specialtyHandler, 'getAllHandler'));
        
        $app->get('/specialties/profile/{profile_id}', array($specialtyHandler, 'getProfileSpecialtiesHandler'))->add($scopes(['read_talent_tree']));
        $app->get('/specialties/student/{student_id}', array($specialtyHandler, 'getStudentSpecialtiesHandler'))->add($scopes(['super_admin']));
        $app->post('/specialty/{specialty_id}', array($specialtyHandler, 'updateSpecialtyHandler'))->add($scopes(['super_admin']));
        $app->post('/specialty/', array($specialtyHandler, 'createSpecialtyHandler'))->add($scopes(['super_admin']));
        $app->delete('/specialty/{specialty_id}', array($specialtyHandler, 'deleteSpecialtyHandler'))->add($scopes(['super_admin']));
        
        $app->post('/specialty/image/{specialty_id}', array($specialtyHandler, 'updateThumbHandler'))->add($scopes(['super_admin']));
        
        $app->post('/specialty/profile/{profile_id}', array($specialtyHandler, 'addSpecialtiesToProfileHandler'))->add($scopes(['super_admin']));
        $app->delete('/specialty/profile/{profile_id}', array($specialtyHandler, 'deleteSpecialtiesFromProfileHandler'))->add($scopes(['super_admin']));

    }
    

}