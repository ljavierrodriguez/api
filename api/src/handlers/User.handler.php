<?php

use Slim\Http\Request as Request;
use Slim\Http\Response as Response;

class UserHandler extends MainHandler{
    
    public function getMe(Request $request, Response $response) {
        
        $data = $request->getQueryParams();
        if(!empty($data['access_token']))
        {
            $storage = $this->app->storage;
            $user = $storage->getUserFromToken($data['access_token']);
            if(!empty($user) and isset($user['username'])) 
            {
                $bcUser = User::where('username', $user['username'])->first();
                if(!$bcUser) throw new Exception('This token does not correspond to any users');
                
                return $this->success($response,$bcUser);
                
            }else throw new Exception('This token does not correspond to any users');
        }else throw new Exception('No access_token provided');
        
        return $this->success($response,null);
        
    }    
    
    public function createCredentialsHandler(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if(empty($data)) throw new Exception('There was an error retrieving the request content, it needs to be a valid JSON');
    
        $user = User::where('username', $data['email'])->first();
        if(!$user)
        {
            $user = new User;
            $user->wp_id = $data['wp_id'];
            $user->type = $data['type'];
        }
        $user->username = $data['email'];
        $user->save();
        
        $storage = $this->app->storage;
        $oauthUser = $storage->setUserWithoutHash($data['email'], $data['password'], null, null);
        if(empty($oauthUser)){
            $user->delete();
            throw new Exception('Unable to create UserCredentials');
        }

        return $this->success($response,$user);
    }    
    
    public function deleteUser(Request $request, Response $response) {
        $userId = $request->getAttribute('user_id');
        if(empty($userId)) throw new Exception('There was an error retrieving the user_id');
    
        $user = User::find($userId);
        if(!$user) throw new Exception('User not found');
        
        $user->delete();

        return $this->success($response,'The user was deleted successfully');
    }    
    
}