<?php

define('DEBUG',true);
define('EMAIL_NOTIFICATIONS',true);

define('DATABASE_DRIVER','mysql');
define('DATABASE_HOST','localhost');
define('DATABASE_NAME','');
define('DATABASE_USERNAME','');
define('DATABASE_PASSWORD','');
define('DATABASE_PORT', 3306);

define('UT_DB_DRIVER','sqlite');
define('UT_DB_HOST','localhost');
define('UT_DB_NAME','tests.sqlite');
define('UT_DB_USERNAME','');
define('UT_DB_PASSWORD','');
define('UT_DB_PORT', 3306);

define('DELETE_MAX_DAYS',84400);
define('PUBLIC_URL','../public/');
define('ASSETS_URL','');
define('VALID_IMG_EXTENSIONS',['jpg','png','jpeg']);

define('ADMIN_URL','');
define('STUDENT_URL','');
define('TEACHER_URL','');

define('S3_KEY','');
define('S3_SECRET','');

define('GLOBAL_CONFIG',[
    "scopes" => [
        'sync_data',//sync information from the wordpress app to the API
        'read_basic_info',//very basic info like locations
        'crud_student',//modify the students
        'crud_cohort',//modify the cohorts
        'user_profile',//review user profiles
        'update_cohort_current_day',
        'read_talent_tree',//have access to badges, skills and profiles
        'student_assignments',//review edit and delete student assignments
        'student_tasks',//review edit and delete student tasks

        'teacher_assignments',//review teacher assignments
        'super_admin'//delete or update the most sensitive data
    ]
]);