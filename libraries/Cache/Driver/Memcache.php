<?php
class Spockz_Cache_memcache {
  private $connection = null;
  private $conf = array();
  
  private $defaults = array('compress' => 0, 'port' => 11211);
  
  public function __construct(Array $aConf) {
    if (is_array($aConf['memcache'])) {
      $this->conf = $aConf['memcache'];
    }
    
    if (class_exists('Memcache')) {
      $this->connection = new Memcache;
      $this->connection->connect($this->conf['hostname'], $this->conf['port']);  
    }
  }
  
  public function get($aKey, $aDefault=null) {
    assert($this->isConnected());
    
    if ($this->isConnected()) {
      $result = $this->connection->get($aKey);
      if ($result === false)
        $result = $aDefault;
      return $result;
    }
    return $aDefault;
  }
  
  public function set($aKey, $aValue, $aTTL=60) {
    assert($this->isConnected());
    if ($this->isConnected()) {
      $compress = (isset($this->conf['compress']) ? $this->conf['compress'] : $this->defaults['compress']);
      $this->connection->set($aKey, $aValue, $compress, $aTTL);
    }
  }
  
  private function isConnected() {
    return !is_null($this->connection) && $this->connection->getVersion() !== False;
  }
}
?>