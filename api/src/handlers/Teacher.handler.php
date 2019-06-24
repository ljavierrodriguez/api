<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use Helpers\ArgumentException;

class TeacherHandler extends MainHandler{

    protected $slug = 'Teacher';

    public function getCohortTeachers(Request $request, Response $response) {
        $cohortId = $request->getAttribute('cohort_id');

        //retrieve the cohort by slug or id
        $cohort = null;
        if(is_numeric($cohortId)) $cohort = Cohort::find($cohortId);
        else $cohort = Cohort::where('slug', $cohort_id)->first();
        if(!$cohort) throw new ArgumentException('Invalid cohort id: '.$cohortId);

        return $this->success($response,$cohort->teachers()->get());
    }

    public function deleteTeacherHandler(Request $request, Response $response) {
        $teacherId = $request->getAttribute('teacher_id');

        $teacher = Teacher::find($teacherId);
        if(!$teacher) throw new ArgumentException('Invalid teacher id');

        /*
        $attributes = $teacher->getAttributes();
        $now = time(); // or your date as well
        $daysOld = floor(($now - strtotime($attributes['created_at'])) / DELETE_MAX_DAYS);
        if($daysOld>5) throw new ArgumentException('The teacher is to old to delete');
        */
        $teacher->delete();

        return $this->success($response,"ok");
    }

    public function reviewAssignment(Request $request, Response $response) {
        $taskId = $request->getAttribute('task_id');

        $data = $request->getParsedBody();
        if(!$data)  throw new ArgumentException('Error parsing the request (invalid JSON)');

        $task = Task::find($taskId);
        if(!$task) throw new ArgumentException('Invalid task id');

        if(!in_array($data['revision_status'],Task::$revisionStages)) throw new ArgumentException("Invalid revision_status ".$data['revision_status'].", the only valid revision_status are: ".implode(',',Task::$revisionStages));

        try{
            if($task->type !== 'assignment' or $task->status !== 'done')
                throw new ArgumentException("Only delivered assignments can be marke as approved or rejected", 400);

            $task->revision_status = $data['revision_status']; //pending
            $task->save();

        }
        catch(ArgumentException $e){
            throw $e;
        }

        return $this->success($response,$task);
    }

}