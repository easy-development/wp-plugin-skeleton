<?php
  $entityInformation          = $administrationEntity->getByID($_GET['entity-id']);
  $entityViewMode             = isset($_GET['type']) ? $_GET['type'] : 'list';
  $entityViewInformation      = $administrationEntity->userSupportViewEntity($entityInformation->id, $entityViewMode);
  $entityTableName            = $administrationEntity->getTableName();
  $entityListPage             = '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName;
?>

<h2>
  <?php echo ucwords(str_replace('_', " ", $entityTableName)) .
              __(" Management - View") .
              (isset($entityInformation->name) ? ' : ' . $entityInformation->name : '');?>


  <a href="<?php echo '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName?>" class="btn btn-primary right">
    <?php echo __("Back to List");?>
  </a>

  <?php if($administrationEntity->getAdministrationListInformation() != false) : ?>
  <a href="<?php echo '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName?>-edit&entity-id=<?php echo $entityInformation->id;?>"
     class="btn btn-success right">
    <?php echo __("Edit Entry");?>
  </a>
  <?php endif;?>

  <?php if(method_exists($administrationEntity, 'userSupportViewEntityOptions')) : ?>
  <div class="right list-view-options">
    <?php foreach($administrationEntity->userSupportViewEntityOptions($_GET['entity-id']) as $viewMethodAlias => $viewMethodText) : ?>
      <?php
        $link = ( strpos($viewMethodAlias, '?') !== false ? $viewMethodAlias :
                 '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName . '-view' .
                 '&entity-id=' . $entityInformation->id . '&type=' . $viewMethodAlias);
      ?>
      <a href="<?php echo $link;?>"
         class="btn <?php echo $viewMethodAlias == $entityViewMode ? 'btn-success' : 'btn-default'?>">
        <?php echo __($viewMethodText);?>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif;?>

  <div class="clearfix"></div>
</h2>

<div class="clearfix"></div>

<?php foreach($entityViewInformation as $currentRowColumns) : ?>
  <div class="row">
    <?php $columnType = ceil(12 / count($currentRowColumns)); ?>

    <?php foreach($currentRowColumns as $currentRowColumn) : ?>
      <div class="col-md-<?php echo $columnType;?>">
        <?php if(isset($currentRowColumn['name'])) : ?>
        <h2><?php echo $currentRowColumn['name'];?></h2>
        <?php endif; ?>

        <?php if($currentRowColumn['type'] == 'raw') : ?>
          <?php echo $currentRowColumn['content'];?>
        <?php endif;?>

        <?php if($currentRowColumn['type'] == 'chart') : ?>
          <?php echo $this->modelChart->buildChartByTypeAndInformationArray(
            $currentRowColumn['chartType'], $currentRowColumn['chartInformation'],
            (isset($currentRowColumn['chartLabels']) ? $currentRowColumn['chartLabels'] : array())
          );?>
        <?php endif;?>
        <?php if($currentRowColumn['type'] == 'table') : ?>
          <?php echo $this->modelTable->buildFromInformationArray(
              $currentRowColumn['tableInformation'],
              (isset($currentRowColumn['tableHeadInformation']) ? $currentRowColumn['tableHeadInformation'] : array())
          );?>
        <?php endif;?>
      </div>
    <?php endforeach; ?>
    <div class="clearfix"></div>
  </div>
<?php endforeach;?>