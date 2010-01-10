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
/*
 * Created on 11 nov 2008
 */

# Requirements
# - helpers/spockz/lib_helper.php
# - libraries/spockz/Spockz_Library.php

# Prelude
// require_once(APPPATH.'/helpers/spockz/lib_helper.php');
// require_once(APPPATH.'/libraries/spockz/Library.php');


/**
 * CodeIgniter lib for authentication againt OpenID. Uses the PHP-OpenID library.
 * At the moment it expects you to run version 2.x.x of this lib.
 * 
 * @author Alessandro Vermeulen
 * @copyright Copyright &copy; 2008, Alessandro Vermeulen
 */
class Spockz_OpenID_Auth extends Spockz_Library {
	/**
	 * @var Auth_OpenID_FileStore
	 */
	protected $OpenIDStorage;
	/**
	 * @var Spockz_OpendIDSessionInterface Contains the interface to session variables for Yadis auth.
	 */
	protected $OpenIDSessionInterface;
	
	public function __construct() {
		parent::__construct();

		define('Auth_OpenID_RAND_SOURCE', $this->spockzConfig['OpenID']['randomSource']);
		
		$path = ini_get('include_path');
		$path = $this->spockzConfig['OpenID']['authPath']. PATH_SEPARATOR . $path;
		ini_set('include_path', $path);
		
		/** Make sure we have the required files from the lib **/
        require_once( LIBRARYPATH . DIRECTORY_SEPARATOR . "openid/Auth/OpenID/Consumer.php" );
		// File Storage module
		require_once( LIBRARYPATH . DIRECTORY_SEPARATOR . "openid/Auth/OpenID/FileStore.php" );
		// Simple Registration API
		require_once( LIBRARYPATH . DIRECTORY_SEPARATOR . "openid/Auth/OpenID/SReg.php" );
		// PAPE Extension
		require_once( LIBRARYPATH . DIRECTORY_SEPARATOR . "openid/Auth/OpenID/PAPE.php" );
		
		$this->OpenIDSessionInterface = new Spockz_OpenID_SessionInterface();		
		$this->setStore();
	}
	
	/**
	 * @throws InvalidOpenIDException if an invalid OpenID was provided
	 */
	public function authenticate($aOpenIdUrl, $aReturnTo, $aRealm, $aRequired = array('nickname'), $aOptional = array('fullname', 'email')) {
		$openID = $aOpenIdUrl;
		$OpenIDConsumer = new Auth_OpenID_Consumer($this->OpenIDStorage, $this->OpenIDSessionInterface);
	
		// Check whether the openID is valid
		if (trim($openID) != '') {
			$authRequest = $OpenIDConsumer->begin($openID);
		}

		// Check whether we have a valid auth_request
		if (is_null($authRequest)) {
            var_dump($openID);
			throw new InvalidOpenIDException($openID);
		}

		$simpleAuthRequest = Auth_OpenID_SRegRequest::build($aRequired, $aOptional);
		
		if (!is_null($simpleAuthRequest)) {
			$authRequest->addExtension($simpleAuthRequest);
		}
		
		// todo : determine what to do with policies
		
		// Redirect the user to the OpenID server for authentication.
	    // Store the token for this authentication so we can verify the
	    // response.
	
	    // For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
	    // form to send a POST request to the server.
	    if ($authRequest->shouldSendRedirect()) {
	        $redirectURL = $authRequest->redirectURL($aRealm,
	                                                   $aReturnTo);
	
	        // If the redirect URL can't be built, display an error
	        // message.
	        if (Auth_OpenID::isFailure($redirectURL)) {
	            displayError("Could not redirect to server: " . $redirectURL->message);
	        } else {
	            // Send redirect.
	            header("Location: ".$redirectURL);
	        }
	    } else {
	        // Generate form markup and render it.
	        $form_id = 'openid_message';
	        $form_html = $authRequest->htmlMarkup($aRealm, $aReturnTo,
	                                               false, array('id' => $form_id));
	
	        // Display an error if the form markup couldn't be generated;
	        // otherwise, render the HTML.
	        if (Auth_OpenID::isFailure($form_html)) {
	            displayError("Could not redirect to server: " . $form_html->message);
	        } else {
	            print $form_html;
	        }
	    }
		exit(0); // Make sure we do a clean exit.		
	}
	
	/**
	 * @todo check whether the url field causes errors due to rewriting
	 */
	public function getResponse($aReturnTo) {
		$OpenIDConsumer = new Auth_OpenID_Consumer($this->OpenIDStorage, $this->OpenIDSessionInterface);
		return $OpenIDConsumer->complete($aReturnTo, Auth_OpenID::getQuery());
	}
	
	/* Helper Functions */
	private function setStore() {
		$storePath = buildPath(array(APPPATH, 'cache', 'openid'));
		if (!file_exists($storePath) && !mkdir($storePath)) {
			throw new InvalidOpenIDCacheDirectoryException($storePath);
		}
		
		$this->OpenIDStorage = new Auth_OpenID_FileStore($storePath);
	}
	
	/* Example Helper Functions */ 
	private function getScheme() {
	    $scheme = 'http';
	    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
	        $scheme .= 's';
	    }
	    return $scheme;
	}
	
	private function getReturnTo($aPage) {
	    return sprintf("%s://%s:%s/".$aPage,
	                   $this->getScheme(), 
	                   $_SERVER['SERVER_NAME'], // todo : from config 3x
	                   $_SERVER['SERVER_PORT']
	                   );
	}
	
	private function getTrustRoot() {
	    return sprintf("%s://%s:%s%s/",
	                   getScheme(), $_SERVER['SERVER_NAME'], // todo : from config 3x
	                   $_SERVER['SERVER_PORT'],
	                   dirname($_SERVER['PHP_SELF']));
	}
}

class InvalidOpenIDException extends Exception {
	function __construct($aMessage) {
		parent::__construct(sprintf('The OpenID ("%s") you are using is not valid.', $aMessage));		
	}
}

class InvalidOpenIDCacheDirectoryException extends Exception{
	function __construct($aMessage) {
		parent::__construct(sprintf("Failed to use OpenID cache folder %s!", $aMessage));
	}
}
?>