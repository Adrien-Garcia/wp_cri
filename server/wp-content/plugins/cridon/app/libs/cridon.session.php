<?php

/**
 * Description of cridon.session.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CriSession
{

    /**
     * CriSession constructor
     */
    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * Set SESSION var
     *
     * @param string $key
     * @param mixed $value
     */
    public function write($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get SESSION var
     *
     * @param null $key
     * @return bool
     */
    public function read($key = null)
    {
        if ($key) {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            } else {
                return false;
            }
        } else {
            return $_SESSION;
        }
    }

    /**
     * Delete SESSION var
     *
     * @param string $key
     */
    public function delete($key = '')
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}