<?php

class EasyDevelopmentSkeletonFormField {

  /**
   * @var EasyDevelopmentSkeleton
   */
  public $mainController;

  public function __construct(EasyDevelopmentSkeleton $mainController) {
    $this->mainController = $mainController;

  }

  /**
   * @param $fieldInformation
   * @param $fieldGroup
   * @param $fieldAlias
   * @param string $fieldValue
   * @return string
   */
  public function formField($fieldInformation, $fieldGroup, $fieldAlias, $fieldValue = '') {
    if($fieldInformation['type'] == 'text')
      return $this->textInput($fieldGroup . '[' . $fieldAlias . ']', $fieldValue, (isset($fieldInformation['placeholder']) ? $fieldInformation['placeholder'] : false));

    if($fieldInformation['type'] == 'textarea')
      return $this->textareaInput($fieldGroup . '[' . $fieldAlias . ']', $fieldValue);

    if($fieldInformation['type'] == 'datetime')
      return $this->textDateTimeInput($fieldGroup . '[' . $fieldAlias . ']', $fieldValue);

    if($fieldInformation['type'] == 'select')
      return $this->selectInput($fieldGroup . '[' . $fieldAlias . ']', $fieldInformation['values'], $fieldValue);

    if($fieldInformation['type'] == 'hiddenOrderHelper')
      return $this->hiddenOrderHelperInput($fieldGroup . '[' . $fieldAlias . ']', $fieldValue);

    if($fieldInformation['type'] == 'wordpressImage')
      return $this->wordpressImageInput(
          $fieldGroup . '[' . $fieldAlias . ']',
          $fieldValue
      );

    if($fieldInformation['type'] == 'imageResize')
      return $this->imageResizeFeatureInput($fieldGroup, $fieldInformation, $fieldAlias);

    if($fieldInformation['type'] == 'html')
      return $fieldInformation['html'];


    if($fieldInformation['type'] == 'table') {
      return $this->tableFormInformation(
          $fieldGroup . '[' . $fieldAlias . ']',
          (isset($fieldInformation['headInformation']) ? $fieldInformation['headInformation'] : false),
          $fieldInformation['information'],
          $fieldValue
      );
    }

    if($fieldInformation['type'] == 'fieldSet') {
      return $this->fieldSetInformation(
          $fieldGroup . '[' . $fieldAlias . ']',
          $fieldInformation['information'],
          $fieldValue,
          (isset($fieldInformation['multipleSetup']) ? $fieldInformation['multipleSetup'] : false),
          (isset($fieldInformation['multipleLabel']) ? $fieldInformation['multipleLabel'] : false),
          (isset($fieldInformation['sortable']) ? $fieldInformation['sortable'] : false)
      );
    }


    return '<div class="alert alert-info">' . __("Field unavailable") . '</div>';
  }

  /**
   * @param $formField
   * @param string $fieldValue
   * @param string|bool $placeholder
   * @return string
   */
  public function textInput($formField, $fieldValue = '', $placeholder = false) {
    $ret = '';

    $ret .= '<input type="text" ' .
                   'class="form-control" ' .
                   'name="' . $formField . '" ' .
                   'value="' . $fieldValue . '" ' .
                   ($placeholder != false ? 'placeholder="' . $placeholder . '" ' : '') .
                   '/>';

    return $ret;
  }

  /**
   * @param $formField
   * @param string $fieldValue
   * @return string
   */
  public function textareaInput($formField, $fieldValue = '') {
    $ret = '';

    $ret .= '<textarea class="form-control" name="' . $formField . '"/>' . $fieldValue . '</textarea>';

    return $ret;
  }

  /**
   * @param $formField
   * @param string $fieldValue
   * @return string
   */
  public function textDateTimeInput($formField, $fieldValue = '') {
    $ret = '';

    $ret .= '<input type="text" ' .
                   'class="form-control easy-development-skeleton-datetime" ' .
                   'name="' . $formField . '" ' .
                   'value="' . $fieldValue . '" ' .
                   '/>';

    return $ret;
  }

  /**
   * @param $formField
   * @param $formOptions
   * @param string $fieldValue
   * @return string
   */
  public function selectInput($formField, $formOptions, $fieldValue = '') {
    $ret = '';

    $ret .= '<select class="form-control" name="' . $formField . '">';

    foreach($formOptions as $fieldOption => $fieldOptionDisplay)
      $ret .= '<option value="' . $fieldOption . '" ' . ($fieldValue === strval($fieldOption) ? 'selected="selected"' : '') . '>' .
                  $fieldOptionDisplay .
              '</option>';

    $ret .= '</select>';

    return $ret;
  }

  public function hiddenOrderHelperInput($formField, $fieldValue = '') {
    return '<input type="hidden" class="hidden-order-helper" name="' . $formField . '" value="' . $fieldValue . '"/>';
  }

  /**
   * @param $formField
   * @param string $fieldValue
   * @return string
   */
  public function wordpressImageInput($formField, $fieldValue = '') {
    $ret = '';

    $ret .= '<span class="btn btn-success"
                   data-ed-wordpress-image-target=' .
                    "'" . '[name="' . $formField . '"]' . "'" . '>' .
                   __("Select Image") .
            '</span>';

    $ret .= '<input type="text" ' .
                   'class="form-control" ' .
                   'name="' . $formField . '" ' .
                   'value="' . $fieldValue . '" ' .
                   '/>';

    return $ret;
  }

  /**
   * @param  $formFieldGroup
   * @param  $fieldInformation
   * @param  $fieldAlias
   * @return string
   */
  public function imageResizeFeatureInput($formFieldGroup, $fieldInformation, $fieldAlias) {
    $ret = '';

    unset($fieldInformation['type']);

    $fieldInformation['target']             = $formFieldGroup .'[' . $fieldInformation['target'] . ']';
    $fieldInformation['dynamicValueTarget'] = $formFieldGroup .'[' . $fieldInformation['dynamicValueTarget'] . ']';

    $ret .= "<div data-image-cropper-utility='" . json_encode($fieldInformation) . "'>
                <input data-image-cropper-response=\"\" type=\"hidden\" name=\"" . $formFieldGroup . "[" . $fieldAlias . "]" . "\"/>
             </div>";

    return $ret;
  }

  /**
   * @param $fieldGroup
   * @param $fieldInformation
   * @param $valueMAP
   * @param bool $multipleFieldSetup
   * @param bool $multipleFieldLabel
   * @param bool $sortableField
   * @return string
   */
  public function fieldSetInformation(
      $fieldGroup,
      $fieldInformation,
      $valueMAP,
      $multipleFieldSetup = false,
      $multipleFieldLabel = false,
      $sortableField      = false
  ) {
    $valueMAP = is_object($valueMAP) ? get_object_vars($valueMAP) : $valueMAP;

    $content = '';

    $content .= '<div class="easy-development-field-set-container" ';
    $content .= '     data-field-key-count="' . $this->_getFieldCount($fieldInformation) . '" ';
    $content .= '     data-multiple-field-setup="' . intval($multipleFieldSetup) . '" ';
    if($multipleFieldLabel != false)
      $content .= '   data-multiple-field-label="' . $multipleFieldLabel . '" ';
    $content .= '     data-multiple-field-sortable="' . intval($sortableField) . '" ';
    $content .= '>';

    if($multipleFieldSetup)
      $content .= $this->_fieldSetInformationMultipleFieldSetup($fieldGroup, $fieldInformation, $valueMAP);
    else
      $content .= $this->_fieldSetInformationDefault($fieldGroup, $fieldInformation, $valueMAP);

    $content .= '</div>';

    return $content;
  }

  private function _getFieldCount($fieldInformationList) {
    $fieldCount = count($fieldInformationList);

    foreach($fieldInformationList as $fieldInformation)
      if($this->_isHiddenField($fieldInformation))
        $fieldCount--;

    return $fieldCount;
  }

  private function _isHiddenField($fieldInformation) {
    return strpos($fieldInformation['type'], 'hidden') === 0;
  }

  private function _fieldSetInformationDefault($fieldGroup, $fieldInformation, $valueMAP) {
    $content = '';

    foreach($fieldInformation as $fieldSetKey => $fieldSetInformation)
      $content .= $this->_fieldSetInformationRow(
          $fieldGroup . "[" . $fieldSetKey . "]",
          $fieldSetInformation,
          (isset($valueMAP[$fieldSetKey]) ? $valueMAP[$fieldSetKey] : array())
      );

    return $content;
  }

  private function _fieldSetInformationMultipleFieldSetup($fieldGroup, $fieldInformation, $valueMAP) {
    $content = '';

    if(is_array($valueMAP) && !empty($valueMAP))
      foreach($valueMAP as $rowKey => $rowInformation)
        $content .= $this->_fieldSetInformationRow(
            $fieldGroup . "[" . $rowKey . "]",
            $fieldInformation,
            $rowInformation
        );

    $content .= $this->_fieldSetInformationRow(
        $fieldGroup . "[new]",
        $fieldInformation,
        array()
    );

    return $content;
  }

  /**
   * @param $fieldGroup
   * @param $rowInformation
   * @param array $currentRowInformation
   * @return string
   */
  private function _fieldSetInformationRow($fieldGroup, $rowInformation, $currentRowInformation = array()) {
    $content  = '<fieldset data-multiple-field-type="' . (empty($currentRowInformation) ? 'add' : 'edit') . '">';

    foreach($rowInformation as $currentFieldAlias => $currentFieldInformation) {
      if($this->_isHiddenField($currentFieldInformation)) {
        $content .= $this->formField($currentFieldInformation,
                                     $fieldGroup,
                                     $currentFieldAlias,
                                     (isset($currentRowInformation[$currentFieldAlias]) ?
                                         $currentRowInformation[$currentFieldAlias]  :
                                         ''
                                     )
                                    );
        continue;
      }

      $content .=  '<div class="form-group">';

      if(isset($currentFieldInformation['label'])) {
        $content .=  '<label class="col-md-3 control-label">';
        $content .=     __($currentFieldInformation['label']) . ' : ';
        $content .=  '</label>';
      }

      $content .=     '<div class="col-md-' . (isset($currentFieldInformation['label']) ? '9' : '12') . '">';

      $content .=     $this->formField($currentFieldInformation,
                                       $fieldGroup,
                                       $currentFieldAlias,
                                       (isset($currentRowInformation[$currentFieldAlias]) ?
                                           $currentRowInformation[$currentFieldAlias]  :
                                           ''
                                       )
                                      );
      $content .=     '</div>';
      $content .= '</div>';
      $content .= '<div class="clearfix"></div>';
    }

    $content .= '</fieldset>';

    return $content;
  }

  /**
   * @param $fieldGroup
   * @param $headInformation
   * @param $fieldInformation
   * @param $valueMAP
   * @return string
   */
  public function tableFormInformation($fieldGroup, $headInformation, $fieldInformation, $valueMAP) {
    $content = '';

    $content .= '<table class="table table-striped easy-development-skeleton-form-table-collection">';

    if($headInformation != false) {
      $content .= '<thead>';

      foreach($headInformation as $headCell)
        $content .= '<td>' . $headCell . '</td>';

      $content .= '</thead>';
    }

    $content .=   '<tbody>';

    foreach($fieldInformation as $fieldRowKey => $fieldRowInformation)
      $content .= $this->_tableInformationRow(
                          $fieldGroup . "[" . $fieldRowKey . "]",
                          $fieldRowInformation,
                          (isset($valueMAP[$fieldRowKey]) ? $valueMAP[$fieldRowKey] : array())
                  );

    $content .=   '</tbody>';
    $content .= '</table>';

    return $content;
  }

  /**
   * @param $fieldGroup
   * @param $rowInformation
   * @param $currentRowInformation
   * @return string
   */
  private function _tableInformationRow($fieldGroup, $rowInformation, $currentRowInformation = array()) {
    $content  = '<tr>';

    foreach($rowInformation as $currentFieldAlias => $currentFieldInformation) {
      $content .= '<td>';
      $content .=   $this->formField($currentFieldInformation,
                                     $fieldGroup,
                                     $currentFieldAlias,
                                     (isset($currentRowInformation[$currentFieldAlias]) ?
                                            $currentRowInformation[$currentFieldAlias]  :
                                            ''
                                     )
                                    );
      $content .= '</td>';
    }

    $content .= '</tr>';

    return $content;
  }

}