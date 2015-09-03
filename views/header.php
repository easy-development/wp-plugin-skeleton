<div class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="?page=<?php echo $this->pluginSlug;?>">
      <img src="<?php echo $this->pluginURLPath . 'assets/logo.png';?>"
           />
    </a>
  </div>
  <div class="collapse navbar-collapse">
    <ul class="nav navbar-nav">
      <li style="margin-bottom: 0" class="<?php echo !(isset($_GET['sub-page'])) ? 'active' : ''?>">
        <a href="?page=<?php echo $this->pluginSlug;?>">
          <?php echo __('Overview')?>
        </a>
      </li>
      <?php foreach($this->menuItems as $menuItemAlias => $menuItemName) : ?>
      <li style="margin-bottom: 0" class="<?php echo (isset($_GET['sub-page']) && $_GET['sub-page'] == $menuItemAlias) ? 'active' : ''?>">
        <a href="?page=<?php echo $this->pluginSlug;?>&sub-page=<?php echo $menuItemAlias;?>">
          <?php echo __($menuItemName)?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div><!--/.nav-collapse -->
</div>

<?php $this->modelBackendRequest->displayRequestNotifications(); ?>