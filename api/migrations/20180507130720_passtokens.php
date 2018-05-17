<?php


use Migrations\Migration;

class Passtokens extends Migration
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
        
        if(!$this->schema->hasTable('passtokens')){
            $this->schema->create('passtokens', function($table) { 
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('token', 250)->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        
        $this->schema->enableForeignKeyConstraints();
    }
    
    public function down(){
        $this->schema->disableForeignKeyConstraints();
        
        $this->schema->dropIfExists('passtokens');
        
        $this->schema->enableForeignKeyConstraints();
    }
}
