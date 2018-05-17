[<-- back to main readme ](./README.md)
# ![alt text](https://assets.breatheco.de/apis/img/images.php?blob&random&cat=icon&tags=breathecode,32) BreatheCode API

## Installation

1. Clone the repo
2. Composer install
3. Config your database (MySQL and SQLite)
3. Create a config.php with the inital environment variables according to your project and add the file into the /api/ directory.
```php
<?php

define('DEBUG',true);

define('DATABASE_DRIVER','mysql');
define('DATABASE_HOST','localhost');
define('DATABASE_NAME','c9');
define('DATABASE_USERNAME','alesanchezr');
define('DATABASE_PASSWORD','');
define('DATABASE_PORT', 3306);

define('UT_DB_DRIVER','sqlite');
define('UT_DB_HOST','localhost');
define('UT_DB_NAME','tests.sqlite');
define('UT_DB_USERNAME','alesanchezr');
define('UT_DB_PASSWORD','');
define('UT_DB_PORT', 3306);

define('DELETE_MAX_DAYS',84400);
define('PUBLIC_URL','../public/');
define('ASSETS_URL','https://assets-alesanchezr.c9users.io');

define('ADMIN_URL','https://bc-admin-alesanchezr.c9users.io');
define('STUDENT_URL','https://coding-editor-alesanchezr.c9users.io');

define('S3_KEY','');
define('S3_SECRETE','');

define('VALID_IMG_EXTENSIONS',['jpg','png','jpeg']);

define('GLOBAL_CONFIG',[
    "scopes" => [
        'sync_data',//sync information from the wordpress app to the API
        'read_basic_info',//very basic info like locations
        'crud_student',//modify the students
        'crud_cohort',//modify the cohorts
        'user_profile',//review user profiles
        'read_talent_tree',//have access to badges, skills and profiles
        'student_assignments',//review edit and delete student assignments
        'student_tasks',//review edit and delete student tasks
        
        'teacher_assignments',//review teacher assignments
        'super_admin'//delete or update the most sensitive data
    ]
]);
```
4. Run the migrations
5. Run the tests
