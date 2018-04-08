<?php

namespace Routes;

class AssignmentRoutes{
    
    public function __construct($app, $scopes){
        

        $assignmentHandler = new \AssignmentHandler($app);
        $app->get('/student/assignments/', array($assignmentHandler, 'getAllHandler'))->add($scopes(['super_admin']));
        $app->get('/student/assignment/{assignment_id}', array($assignmentHandler, 'getSingleHandler'))->add($scopes(['super_admin']));
        
        $app->get('/assignments/student/{student_id}', array($assignmentHandler, 'getAllStudentAssignmentsHandler'))->add($scopes(['student_assignments']));
        $app->get('/assignments/teacher/{teacher_id}', array($assignmentHandler, 'getAllTeacherAssignmentsHandler'))->add($scopes(['teacher_assignments']));
        $app->get('/assignments/cohort/{cohort_id}', array($assignmentHandler, 'getAllCohortAssignmentsHandler'))->add($scopes(['teacher_assignments']));
        
        $app->post('/student/assignment/', array($assignmentHandler, 'createAssignmentHandler'))->add($scopes(['super_admin']));
        $app->post('/student/assignment/{assignment_id}', array($assignmentHandler, 'updateAssignmentHandler'))->add($scopes(['student_assignments']));
        $app->post('/assignment/cohort/{cohort_id}', array($assignmentHandler, 'createCohortAssignmentHandler'))->add($scopes(['teacher_assignments']));
        $app->delete('/student/assignment/{assignment_id}', array($assignmentHandler, 'deleteAssignmentHandler'))->add($scopes(['super_admin']));
        
        $app->post('/assignment/sync/', array($assignmentHandler, 'syncFromWPHandler'))->add($scopes(['sync_data']));
    }
    

}