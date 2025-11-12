<?php

namespace Cookies\Session;

class SessionManager
{
    private $namespace;
    private $config;

    /**
     * @param string $namespace
     * @param array $config
     */
    public function __construct($namespace = 'default', array $config = [])
    {
        $this->namespace = $namespace;
        $this->config = array_merge([
            'lifetime' => 1800, // cookie lifetime
            'idle_timeout' => 900, // 15 minutes idle expiration
            'path' => '/',
            'domain' => '',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
            'name' => 'PHPSESSID'
        ], $config);

        $this->configureSession();
        $this->startSession();
        $this->handleExpiration();

        if (!isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = [];
        }

        // Update last activity time
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Configure secure session cookie parameters.
     */
    private function configureSession()
    {
        session_name($this->config['name']);

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => $this->config['lifetime'],
                'path' => $this->config['path'],
                'domain' => $this->config['domain'],
                'secure' => $this->config['secure'],
                'httponly' => $this->config['httponly'],
                'samesite' => $this->config['samesite']
            ]);
        } else {
            session_set_cookie_params(
                $this->config['lifetime'],
                $this->config['path'],
                $this->config['domain'],
                $this->config['secure'],
                $this->config['httponly']
            );
        }
    }

    /**
     * Start the session safely.
     */
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check and enforce session idle timeout.
     */
    private function handleExpiration()
    {
        if (isset($_SESSION['_last_activity'])) {
            $idle = time() - $_SESSION['_last_activity'];
            if ($idle > $this->config['idle_timeout']) {
                $this->destroy();
                session_start(); // restart clean session
            }
        }
    }

    /**
     * Set a session value.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $_SESSION[$this->namespace][$key] = $value;
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Get a session value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $_SESSION['_last_activity'] = time();
        return isset($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : $default;
    }

    /**
     * Check if a key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($_SESSION[$this->namespace][$key]);
    }

    /**
     * Remove a session key.
     *
     * @param string $key
     */
    public function remove($key)
    {
        unset($_SESSION[$this->namespace][$key]);
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Get all session data for this namespace.
     *
     * @return array
     */
    public function all()
    {
        $_SESSION['_last_activity'] = time();
        return $_SESSION[$this->namespace];
    }

    /**
     * Clear all session data for this namespace.
     */
    public function clear()
    {
        $_SESSION[$this->namespace] = [];
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Regenerate the session ID.
     *
     * @param bool $deleteOldSession
     */
    public function regenerate($deleteOldSession = true)
    {
        session_regenerate_id($deleteOldSession);
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Destroy the session completely.
     */
    public function destroy()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}
