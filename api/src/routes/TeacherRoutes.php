<?php

namespace Routes;

class TeacherRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the student itself
         **/
        $teacherHandler = new \TeacherHandler($app);
        $app->get('/teachers/', array($teacherHandler, 'getAllHandler'))->add($scopes(['super_admin']));
        $app->get('/teacher/{teacher_id}', array($teacherHandler, 'getSingleHandler'))->add($scopes(['super_admin']));
        $app->get('/teachers/cohort/{cohort_id}', array($teacherHandler, 'getCohortTeachers'))->add($scopes(['super_admin']));
        
        $app->post('/teacher/', array($teacherHandler, 'createTeacherHandler'))->add($scopes(['super_admin']));
        $app->post('/teacher/{teacher_id}', array($teacherHandler, 'updateTeacherHandler'))->add($scopes(['super_admin']));
        $app->delete('/teacher/{teacher_id}', array($teacherHandler, 'deleteTeacherHandler'))->add($scopes(['super_admin']));
    }
    

}