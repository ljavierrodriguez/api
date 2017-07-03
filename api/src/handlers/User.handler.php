<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class UserHandler extends MainHandler{
    
    public function getMe(Request $request, Response $response) {
        
        $session = new \Custom\Middleware\SessionHelper;
        $user_id = $session->get('user_id');
        $username = $session->get('user_name');
        return $this->success($response,[ 
            'user_id' => $user_id, 
            'username' => $username
        ]);
    }    
}