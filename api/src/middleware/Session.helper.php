<?php
namespace Custom\Middleware;

class SessionHelper {
    public function get($key) {
        return $this->keyExists($key) ? $_SESSION[$key] : false;
    }
    
    public function set($key, $value, $overwrite = false) {
        if ($this->keyExists($key) === false || $overwrite === true) {
            $_SESSION[$key] = $value;
            return true;
        }
        return false;
    }
    
    public function delete($key) {
        if ($this->keyExists($key) === false) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    
    public static function id($new = false) {
        if ($new === true && session_id()) {
            session_regenerate_id(true);
        }
        return session_id() ? : false;
    }
    
    public static function destroy() {
        if (self::id()) {
            session_unset();
            session_destroy();
            session_write_close();
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 4200, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
        }
    }
    
    protected function keyExists($key) {
        return array_key_exists($key, $_SESSION);
    }
    
    public function __get($key) {
        return $this->get($key);
    }
    
    public function __set($key, $value) {
        $this->set($key, $value);
    }
    
    public function __unset($key) {
        $this->delete($key);
    }
    
    public function __isset($key) {
        return $this->exists($key);
    }
}