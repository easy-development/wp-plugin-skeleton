<?php

class EasyDevelopmentSkeletonBackendRequest {

  public $notifications              = array(
    'errors'        => array(),
    'notifications' => array(),
    'success'       => array(),
    'warnings'      => array()
  );
  public $actions                    = array();
  public $_processRequestComplete    = 0;

  /**
   * @var EasyDevelopmentSkeleton
   */
  public $mainController;

  public function __construct(EasyDevelopmentSkeleton $mainController) {
    $this->mainController = $mainController;
  }

  public function process() {
    if(!is_admin())
      return;

    $this->_init();
  }

  public function _init() {
    $requestParams = array_merge_recursive($_GET, $_POST);
    $entityID      = isset($requestParams['entity-id']) ? intval($requestParams['entity-id']) : 0;

    if(isset($requestParams['action'])) {
      $actionResponse      = null;
      $requestEntity       = substr($requestParams['action'], 0, strpos($requestParams['action'], '-'));
      $requestAction       = str_replace($requestEntity . '-', '', $requestParams['action']);
      $requestEntityObject = (isset($this->mainController->entityManagementList[$requestEntity]) ?
                              $this->mainController->entityManagementList[$requestEntity] : false);
      $formContent         = isset($requestParams[$requestEntity . '_form']) ? $requestParams[$requestEntity . '_form'] : array();

      foreach($formContent as $key => $value) {
        if(isset($formContent[$key . '-resize'])) {
          $formContent[$key] = $this->_imageResizeUtility(
              $value,
              $requestEntityObject,
              $requestEntity,
              json_decode(stripcslashes($formContent[$key . '-resize'])),
              $requestParams
          );

          unset($formContent[$key . '-resize']);
        }
      }

      if($requestEntityObject != false) {
        if($requestAction == 'duplicate' && method_exists($requestEntityObject, 'actionDuplicateEntity')) {
          $actionResponse = $requestEntityObject->actionDuplicateEntity($entityID);
          $this->actions[] = $requestEntityObject->getTableName() . '_duplicate';
        }elseif($requestAction == 'delete' && method_exists($requestEntityObject, 'actionDeleteEntity')) {
          $actionResponse = $requestEntityObject->actionDeleteEntity($entityID);
          $this->actions[] = $requestEntityObject->getTableName() . '_delete';
        }elseif($requestAction == 'add' && $requestEntityObject->getAdministrationFieldNames() != false) {
          if(method_exists($requestEntityObject, 'beforeAdministrationEntitySave'))
            $formContent = $requestEntityObject->beforeAdministrationEntitySave($formContent, $entityID);


          $actionResponse = $requestEntityObject->insert($formContent);
          $this->actions[] = $requestEntityObject->getTableName() . '_add';
          $this->notifications['success'][] = __(
              "Successfully added your new " . ucwords(str_replace("_", " ", $requestEntity))
          );

          if(method_exists($requestEntityObject, 'afterAdministrationEntitySave'))
            $requestEntityObject->afterAdministrationEntitySave($formContent, $entityID);
        }elseif($requestAction == 'edit' && $requestEntityObject->getAdministrationFieldNames() != false) {
          if(method_exists($requestEntityObject, 'beforeAdministrationEntitySave'))
            $formContent = $requestEntityObject->beforeAdministrationEntitySave($formContent, $entityID);

          $actionResponse = $requestEntityObject->update($formContent, $entityID);
          $this->actions[] = $requestEntityObject->getTableName() . '_edit';
          $this->notifications['success'][] = __(
              "Successfully edited your existing " . ucwords(str_replace("_", " ", $requestEntity)) .
              (isset($requestParams[$requestEntity . '_form']['name']) ?
                  '. Known as : ' . $requestParams[$requestEntity . '_form']['name'] :
                  ''
              )
          );

          if(method_exists($requestEntityObject, 'afterAdministrationEntitySave'))
            $requestEntityObject->afterAdministrationEntitySave($formContent, $entityID);
        }
      }

      if($actionResponse !== null) {
        if(is_string($actionResponse))
          $this->notifications['notification'][] = $actionResponse;
        else if(is_array($actionResponse))
          $this->notifications[$actionResponse['type']][] = $actionResponse['content'];

      }
    }
  }

  public function displayRequestNotifications() {
    if(isset($this->notifications['error']))
      foreach($this->notifications['error'] as $error)
        echo '<div class="alert alert-danger">' . $error . '</div>';

    if(isset($this->notifications['notification']))
      foreach($this->notifications['notification'] as $notification)
        echo '<div class="alert alert-info">' . $notification . '</div>';

    if(isset($this->notifications['success']))
      foreach($this->notifications['success'] as $success)
        echo '<div class="alert alert-success">' . $success . '</div>';

    if(isset($this->notifications['warning']))
      foreach($this->notifications['warning'] as $warning)
        echo '<div class="alert alert-warning">' . $warning . '</div>';
  }

  /**
   * @param $currentImage
   * @param $requestEntityObject
   * @param $requestEntity
   * @param $resizeInformation
   * @param $requestParams
   * @return string
   */
  private function _imageResizeUtility(
      $currentImage, $requestEntityObject, $requestEntity, $resizeInformation, $requestParams = array()
  ) {
    $this->registerPluginUploadDirectoryExistence();

    if(strpos($currentImage, $this->getPluginUploadDirectoryURLPath()) === 0) {
      $imagePath = $currentImage;
    } else {
      $fileType = wp_check_filetype($currentImage);

      $imageName = $requestEntityObject->getTableName() . '-' . date("Y-m-d") . (
          isset($requestParams[$requestEntity . '_form']['name']) ?
              sanitize_title($requestParams[$requestEntity . '_form']['name']) :
              time()
          ). '.' . $fileType['ext'];


      file_put_contents(
          $this->getPluginUploadDirectoryFilePath() . $imageName,
          file_get_contents($currentImage)
      );

      $imagePath = $this->getPluginUploadDirectoryURLPath() . $imageName;
    }

    $imageFilePath  = str_replace(
        $this->getPluginUploadDirectoryURLPath(),
        $this->getPluginUploadDirectoryFilePath(),
        $imagePath
    );
    $imageEditor    = wp_get_image_editor( $imageFilePath );

    if ( ! is_wp_error( $imageEditor ) ) {
      $imageEditor->crop(
          $resizeInformation->x,
          $resizeInformation->y,
          $resizeInformation->width,
          $resizeInformation->height
      );

      $imageEditor->save( $imageFilePath );
    }

    return $imagePath;
  }

  public function registerPluginUploadDirectoryExistence() {
    if(!file_exists($this->getPluginUploadDirectoryFilePath()))
      mkdir($this->getPluginUploadDirectoryFilePath(), 0755);
  }

  public function getPluginUploadDirectoryFilePath() {
    $uploadDirectory = wp_upload_dir();

    return $uploadDirectory['basedir'] . DIRECTORY_SEPARATOR . $this->mainController->pluginPrefix . DIRECTORY_SEPARATOR;
  }

  public function getPluginUploadDirectoryURLPath() {
    $uploadDirectory = wp_upload_dir();

    return $uploadDirectory['baseurl'] . '/' . $this->mainController->pluginPrefix . '/';
  }

}