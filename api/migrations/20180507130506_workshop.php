<?php


use Migrations\Migration;

class Workshop extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */

    public function up(){
        
        $this->schema->disableForeignKeyConstraints();
        
        if(!$this->schema->hasTable('wtemplates')){
            $this->schema->create('wtemplates', function($table) { 
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('slug', 200)->unique();
                $table->string('name', 200);
                $table->timestamps();
            
            });
        }
        
        if(!$this->schema->hasTable('workshops')){
            $this->schema->create('workshops', function($table) { 
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
        
        if(!$this->schema->hasTable('workshop_student')){
            $this->schema->create('workshop_student', function($table) { 
                $table->engine = 'InnoDB';
                $table->unsignedBigInteger('student_user_id');//->primary();
                $table->unsignedBigInteger('workshop_id');//->primary();
                $table->timestamps();
            
                $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
                $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            });
        }
        
        $this->schema->enableForeignKeyConstraints();
    }
    
    public function down(){
        $this->schema->disableForeignKeyConstraints();
        
        $this->schema->dropIfExists('wtemplates');
        $this->schema->dropIfExists('workshops');
        $this->schema->dropIfExists('workshop_student');
        
        $this->schema->enableForeignKeyConstraints();
    }
}
