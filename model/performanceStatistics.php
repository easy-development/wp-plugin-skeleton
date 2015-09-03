<?php

class EasyDevelopmentSkeletonPerformanceStatistics {

  /**
   * @var EasyDevelopmentSkeleton
   */
  public $mainController;

  public function __construct(EasyDevelopmentSkeleton $mainController) {
    $this->mainController = $mainController;
  }

  /**
   * @param $informationArray
   * @return string
   */
  public function fromInformationArray($informationArray) {
    $content = '';

    if(isset($informationArray['name']))
      $content .= '<h2>' . $informationArray['name'] . '</h2>';

    if(isset($informationArray['type'])) {
      if($informationArray['type'] == 'chart')
        $content .= $this->mainController->modelChart->buildChartByTypeAndInformationArray(
            $informationArray['chartType'],
            $informationArray['chartInformation'],
            (isset($informationArray['chartLabels']) ? $informationArray['chartLabels'] : false),
            (isset($informationArray['percentage']) ? $informationArray['percentage'] : false)
        );
    }

    return $content;
  }

}