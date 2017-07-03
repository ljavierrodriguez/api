<?php

    require_once('../../vendor/autoload.php');
    require_once('../dependencies.php');

    $schema = $app->db->getSchemaBuilder();
    $app->db->listen(function($sql) {
        var_dump($sql);
    });
    $schema->disableForeignKeyConstraints();
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
    
    echo "Creating a user 1...";
    $u1 = new User;
    $u1->username = 'john@4geeks.co';
    $u1->save();
    echo "done  \n";
    echo "Creating a user 2...";
    $u2 = new User;
    $u2->username = 'pedro@4geeks.co';
    $u2->save();
    echo "done  \n";
    echo "Creating a user 3...";
    $u3 = new User;
    $u3->username = 'mickel@4geeks.co';
    $u3->save();
    echo "done  \n";
    
    
    echo "Creating student 1...";
    $std1 = new Student;
    $std1->full_name = 'John';
    $std1->email = 'john@4geeks.co';
    $u1->student()->save($std1);
    $std1->save();
    echo "done  \n";
    
    
    echo "Creating student 2...";
    $std2 = new Student;
    $std2->full_name = 'Pedro';
    $std2->email = 'pedro@4geeks.co';
    $u2->student()->save($std2);
    $std2->save();
    echo "done  \n";
    
    echo "Creating student 3...";
    $std3 = new Student;
    $std3->full_name = 'Mickel';
    $std3->email = 'mickel@4geeks.co';
    $u3->student()->save($std3);
    $std3->save();
    echo "done  \n";

    $badge1 = new Badge;
    $badge1->name = 'CSS Selectors';
    $badge1->slug = 'css_selectors';
    $badge1->points_to_achieve = 10;
    $badge1->technologies = 'css3';
    $badge1->description = 'Select everything';
    $badge1->save();
    echo "Creating a badge ".$badge1->slug.' (Points to achieve: '.$badge1->points_to_achieve.") \n";
    
    $badge2 = new Badge;
    $badge2->name = 'Shorcut Everything';
    $badge2->slug = 'keyboard_shortcuts';
    $badge2->points_to_achieve = 10;
    $badge2->technologies = 'sublime, c9';
    $badge2->description = 'Learn and use the keyboards';
    $badge2->save();
    echo "Creating a badge ".$badge2->slug.' (Points to achieve: '.$badge2->points_to_achieve.") \n";

    $badge3 = new Badge;
    $badge3->name = 'Clean Code';
    $badge3->slug = 'clean_code';
    $badge3->points_to_achieve = 10;
    $badge3->technologies = 'css3, html5, js, sublime';
    $badge3->description = 'Have a commented and clean code';
    $badge3->save();
    echo "Creating a badge ".$badge3->slug.' (Points to achieve: '.$badge3->points_to_achieve.") \n";
    
    $badge4 = new Badge;
    $badge4->name = 'DRY Master';
    $badge4->slug = 'dry_master';
    $badge4->points_to_achieve = 10;
    $badge4->technologies = 'css3, c9';
    $badge4->description = 'Dont repeat yourself';
    $badge4->save();
    echo "Creating a badge ".$badge4->slug.' (Points to achieve: '.$badge4->points_to_achieve.") \n";
    
    $std1->badges()->attach($badge2);
    echo $std1->full_name." has the badge ".$badge2->slug." \n";
    $std1->badges()->attach($badge3);
    echo $std1->full_name." has the badge ".$badge3->slug." \n";
    $std1->badges()->attach($badge4);
    echo $std2->full_name." has the badge ".$badge4->slug." \n";
    
    $fullstack = new Profile();
    $fullstack->name = "Full-Stack Web Developer";
    $fullstack->slug = "full-stack-web";
    $fullstack->description = "Manages front-end and back-end side of the web";
    $fullstack->save();
    echo "The profile ".$fullstack->name." has been created"." \n";
    
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
    echo 'Creating a specialty: '.$specialty1->slug." with badges ".$badge4->slug.', '.$badge3->slug." for profile: ".$fullstack->name." \n";
    
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
    echo 'Creating a specialty: '.$specialty2->slug." with badges ".$badge1->slug.', '.$badge2->slug." for profile: ".$fullstack->name." \n";