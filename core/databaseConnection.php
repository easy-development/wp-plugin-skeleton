<?php

class EasyDevelopmentSkeletonDatabaseConnection {

  public $wp_db;
  public $tablePrefix;

  public function __construct($tablePrefix) {
    $this->tablePrefix = $tablePrefix;

    global $wpdb;

    $this->wp_db = $wpdb;
  }

  /**
   *  Wrap my array
   *  @param array the array you want to wrap
   *  @param string wrapper , default double-quotes(")
   *  @return an array with wrapped strings
   */
  private function _wrapMyArray($array , $wrapper = '"') {
    $new_array = array();
    foreach($array as $k=>$element){
      if(!is_array($element)){
        $new_array[$k] = $wrapper . $element . $wrapper;
      }
    }
    return $new_array;

  }
  /**
   * Implode an array with the key and value pair giving
   * a glue, a separator between pairs and the array
   * to implode.
   * @param string $glue The glue between key and value
   * @param string $separator Separator between pairs
   * @param array $array The array to implode
   * @return string The imploded array
   */
  private function _arrayImplode( $glue, $separator, $array ) {
    if ( ! is_array( $array ) ) return $array;
    $string = array();
    foreach ( $array as $key => $val ) {
      if ( is_array( $val ) )
        $val = implode( ',', $val );
      $string[] = "{$key}{$glue}{$val}";

    }
    return implode( $separator, $string );
  }

  /**
   *  @param string db_name
   *  @param array data
   *  @uses wrap_my_array
   *  @uses array_implode
   */
  public function insert($db_name , $data){
    if(is_array($data) && !empty($data)){
      $keys = array_keys($data);

      $sql = 'INSERT INTO '.$db_name.' ('
          .implode("," , $this->_wrapMyArray($keys , '`'))
          .') VALUES ('
          .implode("," , $this->_wrapMyArray($data))
          .')';
      $this->wp_db->query($sql);
      return true;
    }
    return false;
  }

  /**
   *  @param string db_name
   *  @param array data
   *  @param array/string where
   *  @uses wrap_my_array
   *  @uses array_implode
   */
  public function update($db_name , $data = array() , $where = array()) {
    if(is_array($data) && !empty($data)){
      $sql = 'UPDATE '.$db_name.' SET ';
      $sql .= $this->sqlFieldSetRule($data, ",");

      if(!empty($where))
        $sql .= ' WHERE ' . (is_array($where) ? $this->sqlFieldSetRule($where) : $where);

      $this->wp_db->query($sql);
      return true;
    }
    return false;
  }

  /**
   *  @param string db_name
   *  @param array/string where
   *  @uses wrap_my_array
   *  @uses array_implode
   */
  public function delete($db_name , $where = array()){
    $sql = 'DELETE FROM '.$db_name.' ';

    if(!empty($where))
      $sql .= ' WHERE ' . (is_array($where) ? $this->sqlFieldSetRule($where) : $where);

    $this->wp_db->query($sql);
  }

  public function increaseFieldValue($db_name, $fieldName, $value, $where = array()) {
    $sql = 'UPDATE ' . $db_name .
           ' SET ' . $fieldName . '  = ' . $fieldName . ' + ' . intval($value);

    if(!empty($where))
      $sql .= ' WHERE ' . (is_array($where) ? $this->sqlFieldSetRule($where) : $where);

    $this->wp_db->query($sql);

    return true;
  }

  public function sqlFieldSetRule($fieldSet, $separator = 'AND') {
    $ret = '';

    $fieldNumber = 1;
    $fieldCount  = count($fieldSet);

    foreach($fieldSet as $fieldKey => $fieldValue) {
      $ret .= '`' . $fieldKey . '` = "' . $fieldValue . '"';
      $ret .= ($fieldNumber < $fieldCount ? ' ' . $separator . ' ' : '');

      $fieldNumber++;
    }

    return $ret;
  }

}