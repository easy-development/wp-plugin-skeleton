<?php

/**
 *
 * Class EasyDevelopmentSkeletonAbstractEntity
 * Optional Function Names, that strictly need to do the specific action and add a notification
 *    - actionDuplicateEntity - Optional Function Name that can be implemented to duplicate an entry
 *    - actionDeleteEntity    - Optional Function Name that can be implemented to delete an entry
 * Optional Function Names, that will implement an page that helps / guides the user on handling the entity
 *    - userSupportViewEntity - Optional Function Name that will display an "View" Page for the user, per entity.
 * Optional Function Names, that will allow entity information changes for certain steps
 *    - prepareAdministrationEntityInformation - Optional change or add entity data before edit page render
 *    - beforeAdministrationEntitySave - Optional to intercept the received data before save ( insert and update )
 *    - afterAdministrationEntitySave  - Optional to intercept the received data after save ( insert and update )
 * Optional Function Names, that will allow custom texts displayed
 *    - getMenuItemName - Optional change the menu item name
 */
abstract class EasyDevelopmentSkeletonAbstractEntity {

  public $wp_db;
  public $_table_prefix;
  /**
   * @var EasyDevelopmentSkeletonDatabaseConnection
   */
  public $databaseConnection;

  public function __construct(EasyDevelopmentSkeletonDatabaseConnection $databaseConnection) {
    global $wpdb;

    $this->wp_db              = $wpdb;
    $this->databaseConnection = $databaseConnection;

    $this->_table_prefix      = $databaseConnection->tablePrefix;
  }

  /**
   * @return string - table name
   */
  abstract public function getTableName();

  /**
   * @return object - administration row information list
   */
  abstract public function getAdministrationListInformation();

  /**
   * @return array - administration field names
   */
  abstract public function getAdministrationFieldNames();

  public function getAbsoluteTableName() {
    return $this->wp_db->base_prefix . $this->_table_prefix . $this->getTableName();
  }

  public function getAll() {
    $sql = 'SELECT * FROM `' . $this->getAbsoluteTableName() . '`';

    $information = $this->wp_db->get_results($sql);

    $information = method_exists($this, 'beforeGetAll') ? $this->beforeGetAll($information) : $information;

    return $information;
  }

  public function getFirstByField($key, $value) {
    $sql = 'SELECT * FROM `' . $this->getAbsoluteTableName() . '` WHERE ' . $key . ' = "' . $value . '" LIMIT 1';

    $information = $this->wp_db->get_results($sql);

    $information = method_exists($this, 'beforeGetAll') ? $this->beforeGetAll($information) : $information;

    return array_shift($information);
  }

  public function getAllByFieldKey($key, $value) {
    $sql = 'SELECT * FROM `' . $this->getAbsoluteTableName() . '` WHERE ' . $key . ' = "' . $value . '"';

    $information = $this->wp_db->get_results($sql);

    $information = method_exists($this, 'beforeGetAll') ? $this->beforeGetAll($information) : $information;

    return $information;
  }

  public function getAllWhere($map) {
    $sql = 'SELECT * FROM `' . $this->getAbsoluteTableName() . '`
                    WHERE ';

    $sql .= $this->databaseConnection->sqlFieldSetRule($map, 'AND');

    $information = $this->wp_db->get_results($sql);

    $information = method_exists($this, 'beforeGetAll') ? $this->beforeGetAll($information) : $information;

    return $information;
  }

  public function getById($id) {
    $sql = 'SELECT * FROM `' . $this->getAbsoluteTableName() . '` WHERE id = ' . intval($id);

    $information = $this->wp_db->get_results($sql);

    $information = method_exists($this, 'beforeGetAll') ? $this->beforeGetAll($information) : $information;

    return !empty($information) ? array_shift($information) : false;
  }

  public function insert($information) {
    $information = method_exists($this, 'beforeInsert') ? $this->beforeInsert($information) : $information;

    if($information == false)
      return false;

    return $this->databaseConnection->insert($this->getAbsoluteTableName(), $information);
  }

  public function update($information, $id) {
    $information = method_exists($this, 'beforeUpdate') ? $this->beforeUpdate($information) : $information;

    if($information == false)
      return false;

    return $this->databaseConnection->update($this->getAbsoluteTableName(), $information, array('id' =>  $id));
  }

  public function delete($id) {
    $this->databaseConnection->delete($this->getAbsoluteTableName(), array('id' =>  $id));
  }

  public function increaseFieldValue($fieldName, $value, $where = array()) {
    $this->databaseConnection->increaseFieldValue(
        $this->getAbsoluteTableName(),
        $fieldName, $value, $where
    );
  }

}