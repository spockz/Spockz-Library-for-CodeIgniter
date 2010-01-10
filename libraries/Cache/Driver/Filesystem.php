<?php
class Spockz_Cache_filesystem {
  private $conf = array();
  private $defaults = array();
  
  public function __construct(Array $aConf) {
    assert(isset($aConf['filesystem']) && is_array($aConf['filesystem']));
    
    $this->conf = $aConf['filesystem'];
    
    assert(isset($this->conf['cache_dir']));
  }
  
  public function get($aKey, $aDefault=null) {
    $cacheFile = $this->cacheFile($aKey);
    
    if (!file_exists($cacheFile)) {
      return $aDefault;
    }
    
    if (time() - filemtime($cacheFile) < $this->getFileTTL($aKey)) {
      $result = unserialize(file_get_contents($cacheFile));
      return ($result === false ? $aDefault : $result);
    }
      
    return $aDefault;  
  }
  
  public function set($aKey, $aValue, $aTTL=60) {
    $this->setFileTTL($aKey, $aTTL);
    file_put_contents($this->cacheFile($aKey), serialize($aValue), LOCK_EX);
  }
  
  private function isConnected() {
    return !is_null($this->connection) && $this->connection->getVersion() !== False;
  }
  
  private function cacheFile($aKey) {
    return $this->conf['cache_dir'].DIRECTORY_SEPARATOR.$aKey;
  }
  
  private function setFileTTL($aKey, $aTTL) {
    $ttls = unserialize(file_get_contents($this->cacheFile('.ttl')));
    $ttls[$aKey] = $aTTL;
    file_put_contents($this->cacheFile('.ttl'), serialize($ttls), LOCK_EX);
  }
  
  private function getFileTTL($aKey) {
    $ttls = unserialize(file_get_contents($this->cacheFile('.ttl')));
    return (isset($ttls[$aKey]) ? $ttls[$aKey] : 0);
  }
}
?>