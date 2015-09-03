<?php

class EasyDevelopmentSkeletonTable {


  public function __construct() {

  }

  public function buildFromInformationArray($informationArray, $headInformationArray = array()) {
    $content  = '';
    $content .= '<table class="table table-bordered">';

    if(!empty($headInformationArray)) {
      $content .=   '<thead>';
      $content .=     $this->_getRowByRowInformationArray($headInformationArray, false, 'th');
      $content .=   '</thead>';
    }

    $content .=   '<tbody>';

    foreach($informationArray as $informationKey => $information)
      $content .= $this->_getRowByRowInformationArray($information, $informationKey);

    $content .=   '</tbody>';
    $content .= '</table>';

    return $content;
  }

  private function _getRowByRowInformationArray($information, $informationKey = false, $trChildElement = 'td') {
    $information = is_object($information) ? get_object_vars($information) : $information;

    if(!is_array($information))
      $information = array($informationKey, $information);

    $content  = '';
    $content .= '<tr>';

    foreach($information as $info)
      $content .= '<' . $trChildElement . '>' . $info . '</' . $trChildElement . '>';

    $content .= '</tr>';

    return $content;
  }

}