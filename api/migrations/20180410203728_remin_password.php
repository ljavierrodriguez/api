<?php


use Migrations\Migration;

class ReminPassword extends Migration
{
    
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
