<?php
/*
Copyright (c) 2008 Alessandro Vermeulen <avermeulen@spockz.nl>

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

# Requirements
# - OpenID PHP lib
# - Auth_Yadis_PHPSession
# - CI Session Class

/*
 * Created on 13 nov 2008
 */
class Spockz_OpenID_SessionInterface extends Auth_Yadis_PHPSession {
	protected $CI = null;
	function __construct() {
		$this->CI = get_instance();
	}
	
	/**
     * Set a session key/value pair.
     *
     * @param string $name The name of the session key to add.
     * @param string $value The value to add to the session.
     */
    function set($name, $value)
    {
        $this->CI->session->set_userdata($name, $value);
    }

    /**
     * Get a key's value from the session.
     *
     * @param string $name The name of the key to retrieve.
     * @param string $default The optional value to return if the key
     * is not found in the session.
     * @return string $result The key's value in the session or
     * $default if it isn't found.
     */
    function get($name, $default=null)
    {
    	if (($val = $this->CI->session->userdata($name)) !== False) {
    		return $val;
    	} else {
    		return $default;
    	}
    }

    /**
     * Remove a key/value pair from the session.
     *
     * @param string $name The name of the key to remove.
     */
    function del($name)
    {
    	$this->CI->session->unset_userdata($name);
    }

    /**
     * Return the contents of the session in array form.
     */
    function contents()
    {
        return $this->CI->session->all_userdata();
    }
}
?>
