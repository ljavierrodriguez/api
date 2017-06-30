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
    
if($schema->hasTable('oauth_clients'))
{
    $schema->dropIfExists('oauth_clients');
    $schema->dropIfExists('oauth_access_tokens');
    $schema->dropIfExists('oauth_authorization_codes');
    $schema->dropIfExists('oauth_refresh_tokens');
    $schema->dropIfExists('oauth_users');
    $schema->dropIfExists('oauth_scopes');
    $schema->dropIfExists('oauth_jwt');
    $createQuery = <<<SCHEMA
CREATE TABLE oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT client_id_pk PRIMARY KEY (client_id));
CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));
CREATE TABLE oauth_authorization_codes (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));
CREATE TABLE oauth_refresh_tokens (refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));
CREATE TABLE oauth_users (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));
CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);
CREATE TABLE oauth_jwt (client_id VARCHAR(80) NOT NULL, subject VARCHAR(80), public_key VARCHAR(2000), CONSTRAINT client_id_pk PRIMARY KEY (client_id));
SCHEMA;

	foreach (explode("\n", $createQuery) as $statement) {
		$app->db->statement($statement);
	}
	$app->db->table('oauth_clients')->insert(array(
			'client_id' => "testclient",
			'client_secret' => "testpass",
			'redirect_uri' => "http://fake/",
		));
	$app->db->table('oauth_users')->insert(array(
			'username' => "alesanchezr",
			'password' => sha1("1234"),
			'first_name' => "Alejandro",
			'last_name' => "Sanchez",
		));
}
    
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
    
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
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