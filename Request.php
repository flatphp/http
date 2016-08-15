<?php namespace Flatphp\Http;

class Request
{
    /**
     * POST and GET
     * @param $key
     * @param mixed $default
     * @return mixed
     */
    public static function input($key = null, $default = null)
    {
        if (null === $key) {
            return array_replace_recursive(self::get(), self::post());
        } else {
            return filter_has_var(INPUT_POST, $key) ? self::post($key, $default) : self::get($key, $default);
        }
    }

    /**
     * Get a get request data
     * default filter by FILTER_SANITIZE_STRING + FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
     * @param string $key
     * @param mixed $default
     * @param int $filter
     * @param int $flags
     * @return mixed
     */
    public static function get($key = null, $default = null, $filter = FILTER_SANITIZE_STRING, $flags = FILTER_FLAG_STRIP_LOW)
    {
        if (null === $key) {
            return filter_var($_GET, $filter, FILTER_REQUIRE_ARRAY|$flags);
        }
        if (!isset($_GET[$key])) {
            return $default;
        }
        if (is_array($_GET[$key])) {
            return filter_input(INPUT_GET, $key, $filter, array(
                'flags' => FILTER_REQUIRE_ARRAY|$flags,
                'options' => ['default' => $default]
            ));
        } else {
            return filter_input(INPUT_GET, $key, $filter, array(
                'flags' => $flags,
                'options' => ['default' => $default]
            ));
        }
    }

    /**
     * @param $key
     * @param int $default
     * @return int
     */
    public static function getInt($key, $default = 0)
    {
        return isset($_GET[$key]) ? (int)$_GET[$key] : (int)$default;
    }

    /**
     * Get a post request data
     * @param string $key
     * @param mixed $default
     * @param int $filter
     * @param int $flags
     * @return mixed
     */
    public static function post($key = null, $default = null, $filter = FILTER_UNSAFE_RAW, $flags = null)
    {
        if (null === $key) {
            return $_POST;
        }
        if (!isset($_POST[$key])) {
            return $default;
        }
        if (is_array($_POST[$key])) {
            return filter_input(INPUT_POST, $key, $filter, array(
                'flags' => FILTER_REQUIRE_ARRAY|$flags,
                'options' => ['default' => $default]
            ));
        } else {
            return filter_input(INPUT_POST, $key, $filter, array(
                'flags' => $flags,
                'options' => ['default' => $default]
            ));
        }
    }

    /**
     * @param $key
     * @param int $default
     * @return int
     */
    public static function postInt($key, $default = 0)
    {
        return isset($_POST[$key]) ? (int)$_POST[$key] : (int)$default;
    }


    /**
     * Get all files uploaded
     * @return array
     */
    public static function files()
    {
        return $_FILES;
    }

    /**
     * Get a file uploaded
     * @param $key
     * @return array|null
     */
    public static function file($key)
    {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    /**
     * Get ip address safely
     * @return string
     */
    public static function ip()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key){
            if (array_key_exists($key, $_SERVER)){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return '';
    }

    /**
     * Get host
     */
    public static function host()
    {
        return filter_input(INPUT_SERVER, 'HTTP_HOST');
    }


    /**
     * Get REQUEST_URI
     * @return mixed
     */
    public static function uri()
    {
        return filter_input(INPUT_SERVER, 'REQUEST_URI');
    }

    /**
     * Get script name
     * @return mixed
     */
    public static function script()
    {
        return filter_has_var(INPUT_SERVER, 'SCRIPT_NAME') ? filter_input(INPUT_SERVER, 'SCRIPT_NAME') : filter_input(INPUT_SERVER, 'PHP_SELF');
    }

    /**
     * Get url base path
     * @return string
     */
    public static function base()
    {
        return rtrim(dirname(self::script()), '\\/');
    }

    /**
     * Get url path
     * @return mixed
     */
    public static function path()
    {
        $script = self::script();
        $base = self::base();
        $uri_path = self::uri();
        if (($poz = strpos($uri_path, '?')) !== false) {
            $uri_path = substr($uri_path, 0, $poz);
        }
        if ($script && strpos($uri_path, $script) === 0) {
            $uri_path = substr($uri_path, strlen($script));
        } elseif ($base && strpos($uri_path, $base) === 0) {
            $uri_path = substr($uri_path, strlen($base));
        }
        return preg_replace('/\/+/', '/', trim($uri_path, '/'));
    }


    /**
     * Check if is post
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    /**
     * Check if is get
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * Check if is ajax
     */
    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

}