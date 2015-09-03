<?php foreach($entityAdministrationFields as $fieldAlias => $fieldInformation) : ?>
  <?php require("_entity-form-input-row.php"); ?>
<?php endforeach; ?>

<div class="col-sm-9 col-sm-offset-3">
  <input type="submit" id="activate"
         value="<?php echo __("Save Information");?>"
         class="btn btn-success" />

  <?php if(isset($entityListPage)) : ?>
    <a href="<?php echo $entityListPage;?>" class="btn btn-danger">
      <?php echo __("Cancel");?>
    </a>
  <?php endif;?>
</div>