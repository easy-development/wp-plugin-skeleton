<?php
  if(isset($entityAdministrationFields['stepBasedForm']) && $entityAdministrationFields['stepBasedForm'] == true)
    require("_entity-form-step-based.php");
  else
    require("_entity-form-regular.php");