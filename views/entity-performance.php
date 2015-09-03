<?php
  $performanceInformation = $administrationEntity->pagePerformanceOverview();
  $entityTableName        = $administrationEntity->getTableName();
?>

<h2>
  <?php echo ucwords(str_replace('_', " ", $entityTableName)) . __(" Performance Overview");?>
  <a href="<?php echo '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName?>" class="btn btn-primary right">
    <?php echo __("View List");?>
  </a>

  <div class="clearfix"></div>
</h2>


<div class="clearfix"></div>

<?php foreach($performanceInformation as $performanceColumns) : ?>
  <div class="row">
    <?php $columnType = ceil(12 / count($performanceColumns)); ?>

    <?php foreach($performanceColumns as $performanceColumn) : ?>
      <div class="col-md-<?php echo $columnType;?>">
        <?php echo BannerEngageController::getInstance()->modelPerformanceStatistics->fromInformationArray(
            $performanceColumn
        );?>
      </div>
    <?php endforeach; ?>
    <div class="clearfix"></div>
  </div>
<?php endforeach;?>