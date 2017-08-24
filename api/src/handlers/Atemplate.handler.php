<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class AtemplateHandler extends MainHandler{
    
    protected $slug = 'Atemplate';
    
    public function createHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');

        if(isset($data['wp_id'])){
            $at = Atemplate::where('wp_id', $data['wp_id'])->first();
            if($at) throw new Exception('There is already a Project Template with this '.$data['wp_id']);
        } 

        if(isset($data['project_slug'])){
            $at = Atemplate::where('project_slug', $data['project_slug'])->first();
            if($at) throw new Exception('There is already a Project Template with project_slug '.$data['project_slug']);
        } 

        $at = new Atemplate();
        $at->project_slug = $data['project_slug'];
        $at->title = $data['title'];
        $at->duration = $data['duration'];
        $at->technologies = $data['technologies'];
        $at = $this->setOptional($at,$data,'excerpt');
        $at = $this->setOptional($at,$data,'wp_id');
        $at->save();
        
        return $this->success($response,$at);
    }
    
    public function syncFromWPHandler(Request $request, Response $response) {
        $wp_id = $request->getAttribute('wp_id');
        
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');

        $at = Atemplate::where('wp_id', $wp_id)->first();
        if(!empty($data['project_slug']) && (!$at || $at->project_slug != $data['project_slug']))
        {
            $otherAt = Atemplate::where('project_slug', $data['project_slug'])->first();
            if($otherAt) throw new Exception('There is another template with this slug: '.$data['project_slug']);
        }
        
        if(!$at){
            $at = new Atemplate();
            $at->project_slug = $data['project_slug'];
            $at->title = $data['title'];
            $at->duration = $data['duration'];
            $at->technologies = $data['technologies'];
            $at->wp_id = $wp_id;
        }
        else{
            $at = $this->setOptional($at,$data,'project_slug');
            $at = $this->setOptional($at,$data,'title');  
            $at = $this->setOptional($at,$data,'duration');  
        }
        
        $at = $this->setOptional($at,$data,'excerpt');
        $at->save();
        
        return $this->success($response,$at);
    }
    
    public function updateHandler(Request $request, Response $response) {
        $atId = $request->getAttribute('atemplate_id');
        $data = $request->getParsedBody();
        
        $at = Atemplate::find($atId);
        if(!$at){
            $at = Atemplate::where('project_slug', $data['project_slug'])->first();
            if(!$at) throw new Exception('Invalid template id or slug: '.$atId);
        }

        $at = $this->setOptional($at,$data,'project_slug');
        $at = $this->setOptional($at,$data,'title');
        $at = $this->setOptional($at,$data,'excerpt');
        $at = $this->setOptional($at,$data,'duration');
        $at = $this->setOptional($at,$data,'wp_id');
        $at = $this->setOptional($at,$data,'technologies');
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