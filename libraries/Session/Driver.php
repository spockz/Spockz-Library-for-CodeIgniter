<?php
abstract class Spockz_Session_Driver extends Spockz_AbstractLibrary {
  /**
   * Reads the userdata from the driver source.
   * @param $aKey The key in the userdata that should be used to collect data
   * @param mixed Returns the data in the session or null if not found.
   */
  abstract function userdata($aKey);

  /**
   * Reads the flashdata from the driver source.
   * @param $aKey The key in the flashdata that should be used to collect data
   * @param mixed Returns the flashdata in the session or null if not found.
   */  
  abstract function flashdata($aKey);
  
  /**
   * Stores the <key, value> pair in the session. The value can later be
   * retrieved by calling userdata(key).
   * @param $aKey mixed, the key under which $aValue should be stored.
   * @param $aValue mixed, the data that should be stored under $aKey.
   */
  abstract function set_userdata($aKey=array(), $aValue='');
  
  /**
   * Stores the <key, value> pair in the flashdata session. The value 
   * can later be retrieved by calling flashdata(key).
   * @param $aKey mixed, the key under which $aValue should be stored.
   * @param $aValue mixed, the data that should be stored under $aKey.
   */
  abstract function set_flashdata($aKey=array(), $aValue='');
  
  /**
   * Deletes the key $aKey from the session, together with data stored within
   * the key.
   * @param $aKey The key that should be removed.
   */
  abstract function unset_userdata($aKey=array());
  
  /**
   * Deletes the key $aKey from the flashdata, together with data stored within
   * the key.
   * @param $aKey The key that should be removed.
   */
//  abstract function unset_flashdata($aKey=array());
  
  /**
   * Returns all userdata that is stored in the current session.
   * @return array
   */
  abstract function all_userdata();
  
  /**
   * Returns all flashdata that is stored in the current session.
   * @return array
   */
//  abstract function all_flashdata();
  
    
  /**
   * Destroys the session, removing all data stored in this session and
   * removing any reference to this session.
   */
  abstract function session_destroy();
}
?>