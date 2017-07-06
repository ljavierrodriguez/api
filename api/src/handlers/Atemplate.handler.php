<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class AtemplateHandler extends MainHandler{
    
    protected $slug = 'Atemplate';
    
    public function createHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');

        $at = new Atemplate();
        $at->project_slug = $data['project_slug'];
        $at->title = $data['title'];
        $at->duration = $data['duration'];
        $at->technologies = $data['technologies'];
        $at->save();
        
        
        return $this->success($response,$at);
    }
    
    public function updateHandler(Request $request, Response $response) {
        $atId = $request->getAttribute('atemplate_id');
        $data = $request->getParsedBody();
        
        $at = Atemplate::find($atId);
        if(!$at) throw new Exception('Invalid template id: '.$atId);

        $at->project_slug = $data['project_slug'];
        $at->title = $data['title'];
        $at->duration = $data['duration'];
        $at->technologies = $data['technologies'];
        $at->save();
        
        return $this->success($response,$at);
    }
    
    public function deleteHandler(Request $request, Response $response) {
        $atId = $request->getAttribute('atemplate_id');
        
        $at = Atemplate::find($atId);
        if(!$at) throw new Exception('Invalid template id: '.$atId);
        
        $assingments = $at->assignments()->get();
        if(count($assingments)>0) throw new Exception('The template cannot be deleted because it has assingments');
        
        $at->delete();
        
        return $this->success($response,"The template was deleted");
    }
    
}