<?php
  $entityAdministrationFields = $administrationEntity->getAdministrationFieldNames();
  $entityTableName            = $administrationEntity->getTableName();
  $entityInformation          = $administrationEntity->getByID($_GET['entity-id']);
  $entityListPage             = '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName;

  if(method_exists($administrationEntity, 'prepareAdministrationEntityInformation'))
    $entityInformation = $administrationEntity->prepareAdministrationEntityInformation($entityInformation);
?>

<?php if(!in_array($entityTableName . '_edit', $this->modelBackendRequest->actions)) : ?>
  <h2>
    <?php echo ucwords(str_replace('_', " ", $entityTableName)) . __(" Management - Edit");?>
    <a href="<?php echo '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName?>" class="btn btn-primary right">
      <?php echo __("Back to List");?>
    </a>

    <?php if(method_exists($administrationEntity, 'pagePerformanceOverview')) : ?>
      <a href="<?php echo '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName?>-view&entity-id=<?php echo $entityInformation->id;?>"
         class="btn btn-success right">
        <?php echo __("View Entry");?>
      </a>
    <?php endif;?>

    <div class="clearfix"></div>
  </h2>

  <br/>

  <form class="form form-horizontal component-uff"
        <?php echo isset($entityAdministrationFields['stepBasedForm'])
                      && $entityAdministrationFields['stepBasedForm'] == true
                          ? 'data-step-method="sliding"' : '';
        ?>
        onkeypress="return event.keyCode != 13;"
        method="POST">
    <input type="hidden" name="action" value="<?php echo $entityTableName;?>-edit"/>
    <?php require('_entity-form.php'); ?>
  </form>
<?php else: ?>
  <?php $redirectionPage = '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName;?>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      setTimeout(function(){
        window.location = '<?php echo $entityListPage;?>';
      }, 2000);
    });
  </script>

  <a href="<?php echo $entityListPage;?>" class="btn btn-primary">
    <?php echo __("Skip Waiting");?>
  </a>

<?php endif;?>