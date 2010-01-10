<?php
class Spockz_Cache extends Spockz_AbstractLibrary {
  private $driver;

  public function __construct() {
    parent::__construct();
    
    if (array_key_exists('cache', $this->spockzConfig) && is_array($this->spockzConfig['cache'])) {
      $this->conf = $this->loadConfig('spockz', 'cache'); 
    } else {
      $this->conf = array('ttl' => 1, 'driver' => 'Memcache', 
                        'memcache' => array('hostname' => 'localhost', 'port' => 11211), 
                        'filesystem' => array('cache_dir' => APPPATH.'/cache'));
    }

    $this->loadDriver($this->conf['driver']);
  }

  public function get($aKey, $aDefault=null) {
    return $this->driver->get($aKey, $aDefault);
  }
  
  public function set($aKey, $aValue, $aTTL=null) {
    if (is_null($aTTL)) $aTTL = $this->conf['ttl'];
    return $this->driver->set($aKey, $aValue, $aTTL);
  }
  
  private function loadDriver($aDriverName) {
    require_once('Driver/'.$aDriverName.'.php');
    $driverClassName = 'Spockz_Cache_'.$aDriverName;
    $this->driver = new $driverClassName($this->conf); // ToDo: Find some cleaner solution for this.
  }
}
?>