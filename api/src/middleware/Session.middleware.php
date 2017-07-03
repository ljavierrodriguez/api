<?php
namespace Custom\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Session {
    
    protected $options = array(
        'name'          => 'sessie',
        'lifetime'      => 3600,
        'path'          => '/',
        'domain'        => null,
        'secure'        => false,
        'httponly'      => true,
        'cache_limiter' => 'nocache',
        'autorefresh'   => false
    );
    
    public function __construct(array $options = array()) {
        if (isset($options['lifetime']) and is_string($options['lifetime'])) {
            $options['lifetime'] = strtotime($options['lifetime']) - time();
        }
    
        $this->options = array_merge($this->options, $options);
    }
    
    public function __invoke(Request $request, Response $response, callable $next) {
        $this->startSession();
    
        return $next($request, $response);
    }
    
    
    protected function startSession() {
        $options = $this->options;
        session_set_cookie_params($options['lifetime'], $options['path'], $options['domain'], $options['secure'], $options['httponly']);
        if (session_id()) {
            if ($options['autorefresh'] === true && isset($_COOKIE[$options['name']]) && ini_get('session.use_cookies')) {
                setcookie($options['name'], $_COOKIE[$options['name']], time() + $options['lifetime'], $options['path'], $options['domain'], $options['secure'], $options['httponly']);
            }
        }
        session_name($options['name']);
        session_cache_limiter(false);
        session_start();
    }
}