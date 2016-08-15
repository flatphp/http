<?php namespace Flatphp\Http;


abstract class Input
{
    protected $_input = [];
    protected $_sanitized = [];
    protected $_is_valid = true;
    protected $_message;

    public function __construct($input = null)
    {
        $this->_input = (null === $input) ? Request::input() : $input;
        $this->_sanitized = $this->_sanitize();
        $this->_is_valid = $this->_validate($this->_message);
    }

    /**
     * Validate input
     */
    protected function _validate(&$message = null)
    {
        return true;
    }

    /**
     * @return array
     */
    protected function _sanitize()
    {
        return $this->_input;
    }

    /**
     * If valid
     * @return boolean
     */
    public function isValid()
    {
        return $this->_is_valid;
    }

    /**
     * Get error message
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Get input data
     * @return array|null
     */
    public function raw($key = null, $default = null)
    {
        if (null === $key) {
            return $this->_input;
        } else {
            return isset($this->_input[$key]) ? $this->_input[$key] : $default;
        }
    }

    /**
     * @return mixed|null
     */
    public function all()
    {
        return $this->_sanitized;
    }

    /**
     * Get value
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->_sanitized[$key]) ? $this->_sanitized[$key] : null;
    }
}