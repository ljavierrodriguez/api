<?php

namespace Routes;

class BadgeRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the student badges
         **/
        $badgeHandler = new \BadgeHandler($app);
        $app->get('/badges/', array($badgeHandler, 'getAllHandler'));
        $app->get('/badge/{badge_id}', array($badgeHandler, 'getSingleBadge'));
        
        $app->get('/badges/student/{student_id}', array($badgeHandler, 'getAllStudentBadgesHandler'))->add($scopes(['read_talent_tree']));
        $app->post('/badge/', array($badgeHandler, 'createOrUpdateBadgeHandler'))->add($scopes(['super_admin']));
        $app->post('/badge/{badge_id}', array($badgeHandler, 'createOrUpdateBadgeHandler'))->add($scopes(['super_admin']));
        $app->delete('/badge/{badge_id}', array($badgeHandler, 'deleteBadgeHandler'))->add($scopes(['super_admin']));
        $app->post('/badge/image/{badge_id}', array($badgeHandler, 'updateThumbHandler'))->add($scopes(['super_admin']));
        
        $app->post('/badge/specialty/{specialty_id}', array($badgeHandler, 'addBadgesToSpecialtyHandler'))->add($scopes(['super_admin']));
        $app->delete('/badge/specialty/{specialty_id}', array($badgeHandler, 'deleteBadgesFromSpecialtyHandler'))->add($scopes(['super_admin']));

    }
    

}