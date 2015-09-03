<?php

class EasyDevelopmentSkeletonChart {

  public $statisticsColors = array(
      array( "default" => "#8ec45a", "hover" => "#a6d37a" ),
      array( "default" => "#34a1da", "hover" => "#50b3e7" ),
      array( "default" => "#e4902e", "hover" => "#eaa049" ),
      array( "default" => "#e4322e", "hover" => "#f04a47" ),
      array( "default" => "#cb49b1", "hover" => "#e264c9" ),
      array( "default" => "#9949cb", "hover" => "#ab5bdc" ),
      array( "default" => "#49cb7a", "hover" => "#68df95" ),
      array( "default" => "#3641d6", "hover" => "#515ce6" ),
      array( "default" => "#3a9b48", "hover" => "#4eaf5c" ),
      array( "default" => "#01cbd8", "hover" => "#4ad7e0" ),
      array( "default" => "#fc4349", "hover" => "#fd5c62" ),
      array( "default" => "#047878", "hover" => "#138787" ),
      array( "default" => "#f23c55", "hover" => "#ef465d" ),
      array( "default" => "#4192d9", "hover" => "#5299d8" ),
      array( "default" => "#f27649", "hover" => "#ef825a" ),
      array( "default" => "#154659", "hover" => "#235b70" ),
      array( "default" => "#59535e", "hover" => "#66606b" ),
      array( "default" => "#a12858", "hover" => "#b03c6a" ),
      array( "default" => "#ffc000", "hover" => "#feca53" ),
      array( "default" => "#097609", "hover" => "#218f21" )
  );

  public $chartTypeLine      = 'Line';
  public $chartTypeBar       = 'Bar';
  public $chartTypeRadar     = 'Radar';
  public $chartTypePolarArea = 'PolarArea';
  public $chartTypePie       = 'Pie';

  private $_chartIDPrefix      = 'easy-development-chart-';
  private $_currentChartNumber = 1;
  private $_activeCharts       = 0;

  public function __construct() {

  }

  /**
   * @param $type
   * @param array $information
   * @param array|bool $labels
   * @param bool  $percentage
   * @return string
   */
  public function buildChartByTypeAndInformationArray($type, $information, $labels = false, $percentage = false) {
    $content = '';

    $content .= $this->_buildChartCanvasContainer();
    $content .= $this->_buildChartJavaScriptInformation($type, $labels, $information, $percentage);

    $this->_registerChartDelivery();

    return $content;
  }

  /**
   * @return string
   */
  private function _buildChartCanvasContainer() {
    return  '<div id="' . $this->_chartIDPrefix . $this->_currentChartNumber . '-legend" class="easy-development-skeleton-chart-legend"></div>' .
            '<canvas id="' . $this->_chartIDPrefix . $this->_currentChartNumber . '" height="400px"></canvas>';
  }

  /**
   * @param $type
   * @param $labels
   * @param $information
   * @param bool $percentage
   * @return string
   */
  private function _buildChartJavaScriptInformation($type, $labels, $information, $percentage = false) {
    $currentChartID       = $this->_chartIDPrefix . $this->_currentChartNumber;
    $currentChartNiceName = str_replace('-', '_', $currentChartID);

    $legendJavascript = 'document.getElementById("' . $currentChartID . '-legend").innerHTML = ' .
                                         $currentChartNiceName . 'ChartObject.generateLegend();';

    $content = '';

    $content .= '<script type="text/javascript">
                  jQuery(document).ready(function(){
                    var ' . $currentChartNiceName . ' = jQuery("#' . $currentChartID . '");
                    ' . $currentChartNiceName . '.attr("width", ' . $currentChartNiceName . '.parent().width());
                    ' .
                      (
                        $type == $this->chartTypePie ?
                          $currentChartNiceName . '.attr("height", ' . $currentChartNiceName . '.parent().width());'
                          : ''
                      ) .
                    '

                    var ' . $currentChartNiceName . '2DContext = document.getElementById("' . $currentChartID . '")
                                                                .getContext("2d");
                    var ' . $currentChartNiceName . 'Data      = ' . $this->_buildChartData($type, $labels, $information). ';
                    var ' . $currentChartNiceName . 'ChartObject = new Chart(' . $currentChartNiceName . '2DContext)
                              .' . $type . '(' . $currentChartNiceName . 'Data, ' . $this->_buildChartOptions($type, $percentage) . ');

                    ' . $legendJavascript . '
                  });
                 </script>';

    return $content;
  }

  /**
   * @param $type
   * @param $labels
   * @param $information
   * @return string
   */
  private function _buildChartData($type, $labels, $information) {
    if($type == $this->chartTypeLine)
      return $this->_buildLineChartData($labels, $information);
    if($type == $this->chartTypePie)
      return $this->_buildPieChartData($information);
    if($type == $this->chartTypePolarArea)
      return $this->_buildPieChartData($information);
    if($type == $this->chartTypeRadar)
      return $this->_buildLineChartData($labels, $information);

    return '{}';
  }

  private function _buildLineChartData($labels, $information) {
    $content  = '{';
    $content .=   'labels: ["' . implode('", "', $labels) . '"],' . "\n";
    $content .=   'datasets: [' . "\n";

    $totalEntries = count($information);
    $i = 0;foreach($information as $currentInformation) {
      $currentColor = $this->_hex2rgb($this->statisticsColors[$i]['default']);

      $content .= '{' . "\n";
      $content .=   'label                 : "' .$currentInformation['label'] . '"' . ',' . "\n";
      $content .=   'fillColor             : "rgba(' . $currentColor . ',0.3)"' . ',' . "\n";
      $content .=   'strokeColor           : "rgba(' . $currentColor . ',1)"' . ',' . "\n";
      $content .=   'pointColor            : "rgba(' . $currentColor . ',1)"' . ',' . "\n";
      $content .=   'pointStrokeColor      : "#ffffff"' . ',' . "\n";
      $content .=   'pointHighlightFill    : "#ffffff"' . ','  . "\n";
      $content .=   'pointHighlightStroke  : "rgba(' . $currentColor . ',1)"' . ',' . "\n";
      $content .=   'data                  : ["' . implode('", "', $currentInformation['data']) . '"]' . "\n";
      $content .= '}' . ($totalEntries != $i + 1? ',' : '') . "\n";

      $i++;
    }

    $content .=  ']';
    $content .= '}';

    return $content;
  }

  private function _buildPieChartData($information) {
    $totalEntries = count($information);
    $content  = '[';
    $i = 0;foreach($information as $currentLabel => $currentValue) {
      $currentColor = $this->statisticsColors[$i];

      $content .= '{' . "\n";
      $content .=   'value     : ' . $currentValue . ',' . "\n";
      $content .=   'color     : "' . $currentColor['default']. '"' . ',' . "\n";
      $content .=   'highlight : "' . $currentColor['hover']. '"' . ','  . "\n";
      $content .=   'label     : "' . $currentLabel . '"' . "\n";
      $content .= '}' . ($totalEntries != $i + 1 ? ',' : '') . "\n";

      $i++;
    }

    $content .=  ']';

    return $content;
  }

  /**
   * @param string $chartType
   * @param bool $percentage
   * @return string
   */
  private function _buildChartOptions($chartType, $percentage = false) {
    if($chartType == $this->chartTypeLine)
      return "{
                legendTemplate : '<% for (var i=0; i<datasets.length; i++) { %>' +
                                    '<span style=\"background-color:<%= datasets[i].strokeColor %>\">' +
                                    '<% if (datasets[i].label) { %><%= datasets[i].label %><% } %>' +
                                    '</span>' +
                                 '<% } %>',
                multiTooltipTemplate: \"" . (
                                              $percentage ?
                                                  "<%=datasetLabel%> : <%= value %>%" :
                                                  "<%=datasetLabel%> : <%= parseFloat(value).toLocaleString('en-US') %>"
                                            ) .
                                     "\"
              }
              ";

    if($chartType == $this->chartTypeRadar)
      return "{}";

    return "{
              legendTemplate : '<% for (var i=0; i<segments.length; i++) { %>' +
                                  '<span style=\"background-color:<%= segments[i].fillColor %>\">' +
                                  '<% if (segments[i].label) { %><%= segments[i].label %><% } %>' +
                                  '</span>' +
                               '<% } %>',
            }";
  }

  private function _registerChartDelivery() {
    $this->_activeCharts++;
    $this->_currentChartNumber++;
  }

  private function _hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);

    return implode(",", $rgb); // returns the rgb values separated by commas
  }

}