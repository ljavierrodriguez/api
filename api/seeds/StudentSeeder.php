<?php

use Migrations\Seeder;

class StudentSeeder extends Seeder
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
        $user1 = new User();
        $user1->first_name = 'Alejandro';
        $user1->last_name = 'Sanchez';
        $user1->type = 'student';
        $user1->username = 'aalejo@gmail.com';
        $user1->save();

        $teacher = new Teacher();
        $user->teacher()->save($teacher);

        $user2 = new User();
        $user2->first_name = 'Ramon';
        $user2->last_name = 'Peralta';
        $user2->type = 'teacher';
        $user2->username = 'a@4geeks.co';
        $user2->save();

        $student = new Student();
        $user->teacher()->save($student);

    }
}
