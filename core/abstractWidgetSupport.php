<?php

abstract class EasyDevelopmentSkeletonAbstractWidgetSupport extends WP_Widget {

  private $_defaultWidgetFields = array(
    'title' => array(
        "label" => 'Title',
        "type"  => 'input'
    )
  );

  public function __construct() {
    parent::__construct(
        $this->getWidgetAlias(),
        __( $this->getWidgetName() ),
        array( 'description' => __( $this->getWidgetDescription() ) )
    );
  }

  abstract public function getWidgetAlias();

  abstract public function getWidgetName();

  abstract public function getWidgetDescription();

  abstract public function getWidgetContent($instance);

  abstract public function getWidgetFields();

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }
    echo $this->getWidgetContent($instance);
    echo $args['after_widget'];
  }

  /**
   * @param array $instance
   * @return string|void
   */
  public function form( $instance ) {
    $widgetOptionList = $this->_defaultWidgetFields + $this->getWidgetFields();
    $formContent = '';

    foreach($widgetOptionList as $fieldAlias => $optionInformation) {
      if(!isset($optionInformation['type']))
        continue;

      $formContent .=   '<p>';

      if(isset($optionInformation['label']))
        $formContent .=     '<label for="' . $this->get_field_id( $fieldAlias ) . '">' .
                                __( $optionInformation['label'] . ' :' ) .
                            '</label>';


      if($optionInformation['type'] == 'input') {
        $formContent .=     '<input class="widefat" id="' . $this->get_field_id( $fieldAlias ) . '" ';
        $formContent .=                            'name="' . $this->get_field_name( $fieldAlias ) . '" ';
        $formContent .=                            'type="text" ';
        $formContent .=                            'value="' . (isset($instance[$fieldAlias]) ? $instance[$fieldAlias] : '') . '" ';
        $formContent .=     '/>';
      } else if($optionInformation['type'] == 'select') {
        $formContent .= '<select id="' . $this->get_field_id( $fieldAlias ) . '" ';
        $formContent .=         'class="widefat" ';
        $formContent .=         'name="' . $this->get_field_name( $fieldAlias ) . '">';

        foreach($optionInformation['options'] as $value => $name){
          $formContent .= '<option value="' . $value . '"';

          if(isset($instance[$fieldAlias]) && $value == $instance[$fieldAlias])
            $formContent .= 'selected="selected"';

          $formContent .= '>' . $name . '</option>';
        }

        $formContent .= '</select>';
      }

      $formContent .=   '</p>';
    }

    echo $formContent;
  }

  /**
   * Processing widget options on save
   *
   * @param array $new_instance The new options
   * @param array $old_instance The previous options
   * @return array $instance
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $widgetOptionAliasList = array_merge(array_keys($this->getWidgetFields()), array_keys($this->_defaultWidgetFields));

    foreach($widgetOptionAliasList as $optionAlias)
      $instance[$optionAlias] = ( ! empty( $new_instance[$optionAlias] ) ) ?
                                    $new_instance[$optionAlias] : '';

    return $instance;
  }

}