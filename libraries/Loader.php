<?php
class Spockz_Loader extends CI_Loader {
  protected $layout;
  protected $controllerFunction;
  protected $controllerName;
  protected $language;

  public function partial($aPartial, $aData=array(), $aLayout=null, $aLanguage=null) {
    $lastItem = '';
    $parts    = array();
    if (strpos($aPartial, '/') !== false) {
      $parts = explode('/', $aPartial);
      
      $lastItem = array_pop($parts);
    }
    else {
      $lastItem = $aPartial;
    }

    if ($lastItem{0} !== '_') {
      $lastItem = '_'.$lastItem;
    }
    array_push($parts, $lastItem);
    
    $this->view(implode('/', $parts), $aData, $aLayout, $aLanguage);
  }

  public function setLayout($aLayout) {
    if (is_dir(APPPATH.'/views/'.$aLayout)) {
      $this->layout = $aLayout;
    }
  }

  /**
   * @param string $aView
   * @param array $aData
   * @param string $aLayout
   * @param string $aLanguage The locale variant of the view file which should be used.
   */
  public function view($aView=null, $aData=array(), $aLayout=null, $aLanguage=null) {
    if (is_null($aView))
      $aView = strtolower($this->controllerName).'/'.strtolower($this->controllerFunction);
    $lLayout = (is_null($aLayout) ? $this->layout : $aLayout);
    $lLanguage = (is_null($aLanguage) ? $this->language : $aLanguage);
    
    $aData += array('viewLayout' => $lLayout, 'viewLanguage' => $lLanguage);
    
    $lViewsPath = APPPATH.'views';   

    $localizedViewPath = $lViewsPath.'/'.(!empty($lLayout) ? $lLayout.DIRECTORY_SEPARATOR : '').$aView.'_'.$lLanguage.EXT;

//    var_dump($lLanguage, $localizedViewPath);
    if (file_exists($localizedViewPath)) {
//      var_dump($lLayout.DIRECTORY_SEPARATOR.$aView.'_'.$lLanguage);
      parent::view($lLayout.DIRECTORY_SEPARATOR.$aView.'_'.$lLanguage, $aData);
    } 
    elseif (file_exists($lViewsPath . '/' . $lLayout.DIRECTORY_SEPARATOR.$aView.EXT)) {
      log_message('warning', sprintf('No localized version of %s could be found for language %s. Using non-localized one.', $aView, $lLanguage));
      parent::view($lLayout.DIRECTORY_SEPARATOR.$aView, $aData);
    }
    else {
      log_message('warning', sprintf('No default version of %s could be found. (Layout:"%s" Language: "%s")', $aView, $lLayout, $lLanguage));
      show_error(sprintf('Unable to load the requested file: "%s"; Layout: "%s"; Language "%s";', $lViewsPath. '/'.$lLayout.'/'.$aView.EXT, $lLanguage, $lLayout));
    }
  }
  
  /**
   * @param string $aControllerName Contains the currently active controller.
   * @param string $aControllerFunction Contains the currently active controller method.
   */
  public function setController($aControllerName, $aControllerFunction) {
    $this->controllerName = $aControllerName;
    $this->controllerFunction = $aControllerFunction;
  }
  
  /**
   * @param string $aLanguage Contains the language (in the form of "en" or "nl"
   *                          (primary language form))
   */
  public function setLanguage($aLanguage) {
    $this->language = $aLanguage;
  }
  
  /* Getters */
  public function getLayout() {
    return $this->layout;
  }
  
  public function getControllerFunction() {
    return $this->controllerFunction;
  }
  
  public function getControllerName() {
    return $this->controllerName;
  }
  
  public function getLanguage() {
    return $this->language;
  }
}
?>