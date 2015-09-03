<?php

/**
 * EasyDevelopmentSkeletonHelperCSV
 *
 * Access EasyDevelopmentSkeletonHelperCSV - internal functions
 *
 * @author Robert
 */
class EasyDevelopmentSkeletonHelperCSV {

  /**
   * @param array $informationArray
   * @param string $filename
   * @return void
   */
  public static function downloadFromArray(array $informationArray, $filename = 'information-export.csv') {
    self::displayCSVDownloadHeaders($filename);
    echo self::getCSVContentFromArray($informationArray);
    exit;
  }

  /**
   * @param $informationArray
   * @return string
   */
  public static function getCSVContentFromArray($informationArray) {
    ob_start();
    $df = fopen("php://output", 'w');

    foreach ($informationArray as $informationRow) {
      fputcsv($df, $informationRow);
    }

    fclose($df);

    return ob_get_clean();
  }

  /**
   * @param string $filename
   * @return void
   */
  public static function displayCSVDownloadHeaders($filename = 'information-export.csv') {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
  }

}
