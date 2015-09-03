<?php

abstract class EasyDevelopmentSkeleton {

  public $pluginName         = 'EasyDevelopment';
  public $pluginSlug         = 'easy-development';
  public $pluginPrefix       = 'easy_development';
  public $pluginActionPrefix = 'easyDevelopment';

  public $pluginPrimaryFile  = 'skeleton.php';

  public $pluginFilePath;
  public $pluginURLPath;

  public $libraryFilePath;
  public $libraryURLPath;

  public $currentDatabaseVersion  = '1.0';
  public $databaseVersionMAP      = array(

  );

  public $networkAdminSupport  = false;

  public $menuItems            = array();
  public $entityManagementList = array();

  /**
   * @var EasyDevelopmentSkeletonDatabaseConnection
   */
  public $databaseConnection;
  /**
   * @var EasyDevelopmentSkeletonVersionControl
   */
  public $modelVersionControl;
  /**
   * @var EasyDevelopmentSkeletonChart
   */
  public $modelChart;
  /**
   * @var EasyDevelopmentSkeletonTable
   */
  public $modelTable;
  /**
   * @var EasyDevelopmentSkeletonPerformanceStatistics
   */
  public $modelPerformanceStatistics;
  /**
   * @var EasyDevelopmentSkeletonFormField
   */
  public $modelFormField;
  /**
   * @var EasyDevelopmentSkeletonUsageGoals
   */
  public $modelUsageGoals;
  /**
   * @var EasyDevelopmentSkeletonBackendRequest
   */
  public $modelBackendRequest;

  final public function __construct($currentFile = null) {
    $this->_setDependencies($currentFile);

    add_action( "widgets_init", array($this, "_widgetsInitHook"));
    add_action( "init", array($this, "_initHook"));
    add_action( 'admin_menu', array( $this, '_menuHook' ) );

    if( $this->networkAdminSupport )
      add_action( 'network_admin_menu', array( $this, '_menuHook' ) );

    add_action( 'admin_enqueue_scripts', array($this, '_assetsHook') );
  }

  private function _setDependencies($currentFile) {
    $currentFile = ($currentFile == null ? dirname(__FILE__) : $currentFile);

    $this->pluginFilePath = dirname($currentFile) . DIRECTORY_SEPARATOR;
    $this->pluginURLPath  = plugin_dir_url($currentFile);

    $this->libraryFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    $this->libraryURLPath  = plugin_dir_url(__FILE__);
  }

  final public function _widgetsInitHook() {
    if(!class_exists('EasyDevelopmentSkeletonAbstractWidgetSupport'))
      require_once($this->libraryFilePath . 'core/abstractWidgetSupport.php');

    if(method_exists($this, '_registerWidgets'))
      $this->_registerWidgets();
  }

  final public function _initHook() {
    $this->_setLibraryDependencies();
    $this->initHook();
  }

  private function _setLibraryDependencies() {
    if(!class_exists('EasyDevelopmentSkeletonHelperArray'))
      require_once($this->libraryFilePath . 'helpers/array.php');
    if(!class_exists('EasyDevelopmentSkeletonHelperCSV'))
      require_once($this->libraryFilePath . 'helpers/csv.php');
    if(!class_exists('EasyDevelopmentSkeletonDatabaseConnection'))
      require_once($this->libraryFilePath . 'core/databaseConnection.php');
    if(!class_exists('EasyDevelopmentSkeletonAbstractEntity'))
      require_once($this->libraryFilePath . 'core/abstractEntity.php');
    if(!class_exists('EasyDevelopmentSkeletonBackendRequest'))
      require_once($this->libraryFilePath . 'model/backendRequest.php');
    if(!class_exists('EasyDevelopmentSkeletonChart'))
      require_once($this->libraryFilePath . 'model/chart.php');
    if(!class_exists('EasyDevelopmentSkeletonTable'))
      require_once($this->libraryFilePath . 'model/table.php');
    if(!class_exists('EasyDevelopmentSkeletonPerformanceStatistics'))
      require_once($this->libraryFilePath . 'model/performanceStatistics.php');
    if(!class_exists('EasyDevelopmentSkeletonFormField'))
      require_once($this->libraryFilePath . 'model/formField.php');
    if(!class_exists('EasyDevelopmentSkeletonVersionControl'))
      require_once($this->libraryFilePath . 'model/versionControl.php');
    if(!class_exists('EasyDevelopmentSkeletonUsageGoals'))
      require_once($this->libraryFilePath . 'model/usageGoals.php');

    $this->modelBackendRequest        = new EasyDevelopmentSkeletonBackendRequest($this);
    $this->modelChart                 = new EasyDevelopmentSkeletonChart();
    $this->modelTable                 = new EasyDevelopmentSkeletonTable();
    $this->modelPerformanceStatistics = new EasyDevelopmentSkeletonPerformanceStatistics($this);
    $this->modelVersionControl        = new EasyDevelopmentSkeletonVersionControl($this);
    $this->modelFormField             = new EasyDevelopmentSkeletonFormField($this);
    $this->modelUsageGoals            = new EasyDevelopmentSkeletonUsageGoals($this);
    $this->databaseConnection         = new EasyDevelopmentSkeletonDatabaseConnection($this->pluginPrefix . '_');

    $this->setPluginEntities();
  }

  abstract public function setPluginEntities();

  abstract public function initHook();

  private function _addEntityAdministration() {
    foreach($this as $classKey => $classValue)
      if($classValue instanceof EasyDevelopmentSkeletonAbstractEntity)
        if($classValue->getAdministrationListInformation() != false)
          $this->entityManagementList[$classValue->getTableName()] = $classValue;

    foreach($this->entityManagementList as $entityObject)
      $this->menuItems[$entityObject->getTableName()] =
          (method_exists($entityObject, 'getMenuItemName') ?
            $entityObject->getMenuItemName() :
            ucwords(str_replace("_", " ", $entityObject->getTableName())) . 's'
          );

  }

  final public function displayAdministration() {
    $this->_addEntityAdministration();

    if(method_exists($this, 'beforeBackendRequestProcess'))
      $this->beforeBackendRequestProcess();

    $this->modelBackendRequest->process();

    echo '<div class="bootstrap_environment">';

    if(file_exists($this->libraryFilePath . 'views/header.php'))
      require($this->libraryFilePath . 'views/header.php');

    $pageDelivery = false;

    $subPageParam  = isset($_GET['sub-page']) ? $_GET['sub-page'] : '';
    $entityName    = str_replace(array('-add', '-edit', '-performance', '-view'), '', $subPageParam);

    if(file_exists($this->pluginFilePath . 'views/' . $subPageParam . '.php')) {
      require($this->pluginFilePath . 'views/' . $subPageParam . '.php');
      $pageDelivery = true;
    } else if(isset($this->entityManagementList[$entityName])) {
      $administrationEntity = $this->entityManagementList[$entityName];

      if($subPageParam == $entityName) {
        require($this->libraryFilePath . 'views/entity-list.php');
        $pageDelivery = true;
      }

      if($subPageParam == $entityName . '-add') {
        require($this->libraryFilePath . 'views/entity-add.php');
        $pageDelivery = true;
      }

      if($subPageParam == $entityName . '-edit') {
        require($this->libraryFilePath . 'views/entity-edit.php');
        $pageDelivery = true;
      }

      if($subPageParam == $entityName . '-view') {
        require($this->libraryFilePath . 'views/entity-view.php');
        $pageDelivery = true;
      }

      if($subPageParam == $entityName . '-performance') {
        require($this->libraryFilePath . 'views/entity-performance.php');
        $pageDelivery = true;
      }

    }

    if($pageDelivery == false
        && file_exists($this->pluginFilePath . 'views/index.php'))
      require($this->pluginFilePath . 'views/index.php');

    echo '</div>';
  }

  final public function _menuHook() {
    add_menu_page(
        $this->pluginName,
        $this->pluginName,
        'manage_options',
        $this->pluginSlug,
        array(
            $this, 'displayAdministration'
        ),
        $this->pluginURLPath . 'assets/icon.png'
    );
  }

  final public function _assetsHook($hook) {
    if(!(isset($_GET['page']) && $_GET['page'] == $this->pluginSlug))
      return;


    wp_enqueue_style( 'bootstrap', plugins_url('assets/admin-style-bootstrap.css', __FILE__) );
    wp_enqueue_style( $this->pluginSlug . '-admin', plugins_url('assets/admin-style.css', __FILE__) );

    wp_enqueue_style( 'bootstrap-datetimepicker', plugins_url('assets/bootstrap-datetimepicker.min.css', __FILE__) );

    wp_enqueue_style( 'hint.css', plugins_url('assets/hint.css', __FILE__ ));
    wp_enqueue_style('thickbox');

    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script(
        'bootstrap',
        plugins_url('assets/bootstrap.min.js', __FILE__ ),
        array( 'jquery'), false, true
    );
    wp_enqueue_script(
        'sortable',
        plugins_url('assets/sortable.min.js', __FILE__ ),
        array(), false, true
    );
    wp_enqueue_script(
        'sortable-jquery',
        plugins_url('assets/jquery.sortable.js', __FILE__ ),
        array('sortable'), false, true
    );
    wp_enqueue_script(
        'moment',
        plugins_url('assets/moment.js', __FILE__ ),
        array(), false, true
    );
    wp_enqueue_script(
        'bootstrap-datetimepicker',
        plugins_url('assets/bootstrap-datetimepicker.min.js', __FILE__ ),
        array( 'jquery', 'moment', 'bootstrap'), false, true
    );
    wp_enqueue_script(
        'chart.js',
        plugins_url('assets/Chart.min.js', __FILE__ ),
        array( 'jquery'), false, true
    );
    wp_enqueue_script(
        $this->pluginSlug . '-backend.js',
        plugins_url('assets/backend.js', __FILE__ ),
        array(
            'jquery', 'thickbox', 'zClip', 'media-upload', 'jquery-ultimate-fancy-form',
            'int_cropper', 'bootstrap-datetimepicker', 'sortable-jquery'
        ), false, true
    );

    wp_localize_script(  $this->pluginSlug . '-backend.js', 'AppHelper', array(
        'zeroClipboardSWF'       => plugins_url('assets/ZeroClipboard.swf', __FILE__ )
    ));

    wp_enqueue_style( 'jquery-ultimate-fancy-form', plugins_url('assets/uff/jquery-ultimate-fancy-form.css', __FILE__ ));
    wp_enqueue_script(
        'jquery-ultimate-fancy-form',
        plugins_url('assets/uff/jquery-ultimate-fancy-form.js', __FILE__ ),
        array( 'jquery'), false, true
    );

    wp_enqueue_script(
        'zClip',
        plugins_url('assets/zClip.js', __FILE__ ),
        array("jquery"), false, true
    );

    wp_enqueue_style( 'int_cropper', plugins_url('assets/cropper.css', __FILE__ ));
    wp_enqueue_script(
        'int_cropper',
        plugins_url('assets/cropper.js', __FILE__ ),
        array( 'jquery'), false, true
    );
  }

}