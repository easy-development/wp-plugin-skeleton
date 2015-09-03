<?php if(isset($fieldInformation['separator'])) : ?>
  <?php if($fieldInformation['separator'] === true) : ?>
    <hr/>
  <?php else : ?>
    <h3 class="separator"><?php echo $fieldInformation['separator'];?></h3>
  <?php endif; ?>
<?php endif;?>
<div class="form-group">
  <?php if(isset($fieldInformation['label'])) : ?>
  <label class="col-sm-3 control-label">
    <?php echo __($fieldInformation['label']);?> :
  </label>
  <?php endif; ?>
  <div class="col-sm-<?php echo isset($fieldInformation['label']) ? '9' : '12'; ?>">
    <?php echo $this->modelFormField->formField(
        $fieldInformation,
        $entityTableName . '_form',
        $fieldAlias,
        (isset($entityInformation->$fieldAlias) ? $entityInformation->$fieldAlias : ''),
        (isset($entityInformation->id) ? $entityInformation->id : false)
    );?>
  </div>
</div>
<div class="clearfix"></div>