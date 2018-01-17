<?php if (!defined('CIVI_SETUP')): exit("Installation plugins must only be loaded by the installer.\n"); endif; ?>
<h2 id="settings"><?php echo ts('Opt-in'); ?></h2>

<p>
  <?php echo ts('CiviCRM is provided for free -- built with dues, donations, and volunteerism. We don\'t have the marketing or data-analytics prowess of a large corporation, so we rely on users to keep us informed -- and to spread the word.'); ?>
</p>

<p>
  <?php echo ts('Of course, not everyone can help in these ways. But if you can, opt-in to help enrich the product and community.'); ?>
</p>

<div>
  <input type="checkbox" name="civisetup[opt-in][versionCheck]" id="civisetup[opt-in][versionCheck]" value="1" <?php echo $model->extras['opt-in']['versionCheck'] ? 'checked' : ''; ?>>
  <label for="civisetup[opt-in][versionCheck]">
    <?php echo ts('Version pingback'); ?>
  </label>
  <div class="advancedTip">
    <?php echo ts('Checks for CiviCRM version updates. Important for keeping the database secure. Also sends anonymous usage statistics to civicrm.org to to assist in prioritizing ongoing development efforts.'); ?>
  </div>
</div>

<div>
  <input type="checkbox" name="civisetup[opt-in][empoweredBy]" id="civisetup[opt-in][empoweredBy]" value="1" <?php echo $model->extras['opt-in']['empoweredBy'] ? 'checked' : ''; ?>>
  <label for="civisetup[opt-in][empoweredBy]">
    <?php echo ts('Empowered by CiviCRM'); ?>
  </label>
  <div class="advancedTip">
    <?php echo ts('When enabled, "empowered by CiviCRM" is displayed at the bottom of public forms.'); ?>
  </div>
</div>
