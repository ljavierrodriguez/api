<?php

require_once('../../vendor/autoload.php');
require_once('../dependencies.php');

$schema = $app->db->getSchemaBuilder();

$schema->disableForeignKeyConstraints();
if($schema->hasTable('users')) $app->db->table('users')->truncate();
if($schema->hasTable('students')) $app->db->table('students')->truncate();
if($schema->hasTable('cohorts')) $app->db->table('cohorts')->truncate();
if($schema->hasTable('teachers')) $app->db->table('teachers')->truncate();
if($schema->hasTable('cohort_teacher')) $app->db->table('cohort_teacher')->truncate();
if($schema->hasTable('cohort_student')) $app->db->table('cohort_student')->truncate();
if($schema->hasTable('locations')) $app->db->table('locations')->truncate();
if($schema->hasTable('assignmenttemplates')) $app->db->table('assignmenttemplates')->truncate();
if($schema->hasTable('assignments')) $app->db->table('assignments')->truncate();
if($schema->hasTable('badges')) $app->db->table('badges')->truncate();
if($schema->hasTable('badge_student')) $app->db->table('badge_student')->truncate();
if($schema->hasTable('activities')) $app->db->table('activities')->truncate();
if($schema->hasTable('specialties')) $app->db->table('specialties')->truncate();
if($schema->hasTable('student_specialty')) $app->db->table('student_specialty')->truncate();
if($schema->hasTable('requierments')) $app->db->table('requierments')->truncate();
if($schema->hasTable('profiles')) $app->db->table('profiles')->truncate();
if($schema->hasTable('profile_specialty')) $app->db->table('profile_specialty')->truncate();

echo "Creating Badges table... \n";
$schema->dropIfExists('badges');
if(!$schema->hasTable('badges')){
    $schema->create('badges', function($table) {
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 200);
        $table->string('icon', 255);
        $table->integer('points_to_achieve');
        $table->text('description');
        $table->string('technologies', 200);
        $table->timestamps();
    
        $table->index('slug');
    });
}

echo "Creating Users table... \n";
$schema->dropIfExists('users');
if(!$schema->hasTable('users')){
    $schema->create('users', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->integer('wp_id')->unique()->nullable();
        $table->string('avatar_url', 255);
        $table->string('bio', 255);
        $table->text('settings')->nullable();
        $table->string('full_name', 200);
        $table->string('type', 20);
        $table->string('username', 200)->unique();
        $table->timestamps();
        
        $table->index('wp_id');
    });
}

echo "Creating Students table...";
$schema->dropIfExists('students');
if(!$schema->hasTable('students')){
    $schema->create('students', function($table) {
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('user_id');
        $table->string('full_name', 200);
        $table->integer('total_points')->nullable()->default(0);
        $table->timestamps();
        $table->enum('financial_status', ['fully_paid', 'up_to_date', 'late', 'uknown'])->default('uknown');
        $table->enum('status', ['currently_active', 'under_review', 'blocked', 'studies_finished', 'student_dropped'])->default('currently_active');
        $table->primary('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
        echo "s";
}
echo "done. \n";
echo "Creating Cohort table... \n";
$schema->dropIfExists('cohorts');
if(!$schema->hasTable('cohorts')){
    $schema->create('cohorts', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 200);
        $table->date('kickoff-date')->nullable();
        $table->unsignedBigInteger('location_id');
        $table->unsignedBigInteger('profile_id');
        $table->string('stage', 50);//['not-started', 'on-prework', 'on-course','on-final-project','finished']
        $table->string('slack-url', 200);
        $table->timestamps();
    
        $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
    });
}

echo "Creating Teacher table... \n";
$schema->dropIfExists('teachers');
if(!$schema->hasTable('teachers')){
    $schema->create('teachers', function($table) { 
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        
            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
}

echo "Creating AssignmentTemplate table... \n";
$schema->dropIfExists('atemplates');
if(!$schema->hasTable('atemplates')){
    $schema->create('atemplates', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('project_slug', 200)->unique();
        $table->integer('wp_id')->unique()->nullable();
        $table->string('title', 200);
        $table->string('excerpt', 200)->nullable();
        $table->string('difficulty', 20)->nullable();
        $table->string('duration', 200);//in hours
        $table->string('technologies', 200);
        $table->timestamps();
    
    });
}

echo "Creating Assignment table... \n";
$schema->dropIfExists('assignments');
if(!$schema->hasTable('assignments')){
    $schema->create('assignments', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->unsignedBigInteger('student_user_id');
        $table->unsignedBigInteger('teacher_user_id');
        $table->date('duedate');
        $table->unsignedBigInteger('atemplate_id');
        $table->string('status', 40);
        $table->string('reject_reason', 500)->nullable();
        $table->string('github_url', 255)->nullable();
        $table->timestamps();
    
        $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
        $table->foreign('teacher_user_id')->references('user_id')->on('teachers')->onDelete('cascade');
        $table->foreign('atemplate_id')->references('id')->on('atemplates')->onDelete('cascade');
    });
}

echo "Creating Cohort<>Teacher pivot table... \n";
$schema->dropIfExists('cohort_teacher');
if(!$schema->hasTable('cohort_teacher')){
    $schema->create('cohort_teacher', function($table) { 
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('teacher_user_id');//->primary();
        $table->unsignedBigInteger('cohort_id');//->primary();
        $table->boolean('is_instructor')->default(false);//->primary();
        $table->timestamps();
    
        $table->foreign('teacher_user_id')->references('user_id')->on('teachers')->onDelete('cascade');
        $table->foreign('cohort_id')->references('id')->on('cohorts')->onDelete('cascade');
    });
}

echo "Creating Cohort<>Student pivot table... \n";
$schema->dropIfExists('cohort_student');
if(!$schema->hasTable('cohort_student')){
    $schema->create('cohort_student', function($table) { 
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('student_user_id');//->primary();
        $table->unsignedBigInteger('cohort_id');//->primary();
        $table->timestamps();
    
        $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
        $table->foreign('cohort_id')->references('id')->on('cohorts')->onDelete('cascade');
    });
}

echo "Creating Location table... \n";
$schema->dropIfExists('locations');
if(!$schema->hasTable('locations')){
    $schema->create('locations', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 200);
        $table->string('country', 200);
        $table->string('address', 200);
        $table->timestamps();
    
    });
}

echo "Creating Badge<>Student pivot table... \n";
$schema->dropIfExists('badge_student');
if(!$schema->hasTable('badge_student')){
    $schema->create('badge_student', function($table) { 
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('student_user_id');//->primary();
        $table->unsignedBigInteger('badge_id');//->primary();
        $table->unsignedBigInteger('points_acumulated')->default(0);//->primary();
        $table->boolean('is_achieved')->default(false);//->primary();
        $table->timestamps();
    
        $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
    });
}

echo "Creating Activities table... \n";
$schema->dropIfExists('activities');
if(!$schema->hasTable('activities')){
    $schema->create('activities', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->unsignedBigInteger('student_user_id');
        $table->unsignedBigInteger('badge_id');
        $table->string('type', 60); //['project', 'quiz', 'challenge', 'teacher_reward']
        $table->string('name', 255);
        $table->text('description');
        $table->integer('points_earned');
        $table->timestamps();
    
        $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');;
    });
}

echo "Creating Specialties table... \n";
$schema->dropIfExists('specialties');
if(!$schema->hasTable('specialties')){
    $schema->create('specialties', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 255);
        $table->string('icon', 255);
        $table->text('description');
        $table->integer('points_to_achieve');
        $table->timestamps();

    });
}

echo "Creating Student_Specialty pivot table... \n";
$schema->dropIfExists('student_specialty');
if(!$schema->hasTable('student_specialty')){
    $schema->create('student_specialty', function($table) { 
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('student_user_id');
        $table->unsignedBigInteger('specialty_id', 200)->unique();
        $table->timestamps();
    
        $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');;
        $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');;
    });
}

echo "Creating Requierments table... \n";
$schema->dropIfExists('requierments');
if(!$schema->hasTable('requierments')){
    $schema->create('requierments', function($table) { 
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('specialty_id');
        $table->unsignedBigInteger('badge_id');
        $table->integer('points_to_complete');
        $table->timestamps();
    
        $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
    });
}

echo "Creating Profiles table... \n";
$schema->dropIfExists('profiles');
if(!$schema->hasTable('profiles')){
    $schema->create('profiles', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 255);
        $table->text('description');
        $table->timestamps();

    });
}

echo "Creating Profile_Specialty pivot table... \n";
$schema->dropIfExists('profile_specialty');
if(!$schema->hasTable('profile_specialty')){
    $schema->create('profile_specialty', function($table) {
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('profile_id');
        $table->unsignedBigInteger('specialty_id');
        $table->timestamps();
    
        $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
        $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
    });
}

$schema->enableForeignKeyConstraints();
echo "All done!! \n";