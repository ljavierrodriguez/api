<?php


use Migrations\Migration;

class Task extends Migration
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

        if(!$this->schema->hasTable('tasks')){
            $this->schema->create('tasks', function($table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('status', 30); //['pending','done'];
                $table->string('type', 30); // ['project', 'quiz', 'challenge', 'lesson', 'replit'];
                $table->string('title', 200);
                $table->string('associated_slug', 200);
                $table->string('github_url', 255)->nullable();
                $table->string('live_url', 255)->nullable();
                $table->string('revision_status', 200)->default('pending'); // ['pending','approved','rejected'];
                $table->unsignedBigInteger('student_user_id')->nullable();
                $table->text('description');
                $table->timestamps();

                $table->foreign('student_user_id')->references('user_id')->on('students')->onDelete('cascade');
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
