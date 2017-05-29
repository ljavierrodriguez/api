<?php

require_once('../../vendor/autoload.php');
require_once('../dependencies.php');

$schema = $app->db->getSchemaBuilder();

$schema->disableForeignKeyConstraints();
if($schema->hasTable('students')) $app->db->table('students')->truncate();
if($schema->hasTable('badges')) $app->db->table('badges')->truncate();
if($schema->hasTable('badge_student')) $app->db->table('badge_student')->truncate();
if($schema->hasTable('activities')) $app->db->table('activities')->truncate();
if($schema->hasTable('specialties')) $app->db->table('specialties')->truncate();
if($schema->hasTable('student_specialty')) $app->db->table('student_specialty')->truncate();
if($schema->hasTable('requierments')) $app->db->table('requierments')->truncate();
if($schema->hasTable('profiles')) $app->db->table('profiles')->truncate();
if($schema->hasTable('profile_specialty')) $app->db->table('profile_specialty')->truncate();
    
$schema->dropIfExists('badges');
if(!$schema->hasTable('badges'))
{
    $schema->dropIfExists('badges');
    $schema->create('badges', function($table) { 
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 200);
        $table->string('image_url', 255);
        $table->integer('points_to_achieve');
        $table->text('description');
        $table->string('technologies', 200);
        $table->timestamps();
    
        $table->index('slug');
    });
}
$schema->dropIfExists('students');
if(!$schema->hasTable('students'))
{
    $schema->dropIfExists('students');
    $schema->create('students', function($table) { 
        $table->bigIncrements('id');
        $table->unsignedBigInteger('breathecode_id')->unique();
        $table->string('email', 200)->unique();
        $table->string('avatar_url', 255);
        $table->string('full_name', 200);
        $table->integer('total_points')->nullable()->default(0);
        $table->text('description');
        $table->timestamps();
    
    });
}
$schema->dropIfExists('badge_student');
if(!$schema->hasTable('badge_student'))
{
    $schema->create('badge_student', function($table) { 
        $table->unsignedBigInteger('student_id');//->primary();
        $table->unsignedBigInteger('badge_id');//->primary();
        $table->unsignedBigInteger('points_acumulated')->default(0);//->primary();
        $table->boolean('is_achieved')->default(false);//->primary();
        $table->timestamps();
    
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');;
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');;
    });
}

$schema->dropIfExists('activities');
if(!$schema->hasTable('activities'))
{
    $schema->create('activities', function($table) { 
        $table->bigIncrements('id');
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('badge_id');
        $table->enum('type', ['project', 'quiz', 'challenge']);
        $table->string('name', 255);
        $table->text('description');
        $table->integer('points_earned');
        $table->timestamps();
    
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');;
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');;
    });
}
$schema->dropIfExists('specialties');
if(!$schema->hasTable('specialties'))
{
    $schema->create('specialties', function($table) { 
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 255);
        $table->string('image_url', 255);
        $table->text('description');
        $table->integer('points_to_achieve');
        $table->timestamps();

    });
}
$schema->dropIfExists('student_specialty');
if(!$schema->hasTable('student_specialty'))
{
    $schema->create('student_specialty', function($table) { 
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('specialty_id', 200)->unique();
        $table->timestamps();
    
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');;
        $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');;
    });
}
$schema->dropIfExists('requierments');
if(!$schema->hasTable('requierments'))
{
    $schema->create('requierments', function($table) { 
        $table->unsignedBigInteger('specialty_id');
        $table->unsignedBigInteger('badge_id');
        $table->integer('points_to_complete');
        $table->timestamps();
    
        $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
    });
}
$schema->dropIfExists('profiles');
if(!$schema->hasTable('profiles'))
{
    $schema->create('profiles', function($table) { 
        $table->bigIncrements('id');
        $table->string('slug', 200)->unique();
        $table->string('name', 255);
        $table->text('description');
        $table->timestamps();

    });
}
$schema->dropIfExists('profile_specialty');
if(!$schema->hasTable('profile_specialty'))
{
    $schema->create('profile_specialty', function($table) { 
        $table->unsignedBigInteger('profile_id');
        $table->unsignedBigInteger('specialty_id');
        $table->timestamps();
    
        $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
        $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
    });
}
$schema->enableForeignKeyConstraints();