<?php


use Migrations\Migration;

class TaskMigration extends Migration
{
    public function up()
    {
        $this->schema->disableForeignKeyConstraints();
        
        if(!$this->schema->hasTable('tasks')){
            $this->schema->create('tasks', function($table) { 
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('status', 30); //['pending','done'];
                $table->string('type', 30); // ['project', 'quiz', 'challenge', 'lesson', 'replit'];
                $table->string('title', 200);
                $table->string('associated_slug', 200);
                $table->string('github_url', 255)->nullable();
                $table->string('revision_status', 200)->default('pending'); // ['pending','approved','rejected'];
                $table->unsignedBigInteger('student_user_id')->nullable();
                $table->text('description');
                $table->timestamps();
                
                $table->foreign('student_user_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
        
        $this->schema->enableForeignKeyConstraints();
    }
    
    public function down(){
        $this->schema->disableForeignKeyConstraints();
        
        $this->schema->dropIfExists('tasks');
        
        $this->schema->enableForeignKeyConstraints();
    }
}
