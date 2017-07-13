<?php

    require_once('../../vendor/autoload.php');
    require_once('../dependencies.php');

    $schema = $app->db->getSchemaBuilder();
    $app->db->listen(function($sql) {
        var_dump($sql);
    });
    $schema->disableForeignKeyConstraints();
    $app->db->table('users')->truncate();
    $app->db->table('cohorts')->truncate();
    $app->db->table('assignments')->truncate();
    $app->db->table('atemplates')->truncate();
    $app->db->table('teachers')->truncate();
    $app->db->table('locations')->truncate();
    $app->db->table('students')->truncate();
    $app->db->table('badges')->truncate();
    $app->db->table('badge_student')->truncate();
    $app->db->table('activities')->truncate();
    $app->db->table('specialties')->truncate();
    $app->db->table('student_specialty')->truncate();
    $app->db->table('requierments')->truncate();
    $app->db->table('profile_specialty')->truncate();
    $app->db->table('profiles')->truncate();
    $schema->enableForeignKeyConstraints();
    
    //$oauth = new oauth_clients;
    
    //$oauth->save();
    
    echo "Creating location miami... ";
    $location = new Location;
    $location->slug = 'mdc';
    $location->name = 'Miami Dade';
    $location->address = '270 Catalonia';
    $location->save();
    echo "done  \n";
    
    echo "Adding it to cohort MDC III...";
    $cohort = new Cohort;
    $cohort->slug = 'mdc-iii';
    $cohort->name = 'MDC III';
    $cohort->stage = 'not-started';
    $location->cohorts()->save($cohort);
    $cohort->save();
    echo "done  \n";
    
    
    echo "Creating location el impact hub... ";
    $location2 = new Location;
    $location2->slug = 'ih';
    $location2->name = 'Impact Hub';
    $location2->address = 'Chacao, Torre HP';
    $location2->save();
    echo "done  \n";
    
    echo "Adding it to cohort MDC III...";
    $cohort2 = new Cohort;
    $cohort2->slug = 'ih-i';
    $cohort2->name = 'Impact Hub I';
    $cohort2->stage = 'not-started';
    $location2->cohorts()->save($cohort2);
    $cohort2->save();
    echo "done  \n";
    
    
    echo "Creating a user 1...";
    $u1 = new User;
    $u1->username = 'john@4geeks.co';
    $u1->full_name = 'John';
    $u1->save();
    echo "done  \n";
    echo "Creating a user 2...";
    $u2 = new User;
    $u2->full_name = 'Pedro';
    $u2->username = 'pedro@4geeks.co';
    $u2->save();
    echo "done  \n";
    echo "Creating a user 3...";
    $u3 = new User;
    $u3->full_name = 'Mickel';
    $u3->username = 'mickel@4geeks.co';
    $u3->save();
    echo "done  \n";
    echo "Creating a user 4...";
    $u4 = new User;
    $u4->username = 'teacher1@4geeks.co';
    $u4->full_name = 'Ronal';
    $u4->save();
    echo "done  \n";
    echo "Creating a user 5...";
    $u5 = new User;
    $u5->full_name = 'Saul';
    $u5->username = 'teacher2@4geeks.co';
    $u5->save();
    echo "done  \n";
    echo "Creating a user 6...";
    $u6 = new User;
    $u6->full_name = 'Bob';
    $u6->username = 'teacher3@4geeks.co';
    $u6->save();
    echo "done  \n";
    
    echo "Creating student 1...";
    $std1 = new Student;
    $u1->student()->save($std1);
    $std1->cohorts()->save($cohort);
    $std1->save();
    echo "done  \n";
    
    echo "Creating student 2...";
    $std2 = new Student;
    $u2->student()->save($std2);
    $std2->cohorts()->save($cohort);
    $std2->save();
    echo "done  \n";
    
    echo "Creating student 3...";
    $std3 = new Student;
    $u3->student()->save($std3);
    $std3->cohorts()->save($cohort2);
    $std3->save();
    echo "done  \n";
    
    
    echo "Creating teacher 1...";
    $tea1 = new Teacher;
    $u4->student()->save($tea1);
    $tea1->cohorts()->save($cohort);
    $tea1->save();
    echo "done  \n";
    
    echo "Creating teacher 2...";
    $tea2 = new Teacher;
    $u5->student()->save($tea2);
    $tea2->cohorts()->save($cohort);
    $tea2->save();
    echo "done  \n";
    
    echo "Creating teacher 3...";
    $tea3 = new Teacher;
    $u6->student()->save($tea3);
    $tea3->cohorts()->save($cohort2);
    $tea3->save();
    echo "done  \n";
    
    echo "Creating assingment template 1...";
    $at = new Atemplate;
    $at->project_slug = 'the-portfolio';
    $at->title = 'Your first portfolio';
    $at->excerpt = 'This is an amazing excerpt';
    $at->duration = '16hrs';
    $at->technologies = 'PHP, Wordpress';
    $at->save();
    echo "done  \n";
    
    
    echo "Creating assingment template 2...";
    $at = new Atemplate;
    $at->project_slug = 'the-great-project';
    $at->title = 'Create a great project';
    $at->duration = '6hrs';
    $at->technologies = 'CSS, HTML';
    $at->save();
    echo "done  \n";
    
    echo "Creating assingment 1 and adding it to the template 1...";
    $assignment = new Assignment;
    $assignment->status = 'not-delivered';
    $assignment->duedate = '13/12/2017';
    $assignment->student()->associate($std1);
    $assignment->teacher()->associate($tea1);
    $assignment->template()->associate($at);
    $assignment->save();
    echo "done  \n";

    echo "Creating badge 'css_selectors' (Points to achieve: 10)";
    $badge1 = new Badge;
    $badge1->name = 'CSS Selectors';
    $badge1->slug = 'css_selectors';
    $badge1->points_to_achieve = 10;
    $badge1->technologies = 'css3';
    $badge1->description = 'Select everything';
    $badge1->save();
    echo "done  \n";
    
    echo "Creating badge 'keyboard_shortcuts' (Points to achieve: 10)";
    $badge2 = new Badge;
    $badge2->name = 'Shorcut Everything';
    $badge2->slug = 'keyboard_shortcuts';
    $badge2->points_to_achieve = 10;
    $badge2->technologies = 'sublime, c9';
    $badge2->description = 'Learn and use the keyboards';
    $badge2->save();
    echo "done  \n";

    echo "Creating badge 'clean_code' (Points to achieve: 10)";
    $badge3 = new Badge;
    $badge3->name = 'Clean Code';
    $badge3->slug = 'clean_code';
    $badge3->points_to_achieve = 10;
    $badge3->technologies = 'css3, html5, js, sublime';
    $badge3->description = 'Have a commented and clean code';
    $badge3->save();
    echo "done  \n";
    
    echo "Creating badge 'dry_master' (Points to achieve: 10)";
    $badge4 = new Badge;
    $badge4->name = 'DRY Master';
    $badge4->slug = 'dry_master';
    $badge4->points_to_achieve = 10;
    $badge4->technologies = 'css3, c9';
    $badge4->description = 'Dont repeat yourself';
    $badge4->save();
    echo "done  \n";
    
    $std1->badges()->attach($badge2);
    echo $std1->full_name." has now the badge ".$badge2->slug." \n";
    $std1->badges()->attach($badge3);
    echo $std1->full_name." has now the badge ".$badge3->slug." \n";
    $std2->badges()->attach($badge4);
    echo $std2->full_name." has now the badge ".$badge4->slug." \n";
    
    echo "Creating the profile 'full-stack-web'...";
    $fullstack = new Profile();
    $fullstack->name = "Full-Stack Web Developer";
    $fullstack->slug = "full-stack-web";
    $fullstack->description = "Manages front-end and back-end side of the web";
    $fullstack->save();
    echo "done  \n";
    
    echo "Creating specialty: 'front-end' with badges ".$badge4->slug.', '.$badge3->slug." for profile: ".$fullstack->name." \n";
    $specialty1 = new Specialty();
    $specialty1->slug = 'front-end';
    $specialty1->name = 'Font-End Developer';
    $specialty1->image_url = 'https://assets.breatheco.de/img/funny/baby.jpg';
    $specialty1->description = 'You have completed all the front end skills';
    $specialty1->save();
    $specialty1->badges()->attach($badge4);
    $specialty1->badges()->attach($badge3);
    $specialty1->students()->attach($std1);
    $specialty1->profiles()->attach($fullstack);
    echo "done  \n";
    
    echo "Creating a specialty: 'back-end' with badges ".$badge1->slug.', '.$badge2->slug." for profile: ".$fullstack->name." \n";
    $specialty2 = new Specialty();
    $specialty2->slug = 'back-end';
    $specialty2->name = 'Back-End Developer';
    $specialty2->image_url = 'https://assets.breatheco.de/img/funny/baby.jpg';
    $specialty2->description = 'You have completed all the backend end skills';
    $specialty2->save();
    $specialty2->badges()->attach($badge1);
    $specialty2->badges()->attach($badge2);
    $specialty2->students()->attach($std1);
    $specialty2->profiles()->attach($fullstack);
    echo "done  \n";