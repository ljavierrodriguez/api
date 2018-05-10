<?php

namespace Routes;

class CohortRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the cohorts
         **/
        $cohortHandler = new \CohortHandler($app);
        $app->get('/cohorts/', array($cohortHandler, 'getAllCohortsHandler'));//->add($scopes(['read_basic_info'])));
        $app->get('/cohorts/location/{location_id}', array($cohortHandler, 'getAllCohortsFromLocationHandler'))->add($scopes(['read_basic_info']));
        $app->get('/cohorts/teacher/{teacher_id}', array($cohortHandler, 'getAllCohortsFromTeacherHandler'))->add($scopes(['read_basic_info']));
        $app->get('/cohort/{cohort_id}', array($cohortHandler, 'getSingleCohort'))->add($scopes(['read_basic_info']));
        $app->get('/students/cohort/{cohort_id}', array($cohortHandler, 'getCohortStudentsHandler'))->add($scopes(['read_basic_info']));
        
        $app->post('/student/cohort/{cohort_id}', array($cohortHandler, 'addStudentToCohortHandler'))->add($scopes(['crud_cohort']));
        $app->put('/cohort/', array($cohortHandler, 'createCohortHandler'))->add($scopes(['crud_cohort']));
        $app->post('/cohort/{cohort_id}', array($cohortHandler, 'updateCohortHandler'))->add($scopes(['crud_cohort']));
        $app->delete('/cohort/{cohort_id}', array($cohortHandler, 'deleteCohortHandler'))->add($scopes(['crud_cohort']));
        
        $app->post('/teacher/cohort/{cohort_id}', array($cohortHandler, 'addTeacherToCohortHandler'))->add($scopes(['super_admin']));
        $app->delete('/teacher/cohort/{cohort_id}', array($cohortHandler, 'deleteTeacherFromCohortHandler'))->add($scopes(['super_admin']));
        
        $app->post('/cohort/sync/', array($cohortHandler, 'syncCohortHandler'))->add($scopes(['sync_data']));
    }
    

}