<?php
/**
 * This is my own custom controller. It adds support for .extension in the url, and it provides a shortcut to the view function. So that in the children you wont have to type so much.
 *
 */
// require_once 'Library.php';

abstract class Spockz_Controller extends Controller {
  const SessionKey_PreferredLanguage = '__spockz_preferred_language';
  protected
    $cssFiles     = array(),
    $jsFiles      = array(),
    $metaKeyWords = array(),
    $defaults     = array();
    
  /**
   * Contains the error you/the system wants to show the user.
   *
   * @var array
   */
  private
    $errors = array();
    
  private
    $notices = array();
  /**
   * Contains the information about the current user.
   *
   * @var UserHandler
   */
  public $userhandler=null;  
  
  /**
   * Contains the user model. Used for logging in people.
   *
   * @var User_Model
   */
  public $user;

  /**
   * @var Spockz_PageInfo
   */
  public $pageinfo;  
    
  /**
   * Controls startup options.
   *
   */
  function __construct() {
    parent::__construct();
    $this->load->config('spockz');
    
    $this->spockz_pageinfo = new Spockz_PageInfo;
    
    $this->defaults = $this->config->item('defaults', 'spockz');
    if (!empty($this->defaults['cssFiles']) && is_array($this->defaults['cssFiles'])) {
      foreach ($this->defaults['cssFiles'] as $cssFile) {
        $this->spockz_pageinfo->registerCSSFile($cssFile);
      }
    }
    if (!empty($this->defaults['jsFiles']) && is_array($this->defaults['jsFiles'])) {
      foreach ($this->defaults['jsFiles'] as $jsFile) {
        $this->spockz_pageinfo->registerJSFile($jsFile);
      }
    }
    
    date_default_timezone_set('Europe/Amsterdam');
  }
  
  /**
   * This function remaps the url and retrieves the desired extension from it. It then calls the appropiate controller method.
   *
   * @param string $aMethod
   */
  function _remap($aMethod) {
    $lParams = $this->uri->rsegment_array();
    array_shift($lParams);array_shift($lParams);
    
    
    // Determine the layout
    $lMethod = $aMethod; 
    $lLayout = 'xhtml';
    if (strpos($lMethod, '.') !== False) 
      list($lMethod, $lLayout) = explode('.', $lMethod);
    $this->load->setLayout($lLayout);
    
    // Determine the language
    $lLanguages = array($this->input->get('language'), 
                        $this->session->userdata(self::SessionKey_PreferredLanguage), 
                        $this->defaults['language']);
    foreach ($lLanguages as $lLanguage) {
      if ($lLanguage !== false && !empty($lLanguage)) {
        $this->load->setLanguage($lLanguage);
        break;
      }
    }
      
    $lMethod = (empty($lMethod) ? 'index' : $lMethod);
    if (in_array($lMethod, get_class_methods(get_class($this)))) {
      $this->load->setController(get_class($this), $lMethod);
      call_user_func_array(array($this, $lMethod), $lParams);
    } 
    else {
      $this->show_404($lMethod);
    }
  }
  
  /**
   * This function provides a shortcut to the load->view function. It also appends the current layout so you won't get lost.
   *
   * @param string $aView
   */
  protected function view($aView=null, $aData=array(), $aLayout=null, $aLanguage=null) {
    $aData['errors']   = $this->errors;
    if (count($aData['errors']) > 0) $this->spockz_pageinfo->registerCSSFile('errors');
    
    $aData['notices']   = $this->notices;
    if (count($aData['notices']) > 0) $this->spockz_pageinfo->registerCSSFile('notices');
    
    $aData['data']         =& $aData;
    $aData['user']         = $this->userhandler;
    $aData['pageInfo']     =& $this->spockz_pageinfo;
    
    date_default_timezone_set('Europe/Amsterdam'); // todo: move this to config.
    $this->load->view($aView, $aData, $aLayout, $aLanguage);
  }
  
  protected function addError($aError) {
    $this->errors[] = $aError;
  }
  
  protected function addNotice($aNotice) {
    $this->notices[] = $aNotice;
  }
  
  protected function getErrors() {
    return $this->errors;
  }
  
  protected function getNotices() {
    return $this->notices;
  }
  
  protected function hasErrors() {
    return count($this->errors) > 0;
  }
  
  protected function hasNotices() {
    return count($this->notices) > 0;
  }
  
  protected function show_404($aPage=null) {
    show_404($aPage);
  }
}
?>
