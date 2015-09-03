<?php
  $entityAdministrationFields = $administrationEntity->getAdministrationFieldNames();
  $entityTableName            = $administrationEntity->getTableName();
  $entityListPage             = '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName;
?>

<?php if(!in_array($entityTableName . '_add', $this->modelBackendRequest->actions)) : ?>
  <h2>
    <?php echo ucwords(str_replace('_', " ", $entityTableName)) . __(" Management - Add");?>
    <a href="<?php echo '?page=' . $this->pluginSlug . '&sub-page=' . $entityTableName?>" class="btn btn-primary right">
      <?php echo __("Back to List");?>
    </a>

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
    <input type="hidden" name="action" value="<?php echo $entityTableName;?>-add"/>
    <?php require('_entity-form.php'); ?>
  </form>
<?php else: ?>
  <?php ?>
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