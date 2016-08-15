# Http
Http Lib.

# Installation
```php
composer require "flatphp/http"
```

# Request
```
use Flatphp\Http\Request;
$username = Request::get('username');
$hello = Request::post('hello', 'world');
$sanitized = Request::get('test', '', FILTER_SANITIZE_ENCODED, FILTER_FLAG_ENCODE_LOW);
```

# Input
```
use Flatphp\Http\Input;
class LoginInput extends Input
{
    protected function _sanitize()
    {
	return array(
	    'username' => trim($this->raw('username'))
	);
    }

    protected function _validate(&$message = '')
    {
	if (empty($this->raw('username')) || empty($this->raw('password'))) {
	    $message = 'please input username and password';
	    return false;
	}
	return true;
    }
}

$login_input = new LoginInput();
if ($login_input->isValid()) {
    // do login
} else {
    echo $login_input->getMessage();
}
```
