<?php

require_once('../../vendor/autoload.php');
require_once('../dependencies.php');

$schema = $app->db->getSchemaBuilder();

$schema->disableForeignKeyConstraints();

echo "Creating Workshop Template table... \n";
$schema->dropIfExists('wtemplates');
if(!$schema->hasTable('wtemplates')){
    $schema->create('wtemplates', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 200);
        $table->timestamps();
    
    });
}

echo "Creating Workshop table... \n";
$schema->dropIfExists('workshops');
if(!$schema->hasTable('workshops')){
    $schema->create('workshops', function($table) { 
        $table->engine = 'InnoDB';
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 200);
        $table->date('start-date')->nullable();
        $table->unsignedBigInteger('location_id');
        $table->unsignedBigInteger('wtemplate_id');
        $table->timestamps();
    
        $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        $table->foreign('wtemplate_id')->references('id')->on('wtemplates')->onDelete('cascade');
    });
}

echo "Creating Workshop<>Student pivot table... \n";
$schema->dropIfExists('workshop_student');
if(!$schema->hasTable('workshop_student')){
    $schema->create('workshop_student', function($table) { 
        $table->engine = 'InnoDB';
        $table->unsignedBigInteger('student_user_id');//->primary();
        $table->unsignedBigInteger('workshop_id');//->primary();
        $table->timestamps();
    
        $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
        $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
    });
}

$schema->enableForeignKeyConstraints();
echo "All done!! \n";