<?php

use Migrations\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $task1 = new Task();
        $task1->status = 'pending';
        $task1->type = 'assignment';
        $task1->title = 'Instagram Bootstrap';
        $task1->associated_slug = 'instagram-bootstrap';
        $task2->student()->associate($student->user_id);
        $task1->save();

        $task2 = new Task();
        $task2->status = 'pending';
        $task2->type = 'assignment';
        $task2->title = 'Postcard';
        $task2->associated_slug = 'postcard';
        $task2->student()->associate($student->user_id);
        $task2->save();

    }
}