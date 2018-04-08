<?php


use Migrations\Migration;

class CalendarMigration extends Migration
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
    public function up()
    {
        $this->schema->disableForeignKeyConstraints();
        
        if(!$this->schema->hasTable('calendars')){
            $this->schema->create('calendars', function($table) { 
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('slug', 200)->unique();
                $table->string('title', 200);
                $table->unsignedBigInteger('location_id')->nullable();
                $table->unsignedBigInteger('cohort_id')->nullable();
                $table->text('description');
                $table->timestamps();
                
                $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
                $table->foreign('cohort_id')->references('id')->on('cohorts')->onDelete('cascade');
            });
            
        }
        
        if(!$this->schema->hasTable('calevents')){
            $this->schema->create('calevents', function($table) { 
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('slug', 200)->unique();
                $table->string('title', 200);
                $table->text('description');
                $table->date('event_date');
                $table->unsignedBigInteger('calendar_id');
                $table->enum('type', ['holiday', 'community', 'academic']);
                $table->timestamps();
                
                $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
            });
        }
        
        $this->schema->enableForeignKeyConstraints();
    }
    
    public function down(){
        $this->schema->disableForeignKeyConstraints();
        
        $this->schema->dropIfExists('calevents');
        $this->schema->dropIfExists('calendars');
        
        $this->schema->enableForeignKeyConstraints();
    }
}
