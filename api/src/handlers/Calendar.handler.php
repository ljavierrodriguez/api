<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

use Helpers\BCValidator;

class CalendarHandler extends MainHandler{
    
    protected $slug = 'Calendar';
    
    public function getAllCalendarsHandler(Request $request, Response $response) {
        
        $calendars = Calendar::all();
        
        $data = $request->getParams();
        if(!empty($data))
        {
            $filtered = $calendars->filter(function ($value, $key) use($data) {
                
                if(!empty($data["cohort"])) if($value->cohort_id != $data["cohort"]) return false;
                if(!empty($data["location"])) if($value->location_id != $data["location"]) return false;
                
                return true;
            });
            return $this->success($response,$filtered->values());
        }

        return $this->success($response,$calendars);
    }
    
    public function getSingleCalendar(Request $request, Response $response) {
        $calendarId = $request->getAttribute('calendar_id');
        
        $calendar = null;
        if(is_numeric($calendarId)) $calendar = Calendar::find($calendarId);
        else $calendar = Calendar::where('slug', $calendarId)->first();
        if(!$calendar) throw new Exception('Invalid calendar slug or id: '.$calendarId);
        
        return $this->success($response,$calendar);
    }
    
    public function createCalendarHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        if(!empty($data['location_id'])) throw new Exception('You must pass the location_slug instead of the ID');
        if(!empty($data['cohort_id'])) throw new Exception('You must pass the cohort_slug instead of the ID');
        
        $calendar = new Calendar();
        $calendar = $this->setMandatory($calendar,$data,'title',BCValidator::NAME);
        $calendar = $this->setMandatory($calendar,$data,'slug',BCValidator::SLUG);
        $calendar = $this->setOptional($calendar,$data,'description');

        if($data['cohort_slug'])
        {
            $cohort = Cohort::where('slug', $data['cohort_slug'])->first();
            if(!$cohort) throw new Exception('Invalid cohort_slug slug: '.$data['cohort_slug']);
            $calendar->cohort()->associate($cohort);
        }

        if($data['location_slug'])
        {
            $location = Location::where('slug', $data['location_slug'])->first();
            if(!$location) throw new Exception('Invalid location_slug slug: '.$data['location_slug']);
            $calendar->location()->associate($location);
        }
        
        $calendar->save();
        
        return $this->success($response,$calendar);
    }
    
    public function createCalendarEventHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
        
        if(empty($data['calendar_id'])) throw new Exception('You must specify a calendar id');
        $calendar = Calendar::find($data['calendar_id']);
        if(!$calendar) throw new Exception('Invalid calendar id: '.$data['calendar_id']);
        
        if(!empty($data['type']) && !in_array($data['type'], Calevent::$possibleTypes))
            throw new Exception('Invalid event type: '.$data['type']);
        
        $event = new Calevent();
        $event = $this->setMandatory($event,$data,'title',BCValidator::NAME);
        $event = $this->setMandatory($event,$data,'slug',BCValidator::SLUG);
        $event = $this->setMandatory($event,$data,'type',BCValidator::SLUG);
        $event = $this->setMandatory($event,$data,'event_date',BCValidator::DATETIME);
        $event = $this->setOptional($event,$data,'description');
        $calendar->events()->save($event);
        $event->save();
        
        return $this->success($response,$event);
    }
    
    public function deleteCalendarHandler(Request $request, Response $response) {
        $calendarId = $request->getAttribute('calendar_id');
        
        $calendar = Calendar::find($calendarId);
        if(!$calendar) throw new Exception('Invalid calendar id: '.$calendarId);
        
        $dates = $calendar->events()->get();
        if(count($dates)>0) throw new Exception('Remove all the dates from the calendar first');
        
        $calendar->delete();
        
        return $this->success($response,"The calendar was deleted successfully");
    }
    
    public function deleteCalendarEventHandler(Request $request, Response $response) {
        $eventId = $request->getAttribute('event_id');
        
        $event = Calevent::find($eventId);
        if(!$event) throw new Exception('Invalid event id: '.$eventId);
        $event->delete();
        
        return $this->success($response,"The event was deleted successfully");
    }
    
}