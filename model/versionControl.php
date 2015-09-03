<?php

class EasyDevelopmentSkeletonVersionControl {

  public $wpOptionDatabaseVersion = 'database_version';

  /**
   * @var EasyDevelopmentSkeleton
   */
  public $mainController;

  public function __construct(EasyDevelopmentSkeleton $mainController) {
    $this->mainController = $mainController;

    $this->wpOptionDatabaseVersion = $this->mainController->pluginPrefix . '_' . $this->wpOptionDatabaseVersion;

    $this->init();
  }

  public function init() {
    if(get_option($this->wpOptionDatabaseVersion) != $this->mainController->currentDatabaseVersion)
      foreach($this->mainController->databaseVersionMAP as $versionAlias => $versionFile)
        if(floatval($versionAlias) > floatval(get_option($this->wpOptionDatabaseVersion)))
          $this->_updateDatabaseToVersion($versionAlias, $versionFile);

  }

  private function _updateDatabaseToVersion($versionAlias, $versionFile) {
    global $wpdb;

    $query = file_get_contents(
        $this->mainController->pluginFilePath . $versionFile
    );

    if($query == false)
      throw new Exception($this->mainController->pluginName . ', missing DB UPDATE File');

    $query = str_replace(
        $this->mainController->pluginPrefix . '_' ,
        $wpdb->base_prefix . $this->mainController->pluginPrefix . '_',
        $query
    );
    $queries = explode(';', $query);


    foreach($queries as $query)
      if(strlen($query)> 20)
        $response = $wpdb->query($query);

    $this->setCurrentDatabaseVersion($versionAlias);
  }

  public function setCurrentDatabaseVersion($currentVersion) {
    $this->mainController->currentDatabaseVersion = $currentVersion;
    update_option($this->wpOptionDatabaseVersion, $this->mainController->currentDatabaseVersion);
  }

}