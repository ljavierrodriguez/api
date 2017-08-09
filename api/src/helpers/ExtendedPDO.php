<?php


class ExtendedPDO extends \OAuth2\Storage\Pdo{
    
    public function getUserFromToken($token)
    {
        $stmt = $this->db->prepare($sql = sprintf('SELECT u.* from %s as u,%s as oat where u.username=oat.user_id and oat.access_token =:token', $this->config['user_table'],$this->config['access_token_table']));
        $stmt->execute(array('token' => $token));

        if (!$userInfo = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return false;
        }

        // the default behavior is to use "username" as the user_id
        return $userInfo;
    }
    
    public function setUserWithoutHash($username, $password, $firstName = null, $lastName = null)
    {
        // if it exists, update it.
        if ($this->getUser($username)) {
            $stmt = $this->db->prepare($sql = sprintf('UPDATE %s SET password=:password, first_name=:firstName, last_name=:lastName where username=:username', $this->config['user_table']));
        } else {
            $stmt = $this->db->prepare(sprintf('INSERT INTO %s (username, password, first_name, last_name) VALUES (:username, :password, :firstName, :lastName)', $this->config['user_table']));
        }

        return $stmt->execute(compact('username', 'password', 'firstName', 'lastName'));
    }
    
    // use a secure hashing algorithm when storing passwords. Override this for your application
    protected function hashPassword($password)
    {
        $hasher = new PasswordHash(8, true);
        return $hasher->HashPassword( trim( $password ) );
    }
    
    // plaintext passwords are bad!  Override this for your application
    protected function checkPassword($user, $password)
    {
        $hasher = new PasswordHash(8, true);
        $result = $hasher->CheckPassword($password,$user['password']);

        return $result;
    }
}