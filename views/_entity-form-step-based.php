<div class="uff-form-sliding">
  <div class="sliding-container">
    <?php $currentStep = 1;
          $totalSteps  = count($entityAdministrationFields['steps']);
          foreach($entityAdministrationFields['steps'] as $stepName => $entityAdministrationFieldsStep) : ?>
      <div data-step>
        <h3>
          <?php echo $stepName; ?>

          <ul class="pager right">
            <?php if($currentStep < $totalSteps) : ?>
              <li class="right"><a class="next" data-step-next><?php echo __("Next");?> &raquo;</a></li>
            <?php endif; ?>

            <?php if(isset($entityInformation->id) || $currentStep == $totalSteps) : ?>
              <li class="right"><a class="save" data-step-force data-step-finish><?php echo __("Save");?></a></li>
            <?php endif;?>

            <?php if($currentStep > 1) : ?>
              <li class="right"><a class="prev" data-step-previous>&laquo;  <?php echo __("Previous");?></a></li>
            <?php endif; ?>
          </ul>
          <div class="clearfix"></div>
        </h3>



        <?php foreach($entityAdministrationFieldsStep as $fieldAlias => $fieldInformation) : ?>
          <?php require("_entity-form-input-row.php"); ?>
        <?php endforeach; ?>
      </div>
      <?php $currentStep++;endforeach; ?>
  </div>
</div>
