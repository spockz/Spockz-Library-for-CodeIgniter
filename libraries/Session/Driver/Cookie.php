<?php
/**
 * Note: Please be aware that this type of session is prone to race conditions
 *       by design. Any system that doesn't use locking is prone to fall to
 *       race conditions at some time.
 *
 *       At this time I see no way to prevent this. A way should be provide
 *       locking. Whether or not this is possible with cookies I leave for
 *       someone else to decide.
 */
class Spockz_Session_Driver_Cookie extends Spockz_Session_Driver {  
  protected function __construct() {
  }
  
  /**
   * Reads the userdata from the driver source.
   * @param $aKey The key in the userdata that should be used to collect data
   * @param mixed Returns the data in the session or null if not found.
   */
  function userdata($aKey) {
    
  }

  /**
   * Reads the flashdata from the driver source.
   * @param $aKey The key in the flashdata that should be used to collect data
   * @param mixed Returns the flashdata in the session or null if not found.
   */  
   function flashdata($aKey) {
     
   }
  
  /**
   * Stores the <key, value> pair in the session. The value can later be
   * retrieved by calling userdata(key).
   * @param $aKey mixed, the key under which $aValue should be stored.
   * @param $aValue mixed, the data that should be stored under $aKey.
   */
  function set_userdata($aKey, $aValue) {
    
  }
  
  /**
   * Stores the <key, value> pair in the flashdata session. The value 
   * can later be retrieved by calling flashdata(key).
   * @param $aKey mixed, the key under which $aValue should be stored.
   * @param $aValue mixed, the data that should be stored under $aKey.
   */
  function set_flashdata($aKey, $aValue) {
    
  }
  
  /**
   * Deletes the key $aKey from the session, together with data stored within
   * the key.
   * @param $aKey The key that should be removed.
   */
  function unset_userdata($aKey) {
    
  }
  
  /**
   * Deletes the key $aKey from the flashdata, together with data stored within
   * the key.
   * @param $aKey The key that should be removed.
   */
  function unset_flashdata($aKey) {
    
  }
  
  /**
   * Returns all userdata that is stored in the current session.
   * @return array
   */
  function all_userdata() {
    
  }
  
  /**
   * Returns all flashdata that is stored in the current session.
   * @return array
   */
  function all_flashdata() {
    
  }
  
    
  /**
   * Destroys the session, removing all data stored in this session and
   * removing any reference to this session.
   */
  function destroy() {
    
  }
}
?>