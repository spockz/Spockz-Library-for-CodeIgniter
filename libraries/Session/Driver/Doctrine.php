<?php
class Spockz_Session_Driver_Doctrine extends Spockz_Session_Driver {
  protected $options = array('sess_encrypt_cookie'  => False
                            ,'sess_expiration'      => 7200
                            ,'sess_match_ip'        => False
                            ,'sess_match_useragent' => True
                            ,'sess_cookie_name'     => 'sp_session'
                            ,'cookie_prefix'        => ''
                            ,'cookie_path'          => '/'
                            ,'cookie_domain'        => ''
                            ,'sess_time_to_update'  => 300
                            ,'flashdata_key'        => 'flash'
                            ,'time_reference'       => 'time'
                            ,'gc_probability'       => 5);
  
  /**
   * @var Session
   */
  protected $session;
  
  /**
   * @var array
   */
  protected $flashdata;
  
  // ---------------------------------------------------------------------------
  
  public function __construct($aOptions=array()) {
    parent::__construct();
    assert(class_exists('Spockz_Session_Model'));

    $this->options = array_merge($this->options, $aOptions);
    
    if( $this->read() ) {
      $this->update();
    } else {
      $this->create();
    }
  }
  
  // ---------------------------------------------------------------------------
  
  /**
   * Reads the userdata from the driver source.
   * @param $aKey The key in the userdata that should be used to collect data
   * @param mixed Returns the data in the session or null if not found.
   */
  public function userdata($aKey) {
    return array_key_exists($aKey, $this->session->userdata) ? $this->session->userdata[$aKey] 
                                                             : null;
  }
  
  // ---------------------------------------------------------------------------

  /**
   * Reads the flashdata from the driver source.
   * @param $aKey The key in the flashdata that should be used to collect data
   * @param mixed Returns the flashdata in the session or null if not found.
   */  
  public function flashdata($aKey) {
    return array_key_exists($aKey, $this->session->flashdata) ? $this->session->flashdata[$aKey]
                                                     : null;
  }
  
  // ---------------------------------------------------------------------------
  
  /**
   * Stores the <key, value> pair in the session. The value can later be
   * retrieved by calling userdata(key).
   * @param $aKey mixed, the key under which $aValue should be stored.
   * @param $aValue mixed, the data that should be stored under $aKey.
   */
  public function set_userdata($aKey=array(), $aValue='') {
    if (is_string($aKey)) {
      $aKey = array($aKey => $aValue);
    }
    
    $this->session->userdata = array_merge($this->session->userdata, $aKey);
    
    $this->write();
  }
  
  // ---------------------------------------------------------------------------
  
  /**
   * Stores the <key, value> pair in the flashdata session. The value 
   * can later be retrieved by calling flashdata(key).
   * @param $aKey mixed, the key under which $aValue should be stored.
   * @param $aValue mixed, the data that should be stored under $aKey.
   */
  public function set_flashdata($aKey=array(), $aValue='') {
    if (is_string($aKey)) {
      $aKey = array($aKey => $aValue);
    }
    
    $this->session->userdata = array_merge($this->session->userdata, $aKey);
    
    $this->write();
  }
  
  // ---------------------------------------------------------------------------
  
  /**
   * Deletes the key $aKey from the session, together with data stored within
   * the key.
   * @todo Fix this with references so we don't have to copy the whole arrays
   *       and stuff.
   * @param $aKey The key that should be removed.
   */
  public function unset_userdata($aKey=array()) {
    if (is_string($aKey))
      $aKey = array($aKey);
      
    $tArray = $this->session->userdata;
      
    foreach ($aKey as $key) 
      unset($tArray[$key]);

    $this->session->userdata = $tArray;
     
    $this->write();
  }
  
  // ---------------------------------------------------------------------------
  
  /**
   * Returns all userdata that is stored in the current session.
   * @return array
   */
  public function all_userdata() {
    return $this->session->userdata;
  }

  // ---------------------------------------------------------------------------
  
  /**
   * Destroys the session, removing all data stored in this session and
   * removing any reference to this session.
   */
  public function session_destroy() {
    # Remove cookie
    remove_cookie($this->sign($this->session->id, $this->session->salt));
    
    # Remove session from database
    $this->session->delete();
  }
  
  // ---------------------------------------------------------------------------
  
  protected function read() {
    $cookie = $this->CI->input->cookie($this->options['sess_cookie_name']);
    
    $cookieSessionID = substr($cookie, 0, 40);
    $cookieSignature = substr($cookie, 40);
    
    if ($cookie === False) return False;
    
    $this->session = Doctrine::getTable('Spockz_Session_Model')->find($cookieSessionID);

    # Run checks
    if ($this->session === False) {
      $this->log_message('debug', 'Session id not in database.');
      return False;
    }
    
    if ($this->options['sess_match_ip']
        && $this->session->ip_address !== $this->CI->input->ip_address()) {
      return False;     
    }
    
    if ($this->options['sess_match_useragent']
       && $this->session->user_agent !== $this->user_agent()) {
      return False;       
    }
    
    # Check signature
    if (!$this->check( $cookieSessionID
                     , $this->session->salt
                     , $cookieSignature)) {
      return False;                   
    }
    
    # All clear now
    # Copy flashdata to local and replace it with an empty array
    $this->flashdata = $this->session->flashdata;
    $this->session->flashdata = array();

    return True;
  }
  
  // ---------------------------------------------------------------------------
  
  protected function update() {
    if (time() - $this->session->last_activity > $this->options['sess_time_to_update'])
     $this->session->last_activity = time();
  }
  
  // ---------------------------------------------------------------------------
  
  protected function create() {
    $this->session = new Spockz_Session_Model();
    $this->session->id         = sha1($this->randomString(80));
    $this->session->salt       = sha1($this->randomString(80));
    $this->session->ip_address = $this->CI->input->ip_address();
    $this->session->user_agent = $this->user_agent();
    $this->session->userdata   = array();
    $this->session->flashdata  = array();
    $this->session->save();
    
    $this->write_cookie($this->sign( $this->session->id
                                   , $this->session->salt));
  }
  
  // ---------------------------------------------------------------------------
  
  protected function write() {
    $this->session->save();
  }
  
  // ---------------------------------------------------------------------------
  
  protected function write_cookie($aData) {
    return setcookie( $this->options['sess_cookie_name']
                    , $aData
                    , $this->options['sess_expiration'] + time()
                    , $this->options['cookie_path']
                    , $this->options['cookie_domain']
                    );
  }
  
  // ---------------------------------------------------------------------------
  
  protected function remove_cookie($aData) {
    return setcookie( $this->options['sess_cookie_name']
                    , $aData
                    , time() - 691200 
                    , $this->options['cookie_path']
                    , $this->options['cookie_domain']
                    );
  }
  
  // ---------------------------------------------------------------------------
  
  protected function user_agent() {
    return substr($this->CI->input->user_agent(), 0, 200);
  }

  // ---------------------------------------------------------------------------
  
  protected function sign($aMessage, $aSalt) {
    return $aMessage . $this->signature($aMessage, $aSalt);
  }
  
  // ---------------------------------------------------------------------------
  
  protected function signature($aMessage, $aSalt) {
    return sha1($aMessage, $aSalt);
  }
  
  // ---------------------------------------------------------------------------
  
  protected function check($aMessage, $aSalt, $aMirror) {
    return $this->signature($aMessage, $aSalt) === $aMirror;
  }
  
}
?>