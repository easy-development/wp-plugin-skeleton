<?php

class EasyDevelopmentSkeletonUsageGoals {

  /**
   * @var EasyDevelopmentSkeleton
   */
  public $mainController;
  public $availableAccomplishmentRanks = array(
    'Private', 'Corporal', 'Sergeant', 'Master Sergeant', 'Sergeant Major',
    'Knight', 'Knight-Lieutenant', 'Knight-Captain'
  );

  public function __construct(EasyDevelopmentSkeleton $mainController) {
    $this->mainController = $mainController;
  }

  /**
   * @param $informationArray
   * @param $accomplishmentRanks
   * @return string
   */
  public function displayFromInformationArray($informationArray, $accomplishmentRanks = array(), $customSkin = false) {
    $accomplishmentRanks = empty($accomplishmentRanks) ? $this->availableAccomplishmentRanks : $accomplishmentRanks;

    $content  = '<ul class="usage-goal-list' . ($customSkin != false ? '-' . $customSkin : '') . '">';

    foreach($this->_getInformationArrayMAP($informationArray, $accomplishmentRanks) as $informationArray) {
      $content .= implode("", $informationArray);
    }

    $content .= '</ul>';

    return $content;
  }

  /**
   * @param $informationArray
   * @param $accomplishmentRanks
   * @return array
   */
  private function _getInformationArrayMAP($informationArray, $accomplishmentRanks) {
    $contentMAP = array(
      'completed' => array(),
      'available' => array()
    );

    foreach($informationArray as $currentInformationSet) {
      $name        = $currentInformationSet['name'];
      $description = $currentInformationSet['description'];
      $completed   = (isset($currentInformationSet['completed']) ? $currentInformationSet['completed'] : 0);

      if(!isset($currentInformationSet['options'])) {
        $contentMAP[
        ($completed == true ? 'completed' : 'available')
        ][] .= $this->_getUsageGoalLine(
            $name,
            $description,
            $completed,
            false,
            (
            $completed
                ? ''
                : (isset($currentInformationSet['link']) ? (
                '<p class="usage-goal-action">' .
                '<a href="' . $currentInformationSet['link'] . '">' . __("Start Now") .  '</a>' .
                '</p>'
            ) : '')
            )
        );
      } else {
        $designatedAccomplishmentRanks = isset($currentInformationSet['optionRanks']) ?
            $currentInformationSet['optionRanks'] : $accomplishmentRanks;

        $contentMAP = $this->_getUsageGoalLineWithOptions(
            $contentMAP, $name, $description, $currentInformationSet, $designatedAccomplishmentRanks
        );
      }

    }

    return $contentMAP;
  }

  /**
   * @param $contentMAP
   * @param $name
   * @param $description
   * @param $currentInformationSet
   * @param $accomplishmentRanks
   * @return array
   */
  private function _getUsageGoalLineWithOptions(
      $contentMAP, $name, $description, $currentInformationSet, $accomplishmentRanks
  ) {
    $currentProgress   = $currentInformationSet['progress'];
    $currentCap        = -1;
    $currentPointIndex = -1;
    $nextCap           = -1;
    $nextPointIndex    = -1;
    $currentPointImage = false;
    $nextPointImage    = false;

    $i = 0;
    foreach($currentInformationSet['options'] as $optionInformation) {
      $option = is_array($optionInformation) ? $optionInformation['value'] : $optionInformation;

      if($option <= $currentProgress) {
        $currentCap        = $option;
        $currentPointIndex = $i;
        $currentPointImage = (is_array($optionInformation['icon']) && isset($optionInformation['icon'])
                                ? $optionInformation['icon'] : false);
      } else if($i == $currentPointIndex + 1) {
        $nextCap        = $option;
        $nextPointIndex = $i;
        $nextPointImage = (is_array($optionInformation['icon']) && isset($optionInformation['icon'])
                              ? $optionInformation['icon'] : false);
      }

      $i++;
    }

    if($currentPointIndex != -1) {
      $contentMAP['completed'][] = $this->_getUsageGoalLine(
          str_replace("%s", $accomplishmentRanks[$currentPointIndex], $name),
          str_replace("%s", $currentCap, $description),
          true,
          false,
          '',
          $currentPointImage
      );
    }

    if($nextCap > $currentCap) {
      $progressPercent = intval(($currentProgress - $currentCap) / ($nextCap - $currentCap) * 100);

      $contentMAP['available'][] = $this->_getUsageGoalLine(
                      str_replace("%s", $accomplishmentRanks[$nextPointIndex], $name),
                      str_replace("%s", $nextCap, $description),
                      false,
                      true,
                      '<p class="usage-goal-progress">' .
                       '<span class="usage-goal-progress-bar" style="width:' . $progressPercent . '%;"></span>' .
                       '<span class="usage-goal-status">' .
                         $currentProgress . ' / ' . $nextCap .
                       '</span>' .
                      '</p>',
                      $nextPointImage
                  );
    }

    return $contentMAP;
  }

  /**
   * @param $name
   * @param $description
   * @param bool $completed
   * @param bool $progress
   * @param string $complementaryContent
   * @return string
   */
  private function _getUsageGoalLine(
      $name, $description, $completed = false, $progress = false, $complementaryContent = ''
  ) {
    $content = '';
    $content .= '<li class="' . ($progress ? 'progress-usage-goal' : ($completed ? 'completed-usage-goal' : '')) . '">';
    $content .=   '<span class="usage-icon"></span>';
    $content .=   '<h4>' . $name . '</h4>';
    $content .=   '<p>'  . $description . '</p>';
    $content .=   $complementaryContent;
    $content .=   '<div class="clearfix"></div>';
    $content .= '</li>';

    return $content;
  }

}