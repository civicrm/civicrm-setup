<?php \Civi\Setup::assertRunning(); ?>
<div class="action-box">
  <input id="install_button" type="submit" name="civisetup[action][Install]"
         value="<?php echo htmlentities(ts('Install CiviCRM')); ?>"
         onclick="document.getElementById('saving_top').style.display = ''; this.value = '<?php echo ts('Installing CiviCRM...', array('escape' => 'js')); ?>'"/>
  <span id="saving_top" style="display: none;">
&nbsp;   <img src="<?php echo htmlentities($installURLPath . "network-save.gif") ?>"/>
    <?php echo ts('(this will take a few minutes)'); ?>
</span>
</div>
