<?php

/**
 * @todo Do something with configs
 */
class Spockz_Library {
	/**
	 * @var CI_Base
	 */
	protected $CI;
	
	/**
	 * @var Array
	 */
	protected $spockzConfig;
	
	function __construct() {
		$this->CI = get_instance();
		$this->spockzConfig = $this->CI->config->item('spockz');
		
		$this->autoloadLibraries();
	}
	
	protected function autoloadLibraries() {
	  assert(array_key_exists('autoload', $this->spockzConfig) && is_array($this->spockzConfig['autoload']));

	  foreach ($this->spockzConfig['autoload'] as $libraryName) {
	    $libraryVarName = strtolower($libraryName);
      if (isset($this->$libraryVarName)) {
        log_error('warning', sprintf('The property %1$s already exists while auto-loading library %2$s. Ignoring this library.', $libraryVarName, $libraryName));
        continue;
      }
      
//      $this->$libraryVarName = new $libraryName();
    }
	}

  /*public function __get($aProperty) {
    if (property_exists($this, $aProperty)) {
      return $this->{$aProperty};
    } else {
      user_error(sprintf('Property %s does not exist.', $aProperty), E_USER_WARNING);
    }
  }*/
  
  
  
  public static function autoload($aClassName) {
    $parts = explode('_', $aClassName);
    
    // Make sure we don't have identical prefixes, added by the sheer evil of CI!
    while($parts[0] == $parts[1]) { 
      array_shift($parts);
    }

    $extension = (defined('EXT') ? EXT : '.php');    
    $classFile = implode('/', $parts).$extension;
    $aClassName = implode('_', $parts);

    if ($aClassName == __CLASS__) {
      return True;
    }
    
    foreach (array(APPPATH.'libraries', BASEPATH.'libraries', '/var/www/externals/zend-framework/zend-framework-1.9.5/library') as $rootDir) {
      $lClassFile = $rootDir.'/'.$classFile;

      if (file_exists($lClassFile)) {
        require_once($lClassFile);
        return true;
      }
    }
    return false;
  }
  
  
}

abstract class Spockz_AbstractLibrary {
  /**
	 * @var CI_Base
	 */
	protected $CI;
	
	/**
	 * @var Array
	 */
	protected $spockzConfig;
  
  public function __construct() {
    $this->CI = get_instance();
		$this->spockzConfig = $this->CI->config->item('spockz');
  }
  
  
  public function loadConfig($aClassName) {
     $parts = explode('_', $aClassName);
  
    if ($parts[0] === 'Spockz') {
      array_shift($parts);
    }
    
    $result = $this->spockzConfig;
    foreach ($parts as $part) {
      if (is_array($result) && array_key_exists($part, $result)) {
        $result = $result[$part];
      } else {
        break;
      }
    }
    
    return $result;
  }
  
  protected function loadConfigFile($aFileName, $aConfigKey=null) {
    $this->CI->config->load($aFileName, True, True);
    if ($this->CI->config->item($aFileName) !== False) {
      return $this->CI->config->item($aFileName);
    } else {
      return array();
    }
  }
  
  protected function randomString($aLength=16) {
    $res = '';
    for ($i=0; $i < $aLength; ++$i) {
      $res .= chr(mt_rand(0, 255));
    }
    return $res;
  }
  
  protected function log_message($aLevel, $aMessage) {
    $dbg = debug_backtrace();
    $lClass  = $dbg[1]['class'];
    $lMethod = $dbg[1]['function'];
    
    log_message($aLevel, $lClass.'::'.$lMethod.' - '.$aMessage);
  }
}

spl_autoload_register('Spockz_Library::autoload');

class MissingRequirementException extends Exception {}
?>