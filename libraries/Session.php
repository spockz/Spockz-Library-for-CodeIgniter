<?php
class Spockz_Session extends Spockz_AbstractLibrary {
  /**
    * @var Spockz_Session_Driver
    */
  protected $driver;

  protected $requiredConfs = array('sess_encrypt_cookie', 'sess_expiration'
                                  ,'sess_match_ip', 'sess_match_useragent'
                                  ,'sess_cookie_name', 'cookie_prefix'
                                  ,'cookie_path', 'cookie_domain'
                                  ,'sess_time_to_update', 'flashdata_key'
                                  ,'time_reference', 'gc_probability');

  public function __construct() {
    parent::__construct();

    $config = $this->loadConfig(__CLASS__);

    # Read config and instantiate driver with correct options.
    assert(array_key_exists('driver', $config));

    # Read additional config:
    foreach ($this->requiredConfs as $requiredConf) {
      $config[$requiredConf] = $this->CI->config->item($requiredConf);
    }
    $driver = new $config['driver']($config);

    assert($driver instanceof Spockz_Session_Driver);



    $this->driver = $driver;
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