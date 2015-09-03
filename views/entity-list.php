<?php
  $entityListInformation = $administrationEntity->getAdministrationListInformation();
  $entityTableName       = $administrationEntity->getTableName();
  $entityHasManagement   = ($administrationEntity->getAdministrationFieldNames() != false) ? 1 : 0;
  $entityHasDuplicate    = method_exists($administrationEntity, 'actionDuplicateEntity')   ? 1 : 0;
  $entityHasDelete       = method_exists($administrationEntity, 'actionDeleteEntity')      ? 1 : 0;
  $entityHasView         = method_exists($administrationEntity, 'userSupportViewEntity') ? 1 : 0;
  $entityHasOverview     = method_exists($administrationEntity, 'pagePerformanceOverview') ? 1 : 0;
?>

<h2>
  <?php echo ucwords(str_replace('_', " ", $entityTableName)) . __(" Management - Overview");?>

  <?php if($entityHasManagement || $entityHasOverview) : ?>

  <?php if($entityHasManagement) : ?>
    <a class="btn btn-primary right"
       href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $entityTableName;?>-add">
      <?php echo __('Add New')?>
    </a>
  <?php endif;?>

  <?php if($entityHasOverview) : ?>
    <a class="btn btn-warning right"
       href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $entityTableName;?>-performance">
      <?php echo __('Performance Overview')?>
    </a>
  <?php endif;?>

  <div class="clearfix"></div>

  <?php endif;?>
</h2>

<br/>

<form method="POST" id="<?php echo $this->pluginSlug; ?>-container" onkeypress="return event.keyCode != 13;">
  <?php if(!empty($entityListInformation['body'])) : ?>

    <table class="table table-striped">
      <thead>
        <tr>
          <?php foreach($entityListInformation['head'] as $fieldKey => $fieldName) : ?>
          <th><?php echo $fieldName;?></th>
          <?php endforeach; ?>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($entityListInformation['body'] as $entityRowInformation) : ?>
        <tr class="<?php echo isset($entityRowInformation['options']['rowClass']) ? $entityRowInformation['options']['rowClass'] : ''?> ">
          <?php foreach($entityListInformation['head'] as $fieldKey => $fieldName) : ?>
          <td><?php echo $entityRowInformation['information'][$fieldKey];?></td>
          <?php endforeach; ?>
          <td style="width: <?php echo 55 * ($entityHasManagement + $entityHasDuplicate + $entityHasDelete + $entityHasView);?>px;">
            <?php if($entityHasView) : ?>
            <a class="btn btn-success hint--top"
               data-hint="<?php echo __("View");?>"
               href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $entityTableName;?>-view&entity-id=<?php echo $entityRowInformation['id'];?>">
              <span class="glyphicon glyphicon-eye-open"></span>
            </a>
            <?php endif;?>
            <?php if($entityHasManagement) : ?>
            <a class="btn btn-primary hint--top"
               data-hint="<?php echo __("Edit");?>"
               href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $entityTableName;?>-edit&entity-id=<?php echo $entityRowInformation['id'];?>">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <?php endif;?>
            <?php if($entityHasDuplicate) : ?>
            <a class="btn btn-warning hint--top"
               data-hint="<?php echo __("Duplicate");?>"
               href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $entityTableName;?>&action=<?php echo $entityTableName;?>-duplicate&entity-id=<?php echo $entityRowInformation['id'];?>">
              <span class="glyphicon glyphicon-transfer"></span>
            </a>
            <?php endif;?>
            <?php if($entityHasDelete) : ?>
            <a class="btn btn-danger hint--top"
               data-hint="<?php echo __("Delete");?>"
               href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $entityTableName;?>&action=<?php echo $entityTableName;?>-delete&entity-id=<?php echo $entityRowInformation['id'];?>">
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            <?php endif;?>
          </td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  <?php else : ?>
    <div class="alert alert-info">
      <p><?php echo __('Start by adding your own first ' .
                       ucwords(str_replace("_", " ", $administrationEntity->getTableName())) .
                       '. ' .
                       'Click on the Add New button');?></p>
    </div>
  <?php endif;?>
</form>
