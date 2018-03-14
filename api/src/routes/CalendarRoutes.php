<?php

namespace Routes;

class CalendarRoutes{
    
    public function __construct($app, $scopes){
        
        /**
         * Everything Related to the cohorts
         **/
        $calendarHandler = new \CalendarHandler($app);
        $app->get('/calendars/', array($calendarHandler, 'getAllCalendarsHandler'));//->add($scopes(['read_basic_info']));
        $app->get('/calendar/{calendar_id}', array($calendarHandler, 'getSingleCalendar'));//->add($scopes(['read_basic_info']));
        $app->post('/calendar/', array($calendarHandler, 'createCalendarHandler'))->add($scopes(['super_admin']));
        $app->post('/calendar/{calendar_id}/event', array($calendarHandler, 'createCalendarEventHandler'))->add($scopes(['super_admin']));
        $app->delete('/calendar/{calendar_id}', array($calendarHandler, 'deleteCalendarHandler'))->add($scopes(['super_admin']));
        $app->delete('/calendar/event/{event_id}', array($calendarHandler, 'deleteCalendarEventHandler'))->add($scopes(['super_admin']));
    }
    

}