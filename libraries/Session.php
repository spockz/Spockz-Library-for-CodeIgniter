<?php
class Spockz_Session extends Spockz_AbstractLibrary {
  /**
    * @var Spockz_Session_Driver
    */
  protected $driver;
  
  public function __construct(Spockz_Session_Driver $aDriver) {
    $this->loadConfig(__CLASS__);
    
    # Read config and instantiate driver with correct options.
    $this->driver = $aDriver;
  }
    
  public function __call($aMethod, $aArgs) {
    assert(!is_null($this->driver));
    call_user_func_array(array($this->driver, $aMethod), $aArgs);
  }
  
  public function userdata($aKey) {
    assert(!is_null($this->driver));
    return $this->driver->userdata($aKey);
  }
  
  public function all_userdata() {
    return $this->driver->all_userdata();
  }

  public function flashdata($aKey) {
    assert(!is_null($this->driver));
    return $this->driver->flashdata($aKey);
  }
  
  public function set_userdata($aKey, $aValue) {
    assert(!is_null($this->driver));
    return $this->driver->set_userdata($aKey, $aValue);
  }
  
  public function set_flashdata($aKey, $aValue) {
    assert(!is_null($this->driver));
    return $this->driver->set_flashdata($aKey, $aValue);
  }
}
?>